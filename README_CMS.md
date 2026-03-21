## Mini CMS (Personal + Artículos) — Laravel

Incluye:

- Admin `/admin` protegido por login
- CRUD de Personal y Artículos
- API pública
  - `GET /api/public/staff`
  - `GET /api/public/articles?page=1&size=9` (solo publicados)
  - `GET /api/public/articles/{slug}` (solo publicados)
- Generación de HTML estático para SEO (solo artículos publicados)
  - salida: `public/blog/{slug}/index.html`

### Storage para uploads (obligatorio si subís archivos)

Crear el symlink:

```bash
php artisan storage:link
```

Subidas:

- `storage/app/public/uploads/staff/*`
- `storage/app/public/uploads/articles/*`

Se exponen por:

- `/storage/uploads/staff/*`
- `/storage/uploads/articles/*`

### Usuario admin demo

Seeder crea:

- Email: `admin@example.com`
- Password: `password`

### Comandos

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Entrar a:

- `http://127.0.0.1:8000/login`
- `http://127.0.0.1:8000/admin/articles`
- `http://127.0.0.1:8000/admin/staff`

