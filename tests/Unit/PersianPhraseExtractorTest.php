<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\PhraseExtractors\PersianPhraseExtractor;
use PersianKeyword\Tokenizers\PersianTokenizer;
use PHPUnit\Framework\TestCase;

final class PersianPhraseExtractorTest extends TestCase
{
    public function test_it_extracts_adjacent_candidates_without_crossing_stopwords(): void
    {
        $extractor = new PersianPhraseExtractor(new PersianTokenizer(['برای']));

        self::assertSame(
            ['آموزش لاراول', 'برنامه نویسی'],
            $extractor->extract(['آموزش', 'لاراول', 'برای', 'برنامه', 'نویسی']),
        );
    }

    public function test_it_generates_bigrams_and_trigrams_and_removes_duplicates(): void
    {
        $extractor = new PersianPhraseExtractor(new PersianTokenizer(), 2, 3);

        self::assertSame(
            [
                'برنامه نویسی',
                'نویسی لاراول',
                'لاراول برنامه',
                'برنامه نویسی لاراول',
                'نویسی لاراول برنامه',
                'لاراول برنامه نویسی',
            ],
            $extractor->extract(['برنامه', 'نویسی', 'لاراول', 'برنامه', 'نویسی']),
        );
    }
}
