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

    /** @var list<string> */
    private const GOVERNMENT_ORGANIZATION_PATTERNS = [
        '/(?<![\p{L}\p{N}])مجلس\s+شورای\s+اسلامی(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])شورای\s+عالی\s+امنیت\s+ملی(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])قوه\s+قضاییه(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])نیروی\s+انتظامی(?![\p{L}\p{N}])/u',
    ];

    private const PERSON_BEFORE_OFFICIAL_TITLE_PATTERN = '/(?<![\p{L}\p{N}])((?:[\p{L}][\p{L}\x{200C}-]*\s+){1,2}[\p{L}][\p{L}\x{200C}-]*)(?=\s*(?:[»”"]\s*)?(?:رئیس(?:\x{200C})?جمهور|رئیس|وزیر|دبیر|فرمانده|سخنگو|استاندار|شهردار|اندیشمند|استاد|پژوهشگر|نویسنده|شاعر)(?![\p{L}\p{N}]))/u';

    private const PERSON_AFTER_RANK_PATTERN = '/(?<![\p{L}\p{N}])(?:سرلشکر|سرتیپ|دکتر|مهندس)\s+([\p{L}][\p{L}\x{200C}-]*\s+[\p{L}][\p{L}\x{200C}-]*)(?=\s+(?:تاکید|گفت|اظهار|اعلام|نوشت)|\s*[،:])/u';

    /** @var list<string> */
    private const FACILITY_PATTERNS = [
        '/(?<![\p{L}\p{N}])پالایشگاه\s+(?:اول|دوم|سوم|چهارم|پنجم|[\p{N}]+)\s+پارس\s+جنوبی(?![\p{L}\p{N}])/u',
    ];

    /** @var list<string> */
    private const SPORT_TEAM_PATTERNS = [
        '/(?<![\p{L}\p{N}])تیم\s+ملی\s+فوتبال\s+ایران(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])[\p{L}][\p{L}\x{200C}-]*\s+بلاروس(?![\p{L}\p{N}])/u',
    ];

    /** @var list<string> */
    private const SPORT_ACTION_PATTERNS = [
        '/(?<![\p{L}\p{N}])اخراج(?![\p{L}\p{N}])/u',
    ];

    /** @var list<string> */
    private const NEWS_AND_MEDICAL_PATTERNS = [
        '/(?<![\p{L}\p{N}])تیراندازی\s+در\s+(?:یک\s+)?مدرسه(?:\x{200C}ای)?(?![\p{L}\p{N}])/u',
        '/(?<![\p{L}\p{N}])سرطان\s+[\p{L}\p{N}\x{200C}-]+(?![\p{L}\p{N}])/u',
    ];

    /** @var list<string> */
    private const NON_NAME_WORDS_BEFORE_A_ROLE = [
        'درگذشت', 'تسلیت', 'پیام', 'پیامی', 'مرحوم', 'شادروان', 'زنده‌یاد',
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

        foreach ($this->recognizeGovernmentOrganizations($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizePeopleBeforeOfficialTitles($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizePeopleAfterRanks($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizeFacilities($text, $source) as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizePatternEntities($text, $source, self::SPORT_TEAM_PATTERNS, 'sports_club') as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizePatternEntities($text, $source, self::SPORT_ACTION_PATTERNS, 'event') as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizePatternEntities($text, $source, self::NEWS_AND_MEDICAL_PATTERNS, 'event') as $entity) {
            $entities[] = $entity;
        }

        foreach ($this->recognizeCanonicalRelations($text, $source) as $entity) {
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
    private function recognizeCanonicalRelations(string $text, string $source): array
    {
        if (preg_match('/(?<![\p{L}\p{N}])(?:بازگشت\s+به\s+تلویزیون|به\s+تلویزیون\s+بازگشت)(?![\p{L}\p{N}])/u', $text) !== 1) return [];

        return [new Entity('بازگشت به تلویزیون', 'event', $source)];
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

            $name = trim($match[1].' '.$match[2]);
            if ($this->isPrefixOfDictionaryOrganization($name)) {
                continue;
            }

            $entities[] = new Entity($name, 'organization', $source);
        }

        return $entities;
    }

    private function isPrefixOfDictionaryOrganization(string $name): bool
    {
        $prefix = $this->lowercase($name).' ';

        foreach ($this->dictionary['organization'] ?? [] as $organization) {
            if (str_starts_with($this->lowercase($organization), $prefix)) {
                return true;
            }
        }

        return false;
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

    /** @return list<Entity> */
    private function recognizeGovernmentOrganizations(string $text, string $source): array
    {
        return $this->recognizePatternEntities($text, $source, self::GOVERNMENT_ORGANIZATION_PATTERNS, 'organization');
    }

    /** @return list<Entity> */
    private function recognizePeopleBeforeOfficialTitles(string $text, string $source): array
    {
        if (preg_match_all(self::PERSON_BEFORE_OFFICIAL_TITLE_PATTERN, $text, $matches) < 1) {
            return [];
        }

        $entities = [];
        foreach ($matches[1] as $match) {
            $name = $this->removeLeadingNonNameWord($match);
            $entities[] = new Entity($name, 'person', $source);
        }

        return $entities;
    }

    /** @return list<Entity> */
    private function recognizePeopleAfterRanks(string $text, string $source): array
    {
        if (preg_match_all(self::PERSON_AFTER_RANK_PATTERN, $text, $matches) < 1) return [];

        return array_map(static fn (string $name): Entity => new Entity($name, 'person', $source), $matches[1]);
    }

    private function removeLeadingNonNameWord(string $candidate): string
    {
        $terms = explode(' ', $candidate);
        if (count($terms) === 3 && in_array($this->lowercase($terms[0]), self::NON_NAME_WORDS_BEFORE_A_ROLE, true)) {
            array_shift($terms);
        }

        return implode(' ', $terms);
    }

    /** @return list<Entity> */
    private function recognizeFacilities(string $text, string $source): array
    {
        return $this->recognizePatternEntities($text, $source, self::FACILITY_PATTERNS, 'facility');
    }

    /** @param list<string> $patterns @return list<Entity> */
    private function recognizePatternEntities(string $text, string $source, array $patterns, string $type): array
    {
        $entities = [];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches) < 1) {
                continue;
            }

            foreach ($matches[0] as $name) {
                $entities[] = new Entity($name, $type, $source);
            }
        }

        return $entities;
    }

    private function lowercase(string $text): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }
}
