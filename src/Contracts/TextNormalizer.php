<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

interface TextNormalizer
{
    public function normalize(?string $text): string;
}
