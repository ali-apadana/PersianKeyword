<?php

declare(strict_types=1);

namespace PersianKeyword\DTO;

use JsonSerializable;

final readonly class KeywordScore implements JsonSerializable
{
    public function __construct(private string $keyword, private float $score, private int $frequency)
    {
    }

    public function keyword(): string { return $this->keyword; }
    public function score(): float { return $this->score; }
    public function frequency(): int { return $this->frequency; }

    /** @return array{keyword: string, score: float, frequency: int} */
    public function toArray(): array
    {
        return ['keyword' => $this->keyword, 'score' => $this->score, 'frequency' => $this->frequency];
    }

    /** @return array{keyword: string, score: float, frequency: int} */
    public function jsonSerialize(): array { return $this->toArray(); }
}
