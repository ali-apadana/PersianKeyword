<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Tokenizers\PersianTokenizer;
use PHPUnit\Framework\TestCase;

final class PersianTokenizerTest extends TestCase
{
    public function test_it_extracts_words_numbers_and_compounds_from_persian_text(): void
    {
        $tokenizer = new PersianTokenizer();

        self::assertSame(
            ['لاراول', '۱۲', 'آموزش', 'می‌شود'],
            $tokenizer->tokenize('لاراول ۱۲؛ آموزش می‌شود.'),
        );
    }

    public function test_it_can_remove_stopwords(): void
    {
        $tokenizer = new PersianTokenizer(['این', 'است']);

        self::assertSame(['نمونه', 'متن'], $tokenizer->withoutStopwords(['این', 'نمونه', 'متن', 'است']));
    }

    public function test_it_removes_common_news_boilerplate_when_present_in_the_dictionary(): void
    {
        $tokenizer = new PersianTokenizer(['اخبار', 'منتشر', 'شده']);

        self::assertSame(
            ['تخلیه', 'جاسک'],
            $tokenizer->withoutStopwords(['اخبار', 'تخلیه', 'منتشر', 'شده', 'جاسک']),
        );
    }

    public function test_it_removes_high_frequency_verb_forms_without_removing_news_topics(): void
    {
        $tokenizer = new PersianTokenizer(['کنند', 'کردند', 'شده', 'است']);

        self::assertSame(
            ['شایعه', 'اطلاعیه', 'تکذیب'],
            $tokenizer->withoutStopwords(['شایعه', 'کنند', 'اطلاعیه', 'شده', 'تکذیب', 'است']),
        );
    }

    public function test_it_filters_auxiliary_verb_inflections_with_or_without_a_half_space(): void
    {
        $tokenizer = new PersianTokenizer();

        self::assertTrue($tokenizer->isStopword('نیست'));
        self::assertTrue($tokenizer->isStopword('می‌تواند'));
        self::assertTrue($tokenizer->isStopword('نمی‌توانند'));
        self::assertTrue($tokenizer->isStopword('می‌خوان'));
        self::assertFalse($tokenizer->isStopword('انرژی'));
    }

    public function test_it_filters_common_past_and_perfect_verb_inflections_without_a_dictionary_entry(): void
    {
        $tokenizer = new PersianTokenizer();

        self::assertTrue($tokenizer->isStopword('گرفته‌اند'));
        self::assertTrue($tokenizer->isStopword('کرده‌ایم'));
        self::assertTrue($tokenizer->isStopword('گرفتند'));
        self::assertTrue($tokenizer->isStopword('رسید'));
        self::assertFalse($tokenizer->isStopword('حادثه'));
        self::assertFalse($tokenizer->isStopword('آبرسانی'));
    }

    public function test_it_filters_low_signal_news_terms_without_removing_subject_terms(): void
    {
        $tokenizer = new PersianTokenizer(require __DIR__.'/../../resources/stopwords.php');

        self::assertSame(
            ['نفت', 'انرژی', 'خزر', 'اسرائیل'],
            $tokenizer->withoutStopwords(['نفت', 'یعنی', 'انرژی', 'روز', 'خزر', 'می‌گوید', 'اسرائیل']),
        );
    }

    public function test_it_keeps_known_letter_number_models_as_one_token(): void
    {
        $tokenizer = new PersianTokenizer([], ['اف', 'f']);

        self::assertSame(['اف ۳۵', 'و', 'f 35'], $tokenizer->tokenize('اف ۳۵ و F-35'));
    }

    public function test_the_bundled_stopword_dictionary_covers_common_function_word_variants(): void
    {
        $tokenizer = new PersianTokenizer(require __DIR__.'/../../resources/stopwords.php');

        self::assertSame(
            ['انرژی', 'سیاست'],
            $tokenizer->withoutStopwords(['هرچند', 'بااین‌حال', 'انرژی', 'می‌بایست', 'سیاست']),
        );
    }
}
