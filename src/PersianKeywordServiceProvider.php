<?php

declare(strict_types=1);

namespace PersianKeyword;

use Illuminate\Support\ServiceProvider;
use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\Contracts\KeywordScorer;
use PersianKeyword\Contracts\EntityRecognizer;
use PersianKeyword\Engines\DictionaryEntityRecognizer;
use PersianKeyword\Contracts\PhraseExtractor;
use PersianKeyword\Contracts\TextNormalizer;
use PersianKeyword\Contracts\TextTokenizer;
use PersianKeyword\Engines\PersianKeywordEngine;
use PersianKeyword\Normalizers\PersianNormalizer;
use PersianKeyword\PhraseExtractors\PersianPhraseExtractor;
use PersianKeyword\Scoring\FrequencyKeywordScorer;
use PersianKeyword\Tokenizers\PersianTokenizer;

final class PersianKeywordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/persian-keyword.php', 'persian-keyword');

        $this->app->singleton(TextNormalizer::class, function (): PersianNormalizer {
            /** @var array<string, bool> $options */
            $options = config('persian-keyword.normalizer', []);

            return new PersianNormalizer($options);
        });

        $this->app->singleton(TextTokenizer::class, function (): PersianTokenizer {
            /** @var mixed $stopwords */
            $stopwords = require config('persian-keyword.resources.stopwords');
            /** @var mixed $prefixes */
            $prefixes = require config('persian-keyword.resources.prefixes');

            return new PersianTokenizer(
                is_array($stopwords) ? $stopwords : [],
                is_array($prefixes) ? $prefixes : [],
            );
        });

        $this->app->singleton(PhraseExtractor::class, function ($app): PersianPhraseExtractor {
            /** @var array{min_terms?: int, max_terms?: int} $options */
            $options = config('persian-keyword.phrase_extractor', []);

            return new PersianPhraseExtractor(
                tokenizer: $app->make(TextTokenizer::class),
                minTerms: $options['min_terms'] ?? 2,
                maxTerms: $options['max_terms'] ?? 3,
            );
        });

        $this->app->singleton(KeywordScorer::class, function (): FrequencyKeywordScorer {
            /** @var array{title_weight?: float, body_weight?: float, title_phrase_weight?: float, body_phrase_weight?: float, entity_boost?: float} $options */
            $options = config('persian-keyword.scoring', []);

            return new FrequencyKeywordScorer(
                $options,
                (int) config('persian-keyword.min_keyword_length', 2),
            );
        });

        $this->app->singleton(EntityRecognizer::class, function (): DictionaryEntityRecognizer {
            /** @var mixed $entities */
            $entities = require config('persian-keyword.resources.entities');

            return new DictionaryEntityRecognizer(is_array($entities) ? $entities : []);
        });

        $this->app->singleton(KeywordExtractor::class, PersianKeywordEngine::class);
        $this->app->singleton(PersianKeyword::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/persian-keyword.php' => config_path('persian-keyword.php'),
        ], 'persian-keyword-config');
    }
}
