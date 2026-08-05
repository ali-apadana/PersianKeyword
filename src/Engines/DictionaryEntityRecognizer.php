<?php

declare(strict_types=1);

namespace PersianKeyword\Engines;

use PersianKeyword\Contracts\EntityRecognizer;
use PersianKeyword\DTO\Entity;

final class DictionaryEntityRecognizer implements EntityRecognizer
{
    /** @param array<string, list<string>> $dictionary */
    public function __construct(private readonly array $dictionary = [])
    {
    }

    /** @return list<Entity> */
    public function recognize(string $text, string $source): array
    {
        $entities = [];
        $haystack = $this->lowercase($text);

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

    private function lowercase(string $text): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }
}
