<?php

declare(strict_types=1);

namespace PersianKeyword\DTO;

use JsonSerializable;

final readonly class Entity implements JsonSerializable
{
    public function __construct(private string $name, private string $type, private string $source)
    {
    }

    public function name(): string { return $this->name; }
    public function type(): string { return $this->type; }
    public function source(): string { return $this->source; }

    /** @return array{name: string, type: string, source: string} */
    public function toArray(): array
    {
        return ['name' => $this->name, 'type' => $this->type, 'source' => $this->source];
    }

    /** @return array{name: string, type: string, source: string} */
    public function jsonSerialize(): array { return $this->toArray(); }
}
