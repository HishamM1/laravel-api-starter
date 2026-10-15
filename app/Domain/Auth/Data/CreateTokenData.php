<?php

namespace App\Domain\Auth\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class CreateTokenData extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        public string $password,
        #[Max(255)]
        public string $deviceName,
    ) {}
}
