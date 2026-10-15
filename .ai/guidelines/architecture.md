## Architecture

Code is grouped by domain:

```
app/
  Domain/{Auth,Notes,...}/
    Actions/    lorisleiva/laravel-actions, one class per use case
    Data/       spatie/laravel-data DTOs (input, validation, output)
    Events/
    Policies/
  Models/       all Eloquent models, shared across domains
  Http/Controllers/Api/V1/   only for endpoints that can't be a single action
  Filament/     super admin
  Notifications/
```

- Routes point straight at actions (`Route::post('notes/{note}/archive', ArchiveNote::class)`). Controllers hold no logic.
- All business logic lives in actions. An action has `handle()` plus laravel-actions hooks only, and no custom helper methods. When logic needs a helper, extract it into its own action and call it with `OtherAction::run(...)`.
- `spatie/laravel-data` handles all DTOs, validation, and API responses. Do not create FormRequests or API Resources.
- List endpoints filter, sort, and include through `spatie/laravel-query-builder` inside the action.
- Create files with the generators, using full domain namespaces:
  - `php artisan make:action 'App\Domain\Notes\Actions\ArchiveNote'`
  - `php artisan make:data ArchiveNoteData --namespace='Domain\Notes\Data'`
  - `php artisan make:policy 'App\Domain\Notes\Policies\NotePolicy' --model=Note`
  - `php artisan make:model Note` (models stay in `app/Models`)
- Read the matching file in `.ai/rules` before editing an action, Data class, route, policy, or model.
