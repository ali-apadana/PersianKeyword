<?php

declare(strict_types=1);

namespace PersianKeyword\Scoring;

use PersianKeyword\Contracts\KeywordScorer;
use PersianKeyword\DTO\KeywordScore;

final class FrequencyKeywordScorer implements KeywordScorer
{
    /** @var list<string> */
    private const GENERIC_HEADS = [
        'سیستم', 'سامانه', 'بخش', 'روند', 'برنامه', 'طرح', 'موضوع', 'مورد',
        'موارد', 'وضعیت', 'فرآیند', 'اقدام', 'ایالت', 'شهرستان',
    ];

    /** @param array{title_weight?: float, body_weight?: float, title_phrase_weight?: float, body_phrase_weight?: float, entity_boost?: float} $options */
    public function __construct(private readonly array $options = [], private readonly int $minKeywordLength = 2)
    {
    }

    /**
     * @param list<string> $titleTokens
     * @param list<string> $bodyTokens
     * @param list<string> $titlePhrases
     * @param list<string> $bodyPhrases
     * @param list<string> $entityNames
     * @return list<KeywordScore>
     */
    public function score(
        array $titleTokens,
        array $bodyTokens,
        array $titlePhrases,
        array $bodyPhrases,
        array $entityNames,
        int $limit,
    ): array
    {
        /** @var array<string, array{score: float, frequency: int}> $scores */
        $scores = [];
        $this->addTokens($scores, $titleTokens, (float) ($this->options['title_weight'] ?? 2.0));
        $this->addTokens($scores, $bodyTokens, (float) ($this->options['body_weight'] ?? 1.0));

        $this->addPhrases($scores, $titlePhrases, (float) ($this->options['title_phrase_weight'] ?? 5.0));
        $this->addPhrases($scores, $bodyPhrases, (float) ($this->options['body_phrase_weight'] ?? 2.0));
        $this->addEntities($scores, $entityNames, (float) ($this->options['entity_boost'] ?? 3.0));

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

        return $this->selectNonOverlapping($ranked, $limit);
    }

    /** @param array<string, array{score: float, frequency: int}> $scores @param list<string> $tokens */
    private function addTokens(array &$scores, array $tokens, float $weight): void
    {
        foreach ($tokens as $token) {
            if (! $this->isEligibleToken($token)) continue;
            $scores[$token] ??= ['score' => 0.0, 'frequency' => 0];
            $scores[$token]['score'] += $weight;
            $scores[$token]['frequency']++;
        }
    }

    /** @param array<string, array{score: float, frequency: int}> $scores @param list<string> $phrases */
    private function addPhrases(array &$scores, array $phrases, float $weight): void
    {
        foreach ($phrases as $phrase) {
            $terms = explode(' ', $phrase);

            if (! $this->isEligiblePhrase($terms)) continue;

            $scores[$phrase] ??= ['score' => 0.0, 'frequency' => 0];
            $scores[$phrase]['score'] += $weight;
            $scores[$phrase]['frequency']++;
        }
    }

    /** @param array<string, array{score: float, frequency: int}> $scores @param list<string> $entityNames */
    private function addEntities(array &$scores, array $entityNames, float $boost): void
    {
        foreach ($entityNames as $entity) {
            if (! $this->isEligibleToken($entity)) continue;
            $scores[$entity] ??= ['score' => 0.0, 'frequency' => 0];
            $scores[$entity]['score'] += $boost;
        }
    }

    private function isEligibleToken(string $token): bool
    {
        return $this->length($token) >= $this->minKeywordLength
            && preg_match('/^\p{N}+$/u', $token) !== 1
            && ! $this->isGenericHead($token);
    }

    /** @param list<string> $terms */
    private function isEligiblePhrase(array $terms): bool
    {
        if (count($terms) < 2 || $this->isGenericHead($terms[0])) return false;

        foreach ($terms as $term) {
            if (! $this->isEligibleToken($term)) return false;
        }

        return true;
    }

    private function isGenericHead(string $token): bool
    {
        $stem = preg_replace('/(?:\x{200C})?(?:های|ها)$/u', '', $token) ?? $token;

        return in_array($stem, self::GENERIC_HEADS, true);
    }

    /** @param list<KeywordScore> $ranked @return list<KeywordScore> */
    private function selectNonOverlapping(array $ranked, int $limit): array
    {
        $selected = [];
        $coveredTerms = [];

        foreach ($ranked as $candidate) {
            $terms = explode(' ', $candidate->keyword());

            if (count($terms) === 1 && isset($coveredTerms[$terms[0]])) continue;

            $selected[] = $candidate;
            if (count($terms) > 1) {
                foreach ($terms as $term) $coveredTerms[$term] = true;
            }

            if (count($selected) >= max(0, $limit)) break;
        }

        return $selected;
    }

    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : (preg_match_all('/./u', $value) ?: 0);
    }
}
