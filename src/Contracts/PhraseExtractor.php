<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

interface PhraseExtractor
{
    /**
     * @param list<string> $tokens
     * @return list<string>
     */
    public function extract(array $tokens): array;
}
