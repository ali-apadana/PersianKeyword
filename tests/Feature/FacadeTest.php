<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Feature;

use PersianKeyword\Facades\Keyword;
use PersianKeyword\Tests\TestCase;

final class FacadeTest extends TestCase
{
    public function test_it_returns_a_structured_foundation_result(): void
    {
        $result = Keyword::extract('نمونه عنوان', 'نمونه متن');

        self::assertSame([], $result->keywords());
        self::assertSame('normalization-ready', $result->meta()['status']);
        self::assertSame('نمونه عنوان', $result->meta()['normalized_title']);
    }
}
