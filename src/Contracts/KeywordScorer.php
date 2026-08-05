<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

use PersianKeyword\DTO\KeywordScore;

interface KeywordScorer
{
    /**
     * @param list<string> $titleTokens
     * @param list<string> $bodyTokens
     * @param list<string> $titlePhrases
     * @param list<string> $bodyPhrases
     * @return list<KeywordScore>
     */
    public function score(array $titleTokens, array $bodyTokens, array $titlePhrases, array $bodyPhrases, int $limit): array;
}
