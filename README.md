# Language Learning Platform — Backend & Admin Panel

Backend and content-management admin panel for a game-based English-learning mobile app (similar in spirit to Duolingo). Learners go through seasons, episodes and chapters made of interactive dialogues, theory pages and mini-games; this service stores all of that content and serves it to the mobile client through a JSON API.

I built this backend from scratch while working as a backend developer at Langly (2022–2025): server setup, PostgreSQL schema, REST API and the admin panel used by the content team. The company has since closed; this repository is published as a portfolio project.

## Tech stack

- **PHP 8 / Laravel 8**
- **PostgreSQL** — 45 Eloquent models, 150+ migrations
- **Auth:** Laravel Sanctum, JWT (`tymon/jwt-auth`), Google OAuth sign-in for admins, role-based access with `spatie/laravel-permission`
- **Search:** Laravel Scout + TNTSearch
- **Storage:** local disk, SFTP and Google Cloud Storage (`league/flysystem-sftp`, GCS adapter)
- **Caching:** `spatie/laravel-responsecache`
- **Import / export:** Excel via `maatwebsite/excel` (dialogues, texts, arcade words)
- **Monitoring:** exceptions are reported to a Telegram bot (`app/Services/ExceptionToTelegram.php`)
- **External APIs:** Oxford Learner's Dictionaries (word data)
- **Docker:** Laravel Sail runtimes for PHP 7.4 / 8.0 / 8.1 (`docker/`)

## What the system does

| Area | Description |
| --- | --- |
| Course structure | Seasons → Episodes → Chapters → Sub-chapters, each with images, backgrounds and lock states |
| Dialogues | Branching dialogue scenes with characters (personages), emotions, backgrounds and audio |
| Theory & practice | Theory pages, practice pages and quizzes linked to dialogues |
| Mini-games | Several game types (word matching, insert/type words, true-or-false, "millionaire", make-a-sentence, arcade games) with templates and descriptions |
| Words | Vocabulary with audio, images and data imported from the Oxford dictionary API |
| Localisation | Multiple interface languages for texts, subtitles and game descriptions |
| Admin panel | Blade-based UI for the content team to create and edit all of the above, upload media and manage users and roles |
| JSON generation | Artisan commands (`app/Console/Commands`) that pre-build JSON for dialogues, games and info screens consumed by the app |

Media files (images, audio, video) are processed and served by a separate service: **[media-service](https://github.com/DarkRedColor/media-service)**.

API documentation for the mobile client lives in [`docs/api`](docs/api).

## Project structure

```
app/
  Console/Commands/   JSON generators and maintenance commands
  Exports/ Imports/   Excel import/export
  Http/Controllers/   admin panel + API controllers
  Http/Middleware/    role checks (IsAdmin, IsUser, IsGuest), JWT
  Models/             Eloquent models
  Services/           Telegram exception reporting
database/migrations/  PostgreSQL schema history
resources/views/      admin panel (Blade)
routes/web.php        admin and API routes
docs/api/             endpoint documentation and sample responses
tests/Feature/        API response tests
```

## Running locally

```bash
cp .env.example .env          # fill in DB credentials
composer install
mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs
php artisan key:generate
php artisan migrate
php artisan db:seed           # languages and roles
php artisan serve
```

Run the tests:

```bash
php artisan test
```

## What I would improve today

The product requirements changed very often (Agile, short iterations), so the code grew quickly and carries some technical debt:

- Several controllers are large (e.g. `GameController`, `DialoguePagesController`); business logic should move into service classes.
- `routes/web.php` holds both admin and API routes; I would split them into separate route files and version the API.
- Test coverage is limited to API response tests; I would add unit tests around the game and dialogue logic.
- Some media paths are tied to the original server layout and should be fully configurable.
