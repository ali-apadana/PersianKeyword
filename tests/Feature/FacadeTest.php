<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Feature;

use PersianKeyword\Facades\Keyword;
use PersianKeyword\Tests\TestCase;

final class FacadeTest extends TestCase
{
    public function test_it_returns_a_structured_foundation_result(): void
    {
        $result = Keyword::extract('نمونه عنوان', 'این یک نمونه متن است');

        self::assertSame([], $result->keywords());
        self::assertSame(['نمونه', 'عنوان', 'نمونه', 'متن'], $result->tokens());
        self::assertSame('tokenization-ready', $result->meta()['status']);
        self::assertSame('نمونه عنوان', $result->meta()['normalized_title']);
    }
}
