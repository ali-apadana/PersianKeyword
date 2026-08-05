<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

use PersianKeyword\DTO\ExtractionResult;

interface KeywordExtractor
{
    /**
     * Extract structured information from a Persian title and body.
     *
     * @param array<string, mixed> $options
     */
    public function extract(?string $title, ?string $body = null, array $options = []): ExtractionResult;
}
