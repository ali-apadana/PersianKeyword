# PersianKeyword

`ali-apadana/persian-keyword` is a Laravel-first package for extracting keywords, important phrases, and named entities from Persian (Farsi) text.

> **0.4.0 adds Persian phrase extraction.** Keyword scoring is planned for the next release.

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
];
```

## Development

```bash
composer install
composer test
composer analyse
composer format
```

## Roadmap

- **0.5:** Keyword scoring engine
- **0.6:** Entity detection
- **1.0:** Stable production release

## License

Released under the [MIT License](LICENSE).
