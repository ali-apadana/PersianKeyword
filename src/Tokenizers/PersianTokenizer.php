<?php

declare(strict_types=1);

namespace PersianKeyword\Tokenizers;

use PersianKeyword\Contracts\TextTokenizer;

final class PersianTokenizer implements TextTokenizer
{
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
            fn (string $token): bool => ! isset($this->stopwords[$token]),
        ));
    }

    private function lowercase(string $text): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($text, 'UTF-8')
            : strtolower($text);
    }
}
