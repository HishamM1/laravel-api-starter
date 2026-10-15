<?php

namespace App\Filament\Concerns;

use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

trait AuthorizesWithPermission
{
    abstract protected static function permission(): string;

    protected static function isReadOnly(): bool
    {
        return false;
    }

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        $user = Filament::auth()->user();

        if ($user === null || ! $user->can(static::permission())) {
            return Response::deny();
        }

        if (static::isReadOnly() && ! in_array($action, ['viewAny', 'view'], true)) {
            return Response::deny();
        }

        return Response::allow();
    }
}
