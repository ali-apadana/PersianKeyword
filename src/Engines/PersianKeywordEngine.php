<?php

declare(strict_types=1);

namespace PersianKeyword\Engines;

use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\Contracts\TextNormalizer;
use PersianKeyword\Contracts\TextTokenizer;
use PersianKeyword\DTO\ExtractionResult;

final class PersianKeywordEngine implements KeywordExtractor
{
    public function __construct(
        private readonly TextNormalizer $normalizer,
        private readonly TextTokenizer $tokenizer,
    ) {
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

        $normalizedTitle = $normalize ? $this->normalizer->normalize($title) : (string) $title;
        $normalizedBody = $normalize ? $this->normalizer->normalize($body) : (string) $body;
        $removeStopwords = $options['remove_stopwords'] ?? config('persian-keyword.tokenizer.remove_stopwords', true);

        $titleTokens = $this->tokenizer->tokenize($normalizedTitle);
        $bodyTokens = $this->tokenizer->tokenize($normalizedBody);

        if ($removeStopwords) {
            $titleTokens = $this->tokenizer->withoutStopwords($titleTokens);
            $bodyTokens = $this->tokenizer->withoutStopwords($bodyTokens);
        }

        return new ExtractionResult(
            tokens: [...$titleTokens, ...$bodyTokens],
            meta: [
                'version' => '0.3.0',
                'status' => 'tokenization-ready',
                'normalized_title' => $normalizedTitle,
                'normalized_body' => $normalizedBody,
                'title_token_count' => count($titleTokens),
                'body_token_count' => count($bodyTokens),
            ],
        );
    }
}
