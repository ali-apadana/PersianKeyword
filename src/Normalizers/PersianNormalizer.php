<?php

declare(strict_types=1);

namespace PersianKeyword\Normalizers;

use PersianKeyword\Contracts\TextNormalizer;

final class PersianNormalizer implements TextNormalizer
{
    /** @var array{decode_html: bool, strip_html: bool, convert_arabic_characters: bool, convert_digits: bool, remove_diacritics: bool, collapse_whitespace: bool} */
    private array $options;

    /**
     * @param array<string, bool> $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'decode_html' => true,
            'strip_html' => true,
            'convert_arabic_characters' => true,
            'convert_digits' => true,
            'remove_diacritics' => true,
            'collapse_whitespace' => true,
        ], $options);
    }

    public function normalize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $normalized = $text;

        if ($this->options['decode_html']) {
            $normalized = html_entity_decode($normalized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if ($this->options['strip_html']) {
            // Replace tags with a space so adjacent paragraphs do not become one word.
            $normalized = preg_replace('/<[^>]+>/u', ' ', $normalized) ?? $normalized;
        }

        $normalized = strtr($normalized, [
            "\u{0640}" => '', // Arabic tatweel
            "\u{00A0}" => ' ', // non-breaking space
            "\u{200E}" => '', // left-to-right mark
            "\u{200F}" => '', // right-to-left mark
            "\u{FEFF}" => '', // byte order mark
        ]);

        if ($this->options['convert_arabic_characters']) {
            $normalized = strtr($normalized, [
                'ى' => 'ی',
                'ي' => 'ی',
                'ك' => 'ک',
                'ة' => 'ه',
                'ۀ' => 'هٔ',
            ]);
        }

        if ($this->options['convert_digits']) {
            $normalized = strtr($normalized, [
                '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
                '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
                '٠' => '۰', '١' => '۱', '٢' => '۲', '٣' => '۳', '٤' => '۴',
                '٥' => '۵', '٦' => '۶', '٧' => '۷', '٨' => '۸', '٩' => '۹',
            ]);
        }

        if ($this->options['remove_diacritics']) {
            $normalized = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $normalized) ?? $normalized;
        }

        // Preserve the Persian half-space while removing spaces around it.
        $normalized = preg_replace('/\s*\x{200C}\s*/u', "\u{200C}", $normalized) ?? $normalized;
        $normalized = preg_replace('/تله\s+فیلم/u', "تله\u{200C}فیلم", $normalized) ?? $normalized;

        if ($this->options['collapse_whitespace']) {
            $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? $normalized;
        }

        return trim($normalized);
    }
}
