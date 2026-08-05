<?php

declare(strict_types=1);

namespace PersianKeyword\DTO;

use JsonSerializable;

final readonly class ExtractionResult implements JsonSerializable
{
    /**
     * @param list<string> $tokens
     * @param list<string> $keywords
     * @param list<KeywordScore> $keywordScores
     * @param list<string> $phrases
     * @param list<Entity> $entities
     * @param array<string, mixed> $meta
     */
    public function __construct(
        private array $tokens = [],
        private array $keywords = [],
        private array $keywordScores = [],
        private array $phrases = [],
        private array $entities = [],
        private array $meta = [],
    ) {
    }

    /** @return list<string> */
    public function tokens(): array
    {
        return $this->tokens;
    }

    /** @return list<string> */
    public function keywords(): array
    {
        return $this->keywords;
    }

    /** @return list<KeywordScore> */
    public function keywordScores(): array
    {
        return $this->keywordScores;
    }

    /** @return list<string> */
    public function phrases(): array
    {
        return $this->phrases;
    }

    /** @return list<Entity> */
    public function entities(): array
    {
        return $this->entities;
    }

    /** @return array<string, mixed> */
    public function meta(): array
    {
        return $this->meta;
    }

    /** @return array{tokens: list<string>, keywords: list<string>, keyword_scores: list<array{keyword: string, score: float, frequency: int}>, phrases: list<string>, entities: list<array{name: string, type: string, source: string}>, meta: array<string, mixed>} */
    public function toArray(): array
    {
        return [
            'tokens' => $this->tokens(),
            'keywords' => $this->keywords(),
            'keyword_scores' => array_map(static fn (KeywordScore $score): array => $score->toArray(), $this->keywordScores()),
            'phrases' => $this->phrases(),
            'entities' => array_map(static fn (Entity $entity): array => $entity->toArray(), $this->entities()),
            'meta' => $this->meta(),
        ];
    }

    public function toJson(int $flags = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), $flags | JSON_THROW_ON_ERROR);
    }

    /** @return array{tokens: list<string>, keywords: list<string>, keyword_scores: list<array{keyword: string, score: float, frequency: int}>, phrases: list<string>, entities: list<array{name: string, type: string, source: string}>, meta: array<string, mixed>} */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
