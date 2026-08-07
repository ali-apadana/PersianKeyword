<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Feature;

use PersianKeyword\Facades\Keyword;
use PersianKeyword\Tests\TestCase;

final class ExtractionTest extends TestCase
{
    public function test_the_public_api_returns_ranked_keywords_phrases_and_entities(): void
    {
        $result = Keyword::extract(
            'آموزش Laravel در تهران',
            'Laravel یک فریم‌ورک PHP است که در تهران نیز استفاده می‌شود.',
        );

        self::assertContains('laravel', $result->tokens());
        self::assertNotEmpty($result->keywords());
        self::assertContains('آموزش laravel', $result->phrases());

        $entities = array_map(static fn ($entity): array => $entity->toArray(), $result->entities());
        self::assertContains(['name' => 'تهران', 'type' => 'location', 'source' => 'title'], $entities);
        self::assertContains(['name' => 'Laravel', 'type' => 'technology', 'source' => 'title'], $entities);
    }

    public function test_it_prioritizes_a_detected_organization_over_a_generic_reporting_phrase(): void
    {
        $result = Keyword::extract(
            'پایان همکاری استقلال با رامین رضاییان',
            'باشگاه استقلال با انتشار اطلاعیه‌ای از پایان همکاری با رامین رضاییان خبر داد.',
        );

        self::assertContains('باشگاه استقلال', $result->keywords());
        self::assertNotContains('انتشار اطلاعیه‌ای', array_slice($result->keywords(), 0, 5));
        self::assertContains(['name' => 'باشگاه استقلال', 'type' => 'organization', 'source' => 'body'], array_map(
            static fn ($entity): array => $entity->toArray(),
            $result->entities(),
        ));
    }

    public function test_it_promotes_a_detected_sport_event_over_low_signal_terms(): void
    {
        $result = Keyword::extract(
            'قطع همکاری با قلعه نویی همچنان سوژه است/ نظر برخی مسئولان تغییر کرد',
            'فضای حاکم بر هیئت‌رئیسه فدراسیون فوتبال درباره ادامه همکاری با امیر قلعه‌نویی دستخوش تغییر شده و برخی از اعضا که پیش‌تر منتقد او بودند اکنون موافق ادامه حضور او تا جام ملت‌های آسیا هستند.',
            ['max_keywords' => 5],
        );

        self::assertContains('جام ملت‌های آسیا', $result->keywords());
        self::assertNotContains('ادامه', $result->keywords());
        self::assertContains(['name' => 'جام ملت‌های آسیا', 'type' => 'event', 'source' => 'body'], array_map(
            static fn ($entity): array => $entity->toArray(),
            $result->entities(),
        ));
    }

    public function test_it_filters_perfect_verbs_from_keyword_candidates(): void
    {
        $result = Keyword::extract(
            'حمله سایبری به سیستم‌های آبرسانی ۱۲ ایالت آمریکا',
            'سیستم‌های آبرسانی در ۱۲ ایالت آمریکا مورد حمله سایبری قرار گرفته‌اند.',
            ['max_keywords' => 5],
        );

        self::assertSame(['حمله سایبری', 'آمریکا', 'آبرسانی'], array_slice($result->keywords(), 0, 3));
        self::assertNotContains('گرفته‌اند', $result->keywords());
    }

    public function test_it_promotes_people_and_government_organizations_and_filters_low_signal_official_terms(): void
    {
        $result = Keyword::extract(
            'محمد باقر قالیباف رئیس مجلس شورای اسلامی در واکنش به ادعاهای اخیر مقامات آمریکا به دیپلماسی نمایشی اشاره کرد.',
            'رئیس مجلس شورای اسلامی در واکنش به ادعاهای اخیر مقامات آمریکا، نوشت: این دیپلماسی نمایشی، شکست خورده است.',
            ['max_keywords' => 5],
        );

        self::assertContains('محمد باقر قالیباف', $result->keywords());
        self::assertContains('مجلس شورای اسلامی', $result->keywords());
        self::assertContains('دیپلماسی نمایشی', $result->keywords());
        self::assertContains('آمریکا', $result->keywords());
        self::assertNotContains('اخیر مقامات', $result->keywords());
        self::assertNotContains('اخیر مقامات آمریکا', $result->keywords());
        self::assertNotContains('رئیس مجلس شورای', $result->keywords());
        self::assertNotContains('مجلس شورای', $result->keywords());
    }
}
