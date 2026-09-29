# Web4rtTech (single Laravel app)

The whole site in one Laravel 9 app: the public website and the admin panel are
Blade views, so it runs on ordinary shared hosting (PHP + MySQL, no Node.js).
It uses the same MySQL database as the previous React + Laravel version.

## Where things are

- `routes/web.php` — public pages and the admin panel (`/admin`, session login).
- `routes/api.php` — the old JSON API, kept working but no longer used by the site.
- `config/site.php` — services, pricing, FAQs, ventures and fallback news articles.
- `app/Support/Meta.php` — admin labels/colours, money/date formatting, and the
  company details printed on invoices (fill in GSTIN and bank details there).
- `app/Http/Controllers/Site` — public site. `app/Http/Controllers/Admin` — admin pages;
  they call the existing API controllers, so validation and business rules are shared.
- `resources/views/pages` — public pages; `resources/views/admin` — admin pages.
- `public/css/app.css` — all styles (same CSS as the React version).
- `public/js/site.js`, `public/js/admin.js` — small scripts (menu, slider, filters,
  line items, charts). Chart.js is self-hosted in `public/js/vendor`.

## Local development

```bash
C:\xampp2\php\php.exe artisan serve --port=8000
```

Admin login: `/admin/login` with an existing user (roles: admin, sales, finance, developer).

## Deploying to shared hosting

1. Upload the whole folder (including `vendor/`).
2. Best: point the domain's document root at `public/`. If the host doesn't allow
   that, leave it at the project folder; the root `.htaccess` forwards requests to `public/`.
3. Create `.env` from `.env.example`: set `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL=https://web4rttech.com`, the `DB_*` details and the `MAIL_*` settings.
4. Run once (via SSH or the host's terminal): `php artisan key:generate` (new install only),
   `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`,
   `php artisan view:cache`.
5. Make `storage/` and `bootstrap/cache/` writable.
6. Daily admin digest email: add a cron job `* * * * * php /path/to/artisan schedule:run`.

## Tests

`php artisan test --filter=BladeAdminSmokeTest` logs in and exercises every admin page
and the main create/edit/delete flows inside a database transaction that is rolled back.
