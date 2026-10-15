<?php

namespace App\Domain\Notes\Actions;

use App\Models\Note;
use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteNote
{
    use AsAction;

    public function handle(Note $note): void
    {
        $note->delete();
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('delete', $request->route('note'));
    }

    public function asController(Note $note): Response
    {
        $this->handle($note);

        return response()->noContent();
    }
}
