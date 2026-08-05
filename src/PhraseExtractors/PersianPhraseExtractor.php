<?php

declare(strict_types=1);

namespace PersianKeyword\PhraseExtractors;

use PersianKeyword\Contracts\PhraseExtractor;
use PersianKeyword\Contracts\TextTokenizer;

final class PersianPhraseExtractor implements PhraseExtractor
{
    public function __construct(
        private readonly TextTokenizer $tokenizer,
        private readonly int $minTerms = 2,
        private readonly int $maxTerms = 3,
    ) {
    }

    /**
     * Build unique adjacent phrase candidates without crossing a stop word.
     *
     * @param list<string> $tokens
     * @return list<string>
     */
    public function extract(array $tokens): array
    {
        $phrases = [];
        $segment = [];

        foreach ($tokens as $token) {
            if ($this->tokenizer->isStopword($token)) {
                $this->addSegmentPhrases($phrases, $segment);
                $segment = [];

                continue;
            }

            $segment[] = $token;
        }

        $this->addSegmentPhrases($phrases, $segment);

        return array_keys($phrases);
    }

    /**
     * @param array<string, true> $phrases
     * @param list<string> $segment
     */
    private function addSegmentPhrases(array &$phrases, array $segment): void
    {
        $segmentLength = count($segment);

        for ($size = $this->minTerms; $size <= $this->maxTerms; $size++) {
            for ($start = 0; $start <= $segmentLength - $size; $start++) {
                $phrases[implode(' ', array_slice($segment, $start, $size))] = true;
            }
        }
    }
}
