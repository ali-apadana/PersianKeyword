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
}
