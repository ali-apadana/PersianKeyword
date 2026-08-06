<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Scoring\FrequencyKeywordScorer;
use PersianKeyword\DTO\Entity;
use PHPUnit\Framework\TestCase;

final class PhraseAwareScoringTest extends TestCase
{
    public function test_it_promotes_meaningful_phrases_and_entities_and_ignores_standalone_numbers(): void
    {
        $scorer = new FrequencyKeywordScorer();

        $scores = $scorer->score(
            ['حمله', 'سایبری', 'سیستم‌های', 'آبرسانی', '۱۲', 'ایالت', 'آمریکا'],
            ['سیستم‌های', 'آبرسانی', '۱۲', 'ایالت', 'آمریکا', 'حمله', 'سایبری'],
            ['حمله سایبری', 'سیستم‌های آبرسانی', 'ایالت آمریکا'],
            ['سیستم‌های آبرسانی', 'ایالت آمریکا', 'حمله سایبری'],
            [new Entity('آمریکا', 'location', 'title')],
            10,
        );

        self::assertSame(['حمله سایبری', 'آمریکا', 'آبرسانی'], array_map(
            static fn ($score): string => $score->keyword(),
            array_slice($scores, 0, 3),
        ));
    }
}
