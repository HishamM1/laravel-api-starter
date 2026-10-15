<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Data\CreateTokenData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateToken
{
    use AsAction;

    public function handle(CreateTokenData $data): array
    {
        $user = User::query()->where('email', strtolower($data->email))->first();

        if ($user === null || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        return ['token' => $user->createToken($data->deviceName)->plainTextToken];
    }

    public function asController(CreateTokenData $data): array
    {
        return $this->handle($data);
    }
}
