<?php

declare(strict_types=1);

namespace PersianKeyword\Tokenizers;

use PersianKeyword\Contracts\TextTokenizer;

final class PersianTokenizer implements TextTokenizer
{
    /** @var list<string> */
    private const NON_TOPICAL_PATTERNS = [
        '/^نیست(?:م|ی|یم|ید|ند)?$/u',
        '/^(?:ن?می‌?)?توان(?:م|ی|د|یم|ید|ند)$/u',
    ];

    /** @var array<string, true> */
    private array $stopwords;

    /**
     * @param list<string> $stopwords
     */
    public function __construct(array $stopwords = [])
    {
        $this->stopwords = [];

        foreach ($stopwords as $stopword) {
            $this->stopwords[$this->lowercase($stopword)] = true;
        }
    }

    /** @return list<string> */
    public function tokenize(string $text): array
    {
        preg_match_all('/[\p{L}\p{N}]+(?:\x{200C}[\p{L}\p{N}]+)*/u', $text, $matches);

        /** @var list<string> $tokens */
        $tokens = array_map($this->lowercase(...), $matches[0]);

        return $tokens;
    }

    /**
     * @param list<string> $tokens
     * @return list<string>
     */
    public function withoutStopwords(array $tokens): array
    {
        return array_values(array_filter(
            $tokens,
            fn (string $token): bool => ! $this->isStopword($token),
        ));
    }

    public function isStopword(string $token): bool
    {
        $token = $this->lowercase($token);

        if (isset($this->stopwords[$token])) {
            return true;
        }

        foreach (self::NON_TOPICAL_PATTERNS as $pattern) {
            if (preg_match($pattern, $token) === 1) {
                return true;
            }
        }

        return false;
    }

    private function lowercase(string $text): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($text, 'UTF-8')
            : strtolower($text);
    }
}
