<?php

declare(strict_types=1);

namespace PersianKeyword\Engines;

use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\Contracts\TextNormalizer;
use PersianKeyword\DTO\ExtractionResult;

final class PersianKeywordEngine implements KeywordExtractor
{
    public function __construct(private readonly TextNormalizer $normalizer)
    {
    }

    /**
     * The 0.1 foundation deliberately returns an empty, correctly-shaped result.
     * Normalization and extraction are introduced in subsequent minor releases.
     *
     * @param array<string, mixed> $options
     */
    public function extract(?string $title, ?string $body = null, array $options = []): ExtractionResult
    {
        $normalize = $options['normalize'] ?? config('persian-keyword.normalize', true);

        return new ExtractionResult(meta: [
            'version' => '0.2.0',
            'status' => 'normalization-ready',
            'normalized_title' => $normalize ? $this->normalizer->normalize($title) : $title,
            'normalized_body' => $normalize ? $this->normalizer->normalize($body) : $body,
        ]);
    }
}
