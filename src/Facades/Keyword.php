<?php

declare(strict_types=1);

namespace PersianKeyword\Facades;

use Illuminate\Support\Facades\Facade;
use PersianKeyword\Contracts\KeywordExtractor;

/**
 * @method static \PersianKeyword\DTO\ExtractionResult extract(?string $title, ?string $body = null, array<string, mixed> $options = [])
 *
 * @see \PersianKeyword\Engines\PersianKeywordEngine
 */
final class Keyword extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return KeywordExtractor::class;
    }
}
