<?php

namespace App\Domain\Notes\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class NoteData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $body,
        public CarbonImmutable $createdAt,
    ) {}
}
