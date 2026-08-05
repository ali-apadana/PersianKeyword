<?php

declare(strict_types=1);

namespace PersianKeyword;

use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\DTO\ExtractionResult;

final readonly class PersianKeyword
{
    public function __construct(private KeywordExtractor $extractor)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function extract(?string $title, ?string $body = null, array $options = []): ExtractionResult
    {
        return $this->extractor->extract($title, $body, $options);
    }
}
