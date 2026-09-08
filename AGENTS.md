# Mentor Forge AI development guide

This repository is a Laravel application currently deployed to Sakura Rental Server.
Treat the existing Laravel application and Sakura production environment as the source
of truth. Do not convert the current application to Next.js, Vercel, Cloudflare, or
Supabase in place.

## Required checks

Before proposing a merge, run:

```bash
composer ci:check
npm run build
```

## Production constraints

- Keep `composer.json` `config.platform.php` at `8.3.0`.
- Never use `--ignore-platform-req=php`.
- Never commit `.env`, credentials, host names, usernames, API keys, or database data.
- Keep `ROLEPLAY_AI_PROVIDER=scripted` unless the owner explicitly enables an API provider.
- Do not run `php artisan route:cache`; this application has a known Livewire/Flux incompatibility.
- Do not change the Sakura deployment layout or production database without an explicit migration and rollback plan.
- Do not deploy from an AI coding session. Prepare a PR and let the owner review CI first.

## Change discipline

- Work on a feature branch; do not push directly to `main`.
- Keep changes small enough for one-purpose review.
- Add or update tests for behavior changes.
- Preserve authorization boundaries: users may only access their own responses,
  reflections, follow-ups, and roleplay sessions.
- Treat all mentor/career-consultation content as potentially sensitive personal data.
- For infrastructure experiments, create a separate repository or isolated prototype.

## Review priorities

Review in this order:

1. Authentication, authorization, and personal-data exposure
2. Destructive database or deployment behavior
3. Regression risk in the case drill, diagnosis, and follow-up flows
4. Test coverage and error handling
5. Maintainability and presentation

Report concrete findings with file paths and suggested fixes. If no blocking issue is
found, say so explicitly and list remaining test gaps.
