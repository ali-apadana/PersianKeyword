<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

interface TextTokenizer
{
    /** @return list<string> */
    public function tokenize(string $text): array;

    /**
     * @param list<string> $tokens
     * @return list<string>
     */
    public function withoutStopwords(array $tokens): array;
}
