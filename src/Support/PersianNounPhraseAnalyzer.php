<?php

declare(strict_types=1);

namespace PersianKeyword\Support;

final class PersianNounPhraseAnalyzer
{
    /** @var list<string> */
    private const BRIDGE_TERMS = ['توسط', 'برای', 'از', 'با', 'به', 'در', 'که', 'و', 'یا'];

    /** @param list<string> $terms */
    public function isMeaningful(array $terms): bool
    {
        if (count($terms) < 2) return false;

        foreach ($terms as $term) {
            if (in_array($term, self::BRIDGE_TERMS, true) || $this->looksLikeVerb($term)) return false;
        }

        return true;
    }

    private function looksLikeVerb(string $term): bool
    {
        return preg_match('/^(?:ن?می‌?)?(?:شود|شد|شده|شدند|کرد|کرده|کنند|آمد|آمدند|پیوست|گرفت|گرفته)(?:م|ی|یم|ید|ند)?$/u', $term) === 1;
    }
}
