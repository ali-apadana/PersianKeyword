<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default extraction options
    |--------------------------------------------------------------------------
    |
    | These options establish the package contract. Extraction engines will be
    | introduced in later releases while keeping these configuration keys stable.
    |
    */
    'max_keywords' => 10,

    'min_keyword_length' => 2,

    'normalize' => true,

    'normalizer' => [
        'decode_html' => true,
        'strip_html' => true,
        'convert_arabic_characters' => true,
        'convert_digits' => true,
        'remove_diacritics' => true,
        'collapse_whitespace' => true,
    ],

    'tokenizer' => [
        'remove_stopwords' => true,
    ],

    'phrase_extractor' => [
        'min_terms' => 2,
        'max_terms' => 3,
    ],

    'scoring' => [
        'title_weight' => 2.0,
        'body_weight' => 1.0,
        'title_phrase_weight' => 5.0,
        'body_phrase_weight' => 2.0,
        'entity_boost' => 3.0,
        'event_boost' => 4.0,
        'person_boost' => 4.0,
    ],

    'entity_recognition' => [
        'enabled' => true,
    ],

    'resources' => [
        'stopwords' => __DIR__.'/../resources/stopwords.php',
        'prefixes' => __DIR__.'/../resources/prefixes.php',
        'entities' => __DIR__.'/../resources/entities.php',
    ],
];
