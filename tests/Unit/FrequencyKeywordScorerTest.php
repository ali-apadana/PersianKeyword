<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Scoring\FrequencyKeywordScorer;
use PHPUnit\Framework\TestCase;

final class FrequencyKeywordScorerTest extends TestCase
{
    public function test_it_prioritizes_a_title_phrase_over_its_individual_terms(): void
    {
        $scorer = new FrequencyKeywordScorer(['title_weight' => 2.0, 'body_weight' => 1.0, 'phrase_boost' => 1.0]);

        $scores = $scorer->score(['لاراول', 'آموزش'], ['لاراول', 'php'], ['آموزش لاراول'], [], [], 10);
        $byKeyword = [];
        foreach ($scores as $score) $byKeyword[$score->keyword()] = $score;

        self::assertSame('آموزش لاراول', $scores[0]->keyword());
        self::assertSame(5.0, $byKeyword['آموزش لاراول']->score());
        self::assertArrayNotHasKey('لاراول', $byKeyword);
    }
}
