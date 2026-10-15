<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;
use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RevokeToken
{
    use AsAction;

    public function handle(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function asController(ActionRequest $request): Response
    {
        $this->handle($request->user());

        return response()->noContent();
    }
}
