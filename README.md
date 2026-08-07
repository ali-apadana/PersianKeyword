# PersianKeyword

`ali-apadana/persian-keyword` is a Laravel-first package for extracting keywords, important phrases, and dictionary-based named entities from Persian (Farsi) text.

**Version 2.4 provides native phrase-aware ranking, person and organization recognition, and sport-event recognition.** It remains fully PHP/Laravel based and requires no external service.

HTML content is supported: tags are removed and entities such as `&zwnj;` and `&nbsp;` are decoded before extraction.

Known letter-and-number model names remain one token. For example, `اف ۳۵` and `F-35` are extracted as complete model identifiers rather than separate words.

The built-in stop-word dictionary includes broad Persian coverage: formal connectors, pronoun variants, number and temporal forms, news boilerplate, auxiliary verbs, and frequent non-topical verb forms. Pattern rules also cover inflections such as `می‌تواند`, `نمی‌توانند`, and `می‌گوید`. You can point `resources.stopwords` at your own PHP list after publishing the configuration if your content needs different rules.

Common past and perfect verb forms such as `گرفته‌اند`, `کرده‌ایم`, and `رسیدند` are filtered with native patterns, so sentence-ending verbs do not become keywords.

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

The extractor ranks meaningful phrases alongside single terms. For example, a title containing `حمله سایبری به سیستم‌های آبرسانی ۱۲ ایالت آمریکا` prioritizes `حمله سایبری`, `آمریکا`, and `آبرسانی` instead of standalone numbers or generic heads such as `سیستم`.

It also recognizes organization phrases without requiring a dictionary entry. For example, `باشگاه استقلال` is detected as an `organization` entity and receives a ranking boost.

Names that immediately precede an official title are detected as people. For example, `محمد باقر قالیباف رئیس مجلس` detects `محمد باقر قالیباف` as a `person`; government organizations such as `مجلس شورای اسلامی` are also recognized as complete entities.

The same native rule supports cultural and academic roles: `علی قائمی` is recognized as a `person` in a phrase such as `علی قائمی، اندیشمند مازندرانی`.

The native facility rules recognize numbered South Pars refineries such as `پالایشگاه سوم پارس جنوبی`. The built-in entity dictionary also includes the Industrial Development and Renovation Organization of Iran and its acronym, `ایدرو`.

Common sport events such as `جام ملت‌های آسیا`, `جام جهانی`, `لیگ برتر`, and `بازی‌های المپیک` are also recognized as `event` entities and receive an additional ranking boost.

Sports support also recognizes `تیم ملی فوتبال ایران`, club-country names such as `ویتبسک بلاروس`, and disciplinary events such as `اخراج`.

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
        'title_phrase_weight' => 5.0,
        'body_phrase_weight' => 2.0,
        'entity_boost' => 3.0,
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
