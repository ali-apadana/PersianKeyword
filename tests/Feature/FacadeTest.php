<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Feature;

use PersianKeyword\Facades\Keyword;
use PersianKeyword\Tests\TestCase;

final class FacadeTest extends TestCase
{
    public function test_it_returns_a_structured_foundation_result(): void
    {
        $result = Keyword::extract('نمونه عنوان', 'این یک نمونه متن آزمایشی است');

        self::assertSame(['نمونه', 'عنوان', 'متن', 'آزمایشی'], $result->keywords());
        self::assertSame(['نمونه', 'عنوان', 'نمونه', 'متن', 'آزمایشی'], $result->tokens());
        self::assertSame(['نمونه عنوان', 'نمونه متن', 'متن آزمایشی', 'نمونه متن آزمایشی'], $result->phrases());
        self::assertSame('phrase-extraction-ready', $result->meta()['status']);
        self::assertSame('نمونه عنوان', $result->meta()['normalized_title']);
        self::assertSame('نمونه', $result->keywordScores()[0]->keyword());
        self::assertSame([], $result->entities());
    }

    public function test_it_can_disable_entity_recognition_for_one_extraction(): void
    {
        $result = Keyword::extract('Laravel در تهران', null, ['detect_entities' => false]);

        self::assertSame([], $result->entities());
        self::assertFalse($result->meta()['entity_recognition_enabled']);
    }
}
