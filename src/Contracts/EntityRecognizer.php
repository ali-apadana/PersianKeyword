<?php

declare(strict_types=1);

namespace PersianKeyword\Contracts;

use PersianKeyword\DTO\Entity;

interface EntityRecognizer
{
    /** @return list<Entity> */
    public function recognize(string $text, string $source): array;
}
