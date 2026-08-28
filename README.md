# Laravel Simple Blog API

A small **Laravel 10** REST starter for a blog: Sanctum token auth, categories, and posts. It is meant as a teaching/demo kit, not a production CMS.

## What you get

- `POST /api/register`, `POST /api/login`, `POST /api/logout`
- Public, paginated `GET /api/posts` (published posts only, optional `?category=` slug filter)
- Public `GET /api/posts/{slug}` (published only)
- Authenticated `POST` / `PUT` / `DELETE` for posts and categories
- Public `GET /api/categories`
- Seeded demo user, two categories, four published posts, and one draft

## Requirements

- PHP 8.1 or 8.2
- Composer
- SQLite (tests) or MySQL (local/Docker)

This project stays on **Laravel 10**. It is not upgraded to Laravel 11/12.

## Install

```bash
git clone https://github.com/Petar-V-Nikolov/laravel-simple-blog-api.git
cd laravel-simple-blog-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The API is served under `/api` (Laravel’s default prefix). After seeding, you can log in as:

- email: `api@example.com`
- password: `password`

## Example requests

Login and copy the `token` from the JSON body:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"api@example.com","password":"password"}'
```

List published posts:

```bash
curl http://127.0.0.1:8000/api/posts \
  -H "Accept: application/json"
```

Create a post (replace `TOKEN`):

```bash
curl -X POST http://127.0.0.1:8000/api/posts \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{"title":"My post","excerpt":"A short summary","body":"The full text","status":"published"}'
```

Filter by category slug:

```bash
curl "http://127.0.0.1:8000/api/posts?category=laravel" \
  -H "Accept: application/json"
```

## Docker (optional)

A `docker-compose.yml` is included (Nginx on port **8080**, MySQL, PHP-FPM). It is optional and a bit larger than this API needs (Meilisearch, Mailpit, and Selenium are leftover services and unused by the blog endpoints).

If you use Compose, point the app at the `mysql` service:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel_simple_blog_api
DB_USERNAME=root
DB_PASSWORD=secret
```

Then:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

The API will be at `http://localhost:8080/api`.

## Tests

```bash
php artisan test
```

CI runs the same command on PHP 8.2 (see `.github/workflows/tests.yml`). Tests use SQLite in memory and an `APP_KEY` from `phpunit.xml`.

## Limitations

- Laravel 10 only — no upgrade path is maintained here.
- No comments, tags, media uploads, or roles/permissions.
- Any authenticated user may create, update, or delete any post or category (MVP; there is no owner-only policy).
- CORS uses Laravel’s default `config/cors.php` (including `allowed_origins` of `*`). Tighten that before exposing the API to browsers in production.
- No email verification, password reset, or rate-limit customization beyond Laravel defaults.

## License

MIT © 2026 Petar Nikolov / PN Scripts
