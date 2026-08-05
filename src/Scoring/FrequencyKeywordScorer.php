<?php

declare(strict_types=1);

namespace PersianKeyword\Scoring;

use PersianKeyword\Contracts\KeywordScorer;
use PersianKeyword\DTO\KeywordScore;

final class FrequencyKeywordScorer implements KeywordScorer
{
    /** @param array{title_weight?: float, body_weight?: float, phrase_boost?: float} $options */
    public function __construct(private readonly array $options = [], private readonly int $minKeywordLength = 2)
    {
    }

    /**
     * @param list<string> $titleTokens
     * @param list<string> $bodyTokens
     * @param list<string> $titlePhrases
     * @param list<string> $bodyPhrases
     * @return list<KeywordScore>
     */
    public function score(array $titleTokens, array $bodyTokens, array $titlePhrases, array $bodyPhrases, int $limit): array
    {
        /** @var array<string, array{score: float, frequency: int}> $scores */
        $scores = [];
        $this->addTokens($scores, $titleTokens, (float) ($this->options['title_weight'] ?? 2.0));
        $this->addTokens($scores, $bodyTokens, (float) ($this->options['body_weight'] ?? 1.0));

        $boost = (float) ($this->options['phrase_boost'] ?? 1.0);
        $this->addPhraseBoost($scores, $titlePhrases, $boost);
        $this->addPhraseBoost($scores, $bodyPhrases, $boost);

        $ranked = [];
        foreach ($scores as $keyword => $data) {
            $ranked[] = new KeywordScore($keyword, $data['score'], $data['frequency']);
        }

        usort($ranked, function (KeywordScore $left, KeywordScore $right): int {
            $byScore = $right->score() <=> $left->score();
            if ($byScore !== 0) return $byScore;
            $byFrequency = $right->frequency() <=> $left->frequency();
            return $byFrequency !== 0 ? $byFrequency : strcmp($left->keyword(), $right->keyword());
        });

        return array_slice($ranked, 0, max(0, $limit));
    }

    /** @param array<string, array{score: float, frequency: int}> $scores @param list<string> $tokens */
    private function addTokens(array &$scores, array $tokens, float $weight): void
    {
        foreach ($tokens as $token) {
            if ($this->length($token) < $this->minKeywordLength) continue;
            $scores[$token] ??= ['score' => 0.0, 'frequency' => 0];
            $scores[$token]['score'] += $weight;
            $scores[$token]['frequency']++;
        }
    }

    /** @param array<string, array{score: float, frequency: int}> $scores @param list<string> $phrases */
    private function addPhraseBoost(array &$scores, array $phrases, float $boost): void
    {
        foreach ($phrases as $phrase) {
            $terms = explode(' ', $phrase);
            foreach ($terms as $term) {
                if (isset($scores[$term])) $scores[$term]['score'] += $boost / count($terms);
            }
        }
    }

    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : (preg_match_all('/./u', $value) ?: 0);
    }
}
