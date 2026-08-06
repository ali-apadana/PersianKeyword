# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

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
