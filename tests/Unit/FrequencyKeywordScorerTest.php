<?php

declare(strict_types=1);

namespace PersianKeyword\Tests\Unit;

use PersianKeyword\Scoring\FrequencyKeywordScorer;
use PHPUnit\Framework\TestCase;

final class FrequencyKeywordScorerTest extends TestCase
{
    public function test_it_rewards_title_mentions_frequency_and_phrase_membership(): void
    {
        $scorer = new FrequencyKeywordScorer(['title_weight' => 2.0, 'body_weight' => 1.0, 'phrase_boost' => 1.0]);

        $scores = $scorer->score(['لاراول', 'آموزش'], ['لاراول', 'php'], ['آموزش لاراول'], [], [], 10);
        $byKeyword = [];
        foreach ($scores as $score) $byKeyword[$score->keyword()] = $score;

        self::assertSame(2, $byKeyword['لاراول']->frequency());
        self::assertSame(3.0, $byKeyword['لاراول']->score());
        self::assertSame(5.0, $byKeyword['آموزش لاراول']->score());
    }
}
