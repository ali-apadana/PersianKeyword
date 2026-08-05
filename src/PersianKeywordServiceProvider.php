<?php

declare(strict_types=1);

namespace PersianKeyword;

use Illuminate\Support\ServiceProvider;
use PersianKeyword\Contracts\KeywordExtractor;
use PersianKeyword\Contracts\PhraseExtractor;
use PersianKeyword\Contracts\TextNormalizer;
use PersianKeyword\Contracts\TextTokenizer;
use PersianKeyword\Engines\PersianKeywordEngine;
use PersianKeyword\Normalizers\PersianNormalizer;
use PersianKeyword\PhraseExtractors\PersianPhraseExtractor;
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

            return new PersianTokenizer(is_array($stopwords) ? $stopwords : []);
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
