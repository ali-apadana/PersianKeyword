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
        '/^(?:ن?می‌?)?گوی(?:م|ی|د|یم|ید|ند)$/u',
        '/^(?:ن?می‌?)?(?:خواه(?:م|ی|د|یم|ید|ند)|خوا(?:م|ی|د|یم|ید|ن|ند))$/u',
        // Past participles with a Persian personal ending: گرفته‌اند، کرده‌ایم، شده است.
        '/^[\p{L}]+ه(?:\x{200C})?(?:ام|ای|ایم|اید|اند|است)$/u',
        // Frequent simple-past reporting and auxiliary verbs, with their personal endings.
        '/^(?:ن)?(?:شد|کرد|داشت|داد|گفت|آمد|رفت|ماند|رسید|یافت|خواند|دید|شنید|نوشت|گرفت)(?:م|ی|یم|ید|ند)?$/u',
    ];

    /** @var array<string, true> */
    private array $stopwords;

    /** @var array<string, true> */
    private array $compoundPrefixes;

    /**
     * @param list<string> $stopwords
     * @param list<string> $compoundPrefixes
     */
    public function __construct(array $stopwords = [], array $compoundPrefixes = [])
    {
        $this->stopwords = [];
        $this->compoundPrefixes = [];

        foreach ($stopwords as $stopword) {
            $this->stopwords[$this->lowercase($stopword)] = true;
        }

        foreach ($compoundPrefixes as $prefix) {
            $this->compoundPrefixes[$this->lowercase($prefix)] = true;
        }
    }

    /** @return list<string> */
    public function tokenize(string $text): array
    {
        preg_match_all('/[\p{L}\p{N}]+(?:\x{200C}[\p{L}\p{N}]+)*/u', $text, $matches);

        /** @var list<string> $tokens */
        $tokens = array_map($this->lowercase(...), $matches[0]);

        return $this->mergeModelTokens($tokens);
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

    /**
     * Merge a known model designator followed by a numeric identifier.
     *
     * @param list<string> $tokens
     * @return list<string>
     */
    private function mergeModelTokens(array $tokens): array
    {
        $merged = [];

        for ($index = 0, $count = count($tokens); $index < $count; $index++) {
            $token = $tokens[$index];
            $next = $tokens[$index + 1] ?? null;

            if ($next !== null
                && isset($this->compoundPrefixes[$token])
                && preg_match('/^\p{N}+$/u', $next) === 1) {
                $merged[] = $token.' '.$next;
                $index++;

                continue;
            }

            $merged[] = $token;
        }

        return $merged;
    }
}
