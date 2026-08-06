<?php

declare(strict_types=1);

namespace PersianKeyword\Engines;

use PersianKeyword\Contracts\EntityRecognizer;
use PersianKeyword\DTO\Entity;

final class DictionaryEntityRecognizer implements EntityRecognizer
{
    /** @var list<string> */
    private const ORGANIZATION_PREFIXES = [
        'باشگاه', 'شرکت', 'وزارت', 'سازمان', 'دانشگاه', 'شهرداری', 'فدراسیون',
    ];

    /** @var list<string> */
    private const ORGANIZATION_CONTINUATIONS_TO_IGNORE = [
        'از', 'با', 'به', 'در', 'برای', 'که', 'و', 'یا', 'این', 'آن', 'یک',
    ];

    /** @var list<string> */
    private const SPORT_EVENT_PATTERNS = [
        '/(?<![\p{L}\p{N}])جام\s+ملت(?:\x{200C})?های\s+[\p{L}\p{N}\x{200C}-]+(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])جام\s+(?:جهانی|حذفی)(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])لیگ\s+(?:برتر|قهرمانان)(?:\s+اروپا)?(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])بازی(?:\x{200C})?های\s+المپیک(?![\p{L}\p{N}])/u',
    ];

    /** @param array<string, list<string>> $dictionary */
    public function __construct(private readonly array $dictionary = [])
    {
    }

    /** @return list<Entity> */
    public function recognize(string $text, string $source): array
    {
        $entities = [];
        $haystack = $this->lowercase($text);

        foreach ($this->recognizeOrganizationNames($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizeSportEvents($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->dictionary as $type => $names) {
            foreach ($names as $name) {
                $needle = $this->lowercase($name);
                $pattern = '/(?<![\p{L}\p{N}])'.preg_quote($needle, '/').'(?![\p{L}\p{N}])/u';

                if ($name === '' || preg_match($pattern, $haystack) !== 1) continue;

                $entities[] = new Entity($name, $type, $source);
            }
        }

        return $entities;
    }

    /** @return list<Entity> */
    private function recognizeOrganizationNames(string $text, string $source): array
    {
        $prefixes = implode('|', array_map(static fn (string $prefix): string => preg_quote($prefix, '/'), self::ORGANIZATION_PREFIXES));
        $pattern = '/(?<![\p{L}\p{N}])('.$prefixes.')\\s+([\p{L}\p{N}\x{200C}-]+)(?![\p{L}\p{N}])/u';

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER) < 1) {
            return [];
        }

        $entities = [];
        foreach ($matches as $match) {
            $continuation = $this->lowercase($match[2]);
            if (in_array($continuation, self::ORGANIZATION_CONTINUATIONS_TO_IGNORE, true)) {
                continue;
            }

            $entities[] = new Entity(trim($match[1].' '.$match[2]), 'organization', $source);
        }

        return $entities;
    }

    /** @return list<Entity> */
    private function recognizeSportEvents(string $text, string $source): array
    {
        $entities = [];

        foreach (self::SPORT_EVENT_PATTERNS as $pattern) {
            if (preg_match_all($pattern, $text, $matches) < 1) {
                continue;
            }

            foreach ($matches[0] as $name) {
                $entities[] = new Entity($name, 'event', $source);
            }
        }

        return $entities;
    }

    private function lowercase(string $text): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }
}
