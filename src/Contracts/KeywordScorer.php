<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

use PersianKeyword\DTO\KeywordScore;
use PersianKeyword\DTO\Entity;

interface KeywordScorer
{
    /**
     * @param list<string> $titleTokens
     * @param list<string> $bodyTokens
     * @param list<string> $titlePhrases
     * @param list<string> $bodyPhrases
     * @param list<Entity> $entities
     * @return list<KeywordScore>
     */
    public function score(
        array $titleTokens,
        array $bodyTokens,
        array $titlePhrases,
        array $bodyPhrases,
        array $entities,
        int $limit,
    ): array;
}
