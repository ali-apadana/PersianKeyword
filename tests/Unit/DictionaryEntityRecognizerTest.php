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

    public function test_it_recognizes_common_sport_event_names(): void
    {
        $recognizer = new DictionaryEntityRecognizer();

        $entities = $recognizer->recognize('حضور تیم تا جام ملت‌های آسیا ادامه دارد.', 'body');

        self::assertCount(1, $entities);
        self::assertSame('جام ملت‌های آسیا', $entities[0]->name());
        self::assertSame('event', $entities[0]->type());
    }

    public function test_it_recognizes_a_person_before_an_official_title_and_a_government_organization(): void
    {
        $recognizer = new DictionaryEntityRecognizer();

        $entities = $recognizer->recognize('محمد باقر قالیباف رئیس مجلس شورای اسلامی واکنش نشان داد.', 'title');
        $entities = array_map(static fn ($entity): array => $entity->toArray(), $entities);

        self::assertContains(['name' => 'محمد باقر قالیباف', 'type' => 'person', 'source' => 'title'], $entities);
        self::assertContains(['name' => 'مجلس شورای اسلامی', 'type' => 'organization', 'source' => 'title'], $entities);
    }
}
