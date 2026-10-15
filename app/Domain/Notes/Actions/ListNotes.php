<?php

namespace App\Domain\Notes\Actions;

use App\Domain\Notes\Data\NoteData;
use App\Models\Note;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListNotes
{
    use AsAction;

    public function handle(User $user): CursorPaginator
    {
        return QueryBuilder::for(Note::class)
            ->where('user_id', $user->id)
            ->allowedFilters(AllowedFilter::partial('title'))
            ->allowedSorts('created_at', 'title')
            ->defaultSort('-created_at', '-id')
            ->cursorPaginate();
    }

    public function asController(ActionRequest $request): CursorPaginatedDataCollection
    {
        return NoteData::collect($this->handle($request->user()), CursorPaginatedDataCollection::class);
    }
}
