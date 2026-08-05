<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Normalizers\PersianNormalizer;
use PHPUnit\Framework\TestCase;

final class PersianNormalizerTest extends TestCase
{
    public function test_it_normalizes_common_arabic_variants_and_digits(): void
    {
        $normalizer = new PersianNormalizer();

        self::assertSame('سلام دنیا، سال ۱۴۰۵', $normalizer->normalize("  سَلام\nدنيـا، سال 1405  "));
    }

    public function test_it_preserves_and_standardizes_the_persian_half_space(): void
    {
        $normalizer = new PersianNormalizer();

        self::assertSame('می‌روم', $normalizer->normalize("می \u{200C} روم"));
    }

    public function test_it_decodes_html_entities_and_removes_html_tags(): void
    {
        $normalizer = new PersianNormalizer();

        self::assertSame(
            'می‌روم به تهران',
            $normalizer->normalize('<p>می&zwnj;روم&nbsp;به <strong>تهران</strong></p>'),
        );
    }

    public function test_it_returns_an_empty_string_for_null_input(): void
    {
        self::assertSame('', (new PersianNormalizer())->normalize(null));
    }
}
