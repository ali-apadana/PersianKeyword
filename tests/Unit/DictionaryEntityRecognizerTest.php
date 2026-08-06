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

    public function test_it_recognizes_a_prefixed_organization_name_without_a_dictionary_entry(): void
    {
        $recognizer = new DictionaryEntityRecognizer();

        $entities = $recognizer->recognize('باشگاه استقلال با انتشار اطلاعیه‌ای خبر داد.', 'body');

        self::assertCount(1, $entities);
        self::assertSame('باشگاه استقلال', $entities[0]->name());
        self::assertSame('organization', $entities[0]->type());
        self::assertSame('body', $entities[0]->source());
    }
}
