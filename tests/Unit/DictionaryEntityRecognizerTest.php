<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Engines\DictionaryEntityRecognizer;
use PHPUnit\Framework\TestCase;

final class DictionaryEntityRecognizerTest extends TestCase
{
    public function test_it_recognizes_dictionary_entities_case_insensitively(): void
    {
        $recognizer = new DictionaryEntityRecognizer([
            'location' => ['تهران'],
            'technology' => ['Laravel'],
        ]);

        $entities = $recognizer->recognize('Laravel در تهران', 'title');

        self::assertSame('تهران', $entities[0]->name());
        self::assertSame('location', $entities[0]->type());
        self::assertSame('Laravel', $entities[1]->name());
        self::assertSame('title', $entities[1]->source());
    }
}
