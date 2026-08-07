# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [3.1.0] - 2026-08-07

### Added

- Quality-regression coverage for security and sports news examples.
- Built-in entities for `نیروهای مسلح عراق`, `حشد شعبی`, and `پانتولیکوس یونان`.

### Improved

- Native filtering for common past verbs such as `درآمدند` and `پیوست`.

## [3.0.0] - 2026-08-07

### Added

- Structured native entities for major news organizations, people, and recurring news concepts.

## [2.9.0] - 2026-08-07

### Added

- Native recognition of a ranked person name such as `سرلشکر محسن رضایی`.
- Built-in entities for `تنگه هرمز`, `بانک مرکزی`, `کیف پول ایران`, `پرداخت الکترونیکی`, `کدهای دستوری`, and `تلفن همراه`.

### Improved

- Filtered low-signal phrases built around `اجازه باز` and `گام تازه`.

## [2.8.0] - 2026-08-07

### Added

- Native recognition of `تیراندازی در مدرسه` and medical terms such as `سرطان مری`.
- Built-in entries for `تایلند`, `بانکوک`, and `دانشگاه علوم پزشکی تهران`.

### Improved

- Filtered imperative and present verb forms such as `بنوشید` and `می‌گیرید`, plus low-signal phrases containing `اثر` and `پایتخت`.

## [2.7.0] - 2026-08-07

### Added

- Native recognition for `تیم ملی فوتبال ایران`, a club-country form such as `ویتبسک بلاروس`, and the disciplinary event `اخراج`.
- Built-in entries for `پرسپولیس` and `بلاروس`.

### Improved

- Sports-team ranking and filtering of generic sports-role and narrative terms such as `مدافع`, `تلخ`, and `پشت سر`.

## [2.6.0] - 2026-08-07

### Added

- Native person detection before academic and cultural roles such as `اندیشمند`, `استاد`, `پژوهشگر`, `نویسنده`, and `شاعر`.
- Built-in organization entry for `سازمان تبلیغات اسلامی`.

### Improved

- Filtered the connector `درپی` and avoided shortened organization phrases when a complete organization entity is available.
- Improved recognition of quoted names in obituary and condolence text.

## [2.5.0] - 2026-08-07

### Added

- Native facility recognition for numbered South Pars refineries, such as `پالایشگاه سوم پارس جنوبی`.
- Built-in industrial organization entries for `سازمان گسترش و نوسازی صنایع ایران` and `ایدرو`.
- Stronger ranking for complete, long organization names.

### Improved

- Filtered the low-signal news idiom `کلید خورد`.
- Prevented a shortened dynamic organization match such as `سازمان گسترش` when a longer dictionary organization name is present.

## [2.4.0] - 2026-08-07

### Added

- Native recognition of a Persian person name when it appears immediately before an official title, such as `محمد باقر قالیباف رئیس مجلس`.
- Native recognition of common multi-word government organizations, including `مجلس شورای اسلامی`, `شورای عالی امنیت ملی`, `قوه قضاییه`, and `نیروی انتظامی`.
- Person-specific ranking boost and removal of phrase fragments that duplicate a stronger detected entity.

### Improved

- Filtered low-signal official and temporal words such as `رئیس`, `اخیر`, and `مقامات`, plus common colloquial verb forms such as `می‌خوان`.

## [2.3.0] - 2026-08-06

### Improved

- Added native pattern-based filtering for common Persian past and perfect verb inflections such as `گرفته‌اند`, `کرده‌ایم`, `گرفتند`, and `رسید`.
- Prevented low-signal sentence-ending verbs from appearing as keyword candidates.

## [2.2.0] - 2026-08-06

### Added

- Native recognition of common sport events including `جام ملت‌های آسیا`, `جام جهانی`, `لیگ برتر`, and `بازی‌های المپیک`.
- A separate event-ranking boost so recognized sport events outrank low-signal terms.

## [2.1.0] - 2026-08-06

### Added

- Native recognition of organization names that begin with terms such as `باشگاه`, `شرکت`, `وزارت`, `سازمان`, `دانشگاه`, `شهرداری`, and `فدراسیون`.

### Improved

- Organization phrases such as `باشگاه استقلال` receive an entity boost and rank above generic reporting phrases such as `انتشار اطلاعیه‌ای`.

## [2.0.2] - 2026-08-06

### Added

- Native PHP phrase-aware keyword ranking, without external services or Python dependencies.
- Strong scoring for phrases found in the title and body, plus dictionary-entity boosts.
- Country dictionary entries for the United States, Ukraine, and Azerbaijan.

### Improved

- Standalone numbers and generic phrase heads such as `سیستم` and `ایالت` no longer dominate keywords.
- Keyword output avoids returning individual words already covered by a stronger selected phrase.

## [1.5.0] - 2026-08-06

### Improved

- Expanded the built-in Persian stop-word vocabulary with formal connectors, pronoun variants, number forms, temporal terms, and auxiliary-verb inflections.
- Kept the public extraction API unchanged.

## [1.4.0] - 2026-08-06

### Added

- Model-designator token merging for Persian and Latin identifiers such as `اف ۳۵` and `F-35`.
- Initial aircraft entity dictionary entries.

## [1.3.0] - 2026-08-06

### Improved

- Filtered low-signal news terms including temporal, quantity, and vague-reference words.
- Added pattern-based filtering for reporting verbs such as `می‌گوید`.

## [1.2.0] - 2026-08-06

### Improved

- Added pattern-based filtering for Persian auxiliary verb inflections, including `می‌تواند` and `نمی‌توانند`.
- Added the missing copula form `نیست` to the stop-word vocabulary.

### Fixed

- Applied pattern-based stop-word rules consistently during token filtering.

## [1.1.0] - 2026-08-06

### Improved

- Expanded the built-in Persian stop-word dictionary with conjunctions, pronouns, auxiliary verbs, common verb forms, and detached clitics.
- Filtered non-topical verb forms such as `کنند`, while preserving meaningful news terms such as `شایعه` and `تکذیب`.

## [1.0.3] - 2026-08-05

### Improved

- Filter common Persian news boilerplate such as `اخبار`, `رسمی`, `منتشر`, and `شده` from keyword candidates.

## [1.0.2] - 2026-08-05

### Fixed

- Decode HTML entities such as `&zwnj;` and `&nbsp;` before tokenization.
- Remove HTML tags before extracting keywords.
- Filter common detached Persian affixes including `می` and `های`.

## [1.0.1] - 2026-08-05

### Fixed

- Removed an extra closing bracket from the packaged stop-word dictionary.

## [1.0.0] - 2026-08-05

### Added

- Stable public API for normalization, tokenization, phrase extraction, keyword scoring, and dictionary entities.
- Per-extraction `detect_entities` option and global entity-recognition configuration.
- Configuration validation for phrase lengths.
- GitHub Actions quality checks for supported PHP versions.
- Package export rules for lean release archives.

## [0.6.0] - 2026-08-05

### Added

- Dictionary-based entity recognition for locations, organizations, and technologies.
- Typed `Entity` results with the entity name, type, and source field.
- Configurable entity dictionary resource for extending the built-in vocabulary.

## [0.5.0] - 2026-08-05

### Added

- Frequency-based keyword scoring with greater weight for title terms.
- Phrase-membership boost for terms that appear in candidate phrases.
- Ranked `keywords()` output and typed `keywordScores()` details.
- Configurable title, body, and phrase weights.

## [0.4.0] - 2026-08-05

### Added

- Candidate phrase extraction for adjacent Persian bigrams and trigrams.
- Configurable minimum and maximum phrase lengths.
- Stop-word boundaries, preventing phrases from being formed across connector words.

## [0.3.0] - 2026-08-05

### Added

- Persian-aware tokenizer with support for Unicode words, numbers, and half-space compounds.
- Stop-word filtering for the extraction pipeline, enabled by default and configurable per call.
- `tokens()` on the typed extraction result, plus source token counts in result metadata.

## [0.1.0] - 2026-08-05

### Added

- Laravel 12 package discovery, service provider, and `Keyword` facade.
- PSR-4 package structure and package configuration.
- Typed `ExtractionResult` response object with array and JSON serialization.
- Empty resource dictionaries and a foundation engine to establish a stable API.
- PHPUnit, PHPStan, and Laravel Pint development configuration.

## [0.2.0] - 2026-08-05

### Added

- Configurable Persian normalizer for Arabic letter variants, digits, diacritics, invisible marks, and whitespace.
- Initial Persian stop-word dictionary.
- Normalized title and body fields in the foundation extraction result metadata.
