---
paths:
  - app/Domain/*/Data/**
---

# Data objects (spatie/laravel-data v4)

## One class per purpose
Generate with `php artisan make:data {Name} --namespace='Domain\{Domain}\Data'`. Name input classes after the use case (`ArchiveNoteData`) and output classes after the model (`NoteData`). Use constructor-promoted public properties. Keep them free of business logic.

## Validation
Put validation on the properties with laravel-data attributes (`#[Max(120)]`, `#[Exists('users', 'id')]`, `#[Unique('users', 'email')]`, and so on). Use a static `rules()` method only for rules that attributes can't express. Do not create FormRequests.

## Optional vs nullable
For PATCH-style updates, type a field as `string|Optional` when "not sent" must differ from "sent as null".

## Responses
Actions return Data objects to the client. Use `NoteData::from($model)` for one model and `NoteData::collect($paginator)` for lists. Use `Lazy` properties for relations that should only load when included. Do not create API Resources.

## TypeScript types
Mark every Data class the frontend consumes with `#[TypeScript]` (`Spatie\TypeScriptTransformer\Attributes\TypeScript`). Regenerate the types with `php artisan typescript:transform`.

## Translatable fields
Type spatie/laravel-translatable columns as `string` (the current locale) in output Data. Type them as `array<string, string>` (keyed by locale) in admin input Data.
