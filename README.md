# PersianKeyword

`ali-apadana/persian-keyword` is a Laravel-first package for extracting keywords, important phrases, and dictionary-based named entities from Persian (Farsi) text.

**Version 1.0 is stable.** It provides a predictable, Laravel-native API for Persian text processing without requiring an external service.

HTML content is supported: tags are removed and entities such as `&zwnj;` and `&nbsp;` are decoded before extraction.

The built-in stop-word dictionary filters common news boilerplate, conjunctions, pronouns, temporal and quantity terms, auxiliary verbs, and frequent non-topical verb forms. Pattern rules also cover inflections such as `می‌تواند`, `نمی‌توانند`, and `می‌گوید`. You can point `resources.stopwords` at your own PHP list after publishing the configuration if your content needs different rules.

## Requirements

- PHP 8.2 or later
- Laravel 12

## Installation

```bash
composer require ali-apadana/persian-keyword
```

Laravel discovers the package automatically. To customize its defaults, publish the configuration file:

```bash
php artisan vendor:publish --tag=persian-keyword-config
```

## Usage

```php
use PersianKeyword\Facades\Keyword;

$result = Keyword::extract($title, $body);

$keywords = $result->keywords();
$tokens = $result->tokens();
$phrases = $result->phrases();
$entities = $result->entities();

$array = $result->toArray();
$json = $result->toJson();
```

Pass options for a single extraction when needed:

```php
$result = Keyword::extract($title, $body, [
    'max_keywords' => 5,
    'remove_stopwords' => true,
    'detect_entities' => false,
]);
```

`keywords()` returns the ranked keyword strings. Use `keywordScores()` when you also need each keyword's numeric score and frequency:

```php
foreach ($result->keywordScores() as $keyword) {
    echo $keyword->keyword();
    echo $keyword->score();
}
```

Each detected entity records its name, type, and whether it came from the title or body:

```php
foreach ($result->entities() as $entity) {
    echo $entity->name();   // e.g. تهران
    echo $entity->type();   // e.g. location
    echo $entity->source(); // title or body
}
```

The 0.2 result also exposes the normalized source text in `$result->meta()`:

```php
$normalizedTitle = $result->meta()['normalized_title'];
$normalizedBody = $result->meta()['normalized_body'];
```

By default, `tokens()` removes the package stop words. Preserve them for a specific call when you need the raw token stream:

```php
$result = Keyword::extract($title, $body, ['remove_stopwords' => false]);
```

`phrases()` returns unique two- and three-term candidates, such as `آموزش لاراول` and `برنامه نویسی لاراول`. A phrase never spans a stop word.

You can also use the service directly:

```php
use PersianKeyword\PersianKeyword;

$result = app(PersianKeyword::class)->extract($title, $body);
```

## Configuration

```php
return [
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
        'phrase_boost' => 1.0,
    ],
    'entity_recognition' => [
        'enabled' => true,
    ],
];
```

The initial dictionary is stored in `resources/entities.php`. After publishing the package configuration, point `resources.entities` to your own PHP dictionary file when your application needs additional entity types or names.

## Development

```bash
composer install
composer test
composer analyse
composer format
```

## Notes

Entity detection in 1.0 is dictionary-based. It is fast and deterministic, but it does not infer entities that are absent from the configured dictionary.

## License

Released under the [MIT License](LICENSE).
