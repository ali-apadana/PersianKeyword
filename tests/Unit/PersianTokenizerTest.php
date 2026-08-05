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
        self::assertFalse($tokenizer->isStopword('انرژی'));
    }

    public function test_it_filters_low_signal_news_terms_without_removing_subject_terms(): void
    {
        $tokenizer = new PersianTokenizer(require __DIR__.'/../../resources/stopwords.php');

        self::assertSame(
            ['نفت', 'انرژی', 'خزر', 'اسرائیل'],
            $tokenizer->withoutStopwords(['نفت', 'یعنی', 'انرژی', 'روز', 'خزر', 'می‌گوید', 'اسرائیل']),
        );
    }
}
