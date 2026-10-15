---
paths:
  - app/Domain/*/Actions/**
---

# Actions (lorisleiva/laravel-actions)

## Shape
Generate with `php artisan make:action 'App\Domain\{Domain}\Actions\{VerbNoun}'`. Use the `AsAction` trait. `handle()` holds the logic. It takes Data objects or models and returns a model, a Data object, or a paginator. Never take the raw `Request`.

## No helper methods
Do not add custom private, protected, or public methods. Put a reusable step in its own action and call it with `Step::run(...)`, or `Step::dispatch(...)` for queued work. That way every step can be tested and mocked on its own (`Step::mock()`, `Step::shouldRun()`).

## Allowed trait hooks
The only methods besides `handle()` are laravel-actions hooks. Each one only maps its input and calls `handle()`:
- `asController(SomeData $data, Model $model)`: laravel-data validates the Data object on injection. Return a Data object.
- `authorize(ActionRequest $request)`: delegate to the policy, `$request->user()->can('approve', $request->route('note'))`. Do not use `rules()`, because validation belongs in the Data class.
- `asJob`, `asListener`, `asCommand` (with `$commandSignature`), `configureJob`.

```php
public function asController(ArchiveNoteData $data, Note $note): NoteData
{
    return NoteData::from($this->handle($note, $data));
}
```

## Writes
Wrap multi-write actions in `DB::transaction()`. Lock rows you read and then update with `lockForUpdate()`. Enforce uniqueness with a DB unique index or `upsert`, not an `exists()` check. Queued actions must be idempotent.

## Filtering lists (spatie/laravel-query-builder v7)
List actions build their query with `QueryBuilder::for(Model::class)`. Whitelist every filter, sort, and include. The v7 methods are variadic, so do not pass arrays:

```php
return QueryBuilder::for(Note::class)
    ->allowedFilters(AllowedFilter::exact('user_id'), AllowedFilter::partial('title'), AllowedFilter::scope('active'))
    ->allowedSorts('created_at', 'title')
    ->allowedIncludes('user')
    ->paginate();
```

Put reusable filter logic in a model scope and expose it with `AllowedFilter::scope()`. Use a custom `Filter` class only when a scope can't express it.

## Tests
Unit-test `handle()`. Feature-test the route that `asController()` serves.
