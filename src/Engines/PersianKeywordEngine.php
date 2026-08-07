<?php

declare(strict_types=1);

namespace PersianKeyword\Engines;

use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\Contracts\KeywordScorer;
use PersianKeyword\Contracts\EntityRecognizer;
use PersianKeyword\Contracts\PhraseExtractor;
use PersianKeyword\Contracts\TextNormalizer;
use PersianKeyword\Contracts\TextTokenizer;
use PersianKeyword\DTO\ExtractionResult;
use PersianKeyword\DTO\KeywordScore;

final class PersianKeywordEngine implements KeywordExtractor
{
    public function __construct(
        private readonly TextNormalizer $normalizer,
        private readonly TextTokenizer $tokenizer,
        private readonly PhraseExtractor $phraseExtractor,
        private readonly KeywordScorer $scorer,
        private readonly EntityRecognizer $entityRecognizer,
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

        $titlePhrases = $this->phraseExtractor->extract($titleTokens);
        $bodyPhrases = $this->phraseExtractor->extract($bodyTokens);
        $phrases = [...$titlePhrases, ...$bodyPhrases];

        if ($removeStopwords) {
            $titleTokens = $this->tokenizer->withoutStopwords($titleTokens);
            $bodyTokens = $this->tokenizer->withoutStopwords($bodyTokens);
        }

        $detectEntities = $options['detect_entities'] ?? config('persian-keyword.entity_recognition.enabled', true);
        $entities = $detectEntities
            ? [
                ...$this->entityRecognizer->recognize($normalizedTitle, 'title'),
                ...$this->entityRecognizer->recognize($normalizedBody, 'body'),
            ]
            : [];

        $keywordScores = $this->scorer->score(
            $titleTokens,
            $bodyTokens,
            $titlePhrases,
            $bodyPhrases,
            $this->uniqueEntities($entities),
            (int) ($options['max_keywords'] ?? config('persian-keyword.max_keywords', 10)),
        );

        return new ExtractionResult(
            tokens: [...$titleTokens, ...$bodyTokens],
            keywords: array_map(static fn (KeywordScore $score): string => $score->keyword(), $keywordScores),
            keywordScores: $keywordScores,
            phrases: array_values(array_unique($phrases)),
            entities: $entities,
            meta: [
                'version' => '3.3.0',
                'status' => 'stable',
                'normalized_title' => $normalizedTitle,
                'normalized_body' => $normalizedBody,
                'title_token_count' => count($titleTokens),
                'body_token_count' => count($bodyTokens),
                'entity_recognition_enabled' => $detectEntities,
            ],
        );
    }

    /** @param list<\PersianKeyword\DTO\Entity> $entities @return list<\PersianKeyword\DTO\Entity> */
    private function uniqueEntities(array $entities): array
    {
        $unique = [];
        foreach ($entities as $entity) {
            $unique[$entity->type().'|'.$entity->name()] ??= $entity;
        }

        return array_values($unique);
    }
}
