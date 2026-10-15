---
paths:
  - app/Models/**
  - app/Domain/*/Policies/**
  - database/migrations/**
---

# Models and policies

## Global, thin models
All models live in `app/Models`, shared across domains. They hold relations, casts, scopes, and package traits only. Business logic goes in domain actions. Reusable query filters go in scopes, so laravel-query-builder can expose them with `AllowedFilter::scope()`.

## Policies live in the domain
Policies go in `app/Domain/{Domain}/Policies`. Laravel won't auto-discover them there, so register each one on its model with `#[UsePolicy(NotePolicy::class)]`.

## Translations (spatie/laravel-translatable v6)
List translated columns in `public array $translatable` with the `HasTranslations` trait. Store those columns as `json` in migrations. Read them with `$model->title` (current locale) or `getTranslation('title', 'ar')`. Do not hand-roll `title_en` / `title_ar` columns.

## Audit log (spatie/laravel-activitylog v5)
Models that need an audit trail use `Spatie\Activitylog\Models\Concerns\LogsActivity` and define `getActivitylogOptions()` returning `LogOptions::defaults()->logOnly([...])->logOnlyDirty()->dontLogEmptyChanges()`. Never log secrets, OTPs, or tokens. For events that aren't model changes, log explicitly with `activity()` inside the action.

## Roles and permissions (spatie/laravel-permission v8)
Use `HasRoles` on `User` only. The admin roles are owner and support (see `User::ADMIN_ROLES` and `AdminRoleSeeder`). Check access through policies that call `$user->can('permission')`. Check permissions, not role names, outside of seeders.
