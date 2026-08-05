# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

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
