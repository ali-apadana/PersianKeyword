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
}
