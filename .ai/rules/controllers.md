---
paths:
  - app/Http/Controllers/**
  - routes/**
---

# Routes and controllers

## Route to actions
API routes live under the `v1` prefix and point straight at an action class: `Route::post('notes/{note}/archive', ArchiveNote::class)`. Use route model binding for models in the URL.

## Controllers are a last resort
Only add a controller in `app/Http/Controllers/Api/V1/` when an endpoint can't be a single action. Even then, it takes a Data object, calls actions, and returns a Data object. No queries, conditionals, loops, or model calls.
