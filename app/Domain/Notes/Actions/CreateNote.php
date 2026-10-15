<?php

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Data\CreateNoteData;
use App\Domain\Notes\Data\NoteData;
use App\Models\Note;
use App\Models\User;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNote
{
    use AsAction;

    public function handle(User $user, CreateNoteData $data): Note
    {
        return $user->notes()->create($data->toArray())->refresh();
    }

    public function asController(ActionRequest $request, CreateNoteData $data): NoteData
    {
        return NoteData::from($this->handle($request->user(), $data));
    }
}
