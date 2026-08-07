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

    public function test_it_recognizes_refinery_projects_and_industrial_organization_names(): void
    {
        $result = Keyword::extract(
            'بازسازی پالایشگاه سوم پارس جنوبی کلید خورد',
            'آیین امضای توافق‌نامه پروژه بازسازی پالایشگاه فازهای ۴ و ۵ پارس جنوبی با حضور مدیرعامل سازمان گسترش و نوسازی صنایع ایران (ایدرو) و اعضای کنسرسیوم بازسازی این پروژه در منطقه پارس برگزار شد.',
            ['max_keywords' => 10],
        );

        self::assertContains('پالایشگاه سوم پارس جنوبی', $result->keywords());
        self::assertContains('پارس جنوبی', $result->keywords());
        self::assertContains('سازمان گسترش و نوسازی صنایع ایران', $result->keywords());
        self::assertContains('ایدرو', $result->keywords());
        self::assertNotContains('سازمان گسترش', $result->keywords());
        self::assertNotContains('جنوبی کلید', $result->keywords());
        self::assertNotContains('جنوبی کلید خورد', $result->keywords());
    }

    public function test_it_recognizes_people_before_academic_roles_and_complete_organizations(): void
    {
        $result = Keyword::extract(
            'پیام تسلیت رئیس سازمان تبلیغات اسلامی درپی درگذشت اندیشمند مازندرانی',
            'رئیس سازمان تبلیغات اسلامی در پیامی درگذشت «علی قائمی» اندیشمند کوشای مازندرانی در حوزه علم و تربیت را تسلیت گفت.',
            ['max_keywords' => 10],
        );

        self::assertContains('سازمان تبلیغات اسلامی', $result->keywords());
        self::assertContains('علی قائمی', $result->keywords());
        self::assertContains('پیام تسلیت', $result->keywords());
        self::assertNotContains('سازمان تبلیغات', $result->keywords());
        self::assertNotContains('تبلیغات اسلامی', $result->keywords());
        self::assertNotContains('اسلامی درپی', $result->keywords());
    }

    public function test_it_recognizes_sport_teams_clubs_and_disciplinary_events(): void
    {
        $result = Keyword::extract(
            'شروع تلخ مدافع تیم ملی پس از جدایی از پرسپولیس',
            'مدافع تیم ملی فوتبال ایران، نخستین حضورش در ترکیب اصلی ویتبسک بلاروس را با چشیدن طعم اخراج پشت سر گذاشت.',
            ['max_keywords' => 10],
        );

        self::assertContains('تیم ملی فوتبال ایران', $result->keywords());
        self::assertContains('ویتبسک بلاروس', $result->keywords());
        self::assertContains('پرسپولیس', $result->keywords());
        self::assertContains('اخراج', $result->keywords());
        self::assertNotContains('تلخ مدافع', $result->keywords());
    }

    public function test_it_recognizes_school_shooting_and_medical_terms(): void
    {
        $shooting = Keyword::extract(
            '۷ کشته بر اثر تیراندازی در یک مدرسه در تایلند',
            'بر اثر تیراندازی در مدرسه‌ای در شمال شهر بانکوک، پایتخت تایلند ۷ نفر کشته و ۳۰ تن زخمی شدند.',
            ['max_keywords' => 10],
        );
        self::assertContains('تیراندازی در مدرسه‌ای', $shooting->keywords());
        self::assertContains('تایلند', $shooting->keywords());
        self::assertContains('بانکوک', $shooting->keywords());
        self::assertNotContains('اثر تیراندازی', $shooting->keywords());
        self::assertNotContains('بانکوک پایتخت', $shooting->keywords());

        $health = Keyword::extract(
            'چای داغ بنوشید سرطان می‌گیرید',
            'رئیس مرکز تحقیقات سرطان‌های گوارشی دانشگاه علوم پزشکی تهران، از شناسایی یکی از مهم‌ترین عوامل رفتاری قابل پیشگیری در ابتلا به سرطان مری خبر داد.',
            ['max_keywords' => 10],
        );
        self::assertContains('چای داغ', $health->keywords());
        self::assertContains('سرطان مری', $health->keywords());
        self::assertContains('دانشگاه علوم پزشکی تهران', $health->keywords());
        self::assertNotContains('بنوشید سرطان', $health->keywords());
    }

    public function test_quality_regression_for_security_and_sport_news(): void
    {
        $security = Keyword::extract(
            'نیروهای مسلح عراق به حال آماده‌باش درآمدند',
            'همزمان با نزدیک شدن به پایان مهلت گروه‌های مقاومت عراق به بغداد برای پاسخ به تجاوز آمریکایی-سعودی به مقرهای حشد شعبی، دستور افزایش سطح آماده‌باش امنیتی و نظامی در این کشور صادر شد.',
            ['max_keywords' => 10],
        );
        self::assertContains('نیروهای مسلح عراق', $security->keywords());
        self::assertContains('حشد شعبی', $security->keywords());
        self::assertContains('آماده‌باش امنیتی', $security->keywords());
        self::assertNotContains('حال آماده‌باش', $security->keywords());

        $sport = Keyword::extract(
            'بازیکن خارجی استقلال راهی فوتبال یونان شد',
            'وینگر مالیایی فصل گذشته استقلال، پس از پایان همکاری با آبی‌پوشان و جدایی از این تیم، با امضای قراردادی رسمی به پانتولیکوس یونان پیوست.',
            ['max_keywords' => 10],
        );
        self::assertContains('پانتولیکوس یونان', $sport->keywords());
        self::assertContains('بازیکن خارجی استقلال', $sport->keywords());
        self::assertNotContains('استقلال راهی', $sport->keywords());
    }

    public function test_it_rejects_bridge_and_verb_phrases_before_scoring(): void
    {
        $result = Keyword::extract(
            'تصاویر جدید از پهپادهای منهدم‌شده آمریکا توسط سپاه',
            'تصاویر جدیدی از بقایای پهپادهای آمریکایی-صهیونی که توسط سامانه پدافندی نوین نیروی هوافضای سپاه پاسداران رهگیری و منهدم شده‌اند، منتشر شد.',
            ['max_keywords' => 10],
        );

        self::assertContains('سپاه پاسداران', $result->keywords());
        self::assertContains('نیروی هوافضای سپاه', $result->keywords());
        self::assertContains('سامانه پدافندی', $result->keywords());
        self::assertNotContains('آمریکا توسط', $result->keywords());
        self::assertNotContains('آمریکا توسط سپاه', $result->keywords());
    }
}
