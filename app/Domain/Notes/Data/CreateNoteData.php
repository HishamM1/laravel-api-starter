<?php

namespace App\Domain\Notes\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CreateNoteData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        #[Max(10000)]
        public ?string $body = null,
    ) {}
}
