<?php

namespace App\Domain\Notes\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function delete(User $user, Note $note): bool
    {
        return $note->user_id === $user->id;
    }
}
