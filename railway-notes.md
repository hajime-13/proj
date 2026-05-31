# Railway Deployment Guide

This app uses a **Dockerfile** for Railway deployment (PHP 8.2 + Nginx + PHP-FPM on Alpine).

---

## Step-by-step: Deploy to Railway

### 1. Add a MySQL database service
In your Railway project, click **+ New** → **Database** → **MySQL**.  
Railway will auto-set these variables in your service:

| Variable | Value |
|---|---|
| `MYSQLHOST` | (auto) |
| `MYSQLPORT` | (auto) |
| `MYSQLDATABASE` | (auto) |
| `MYSQLUSER` | (auto) |
| `MYSQLPASSWORD` | (auto) |

### 2. Set environment variables on your Laravel service
In your Laravel service → **Variables**, add:

```
APP_NAME=OrderList
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    ← generate with: php artisan key:generate --show
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
LOG_LEVEL=error
```

> Use Railway's **reference variables** syntax `${{MySQL.MYSQLHOST}}` to link the DB service.

### 3. Deploy
Push to your connected GitHub repo. Railway will:
1. Build the Docker image (`npm install`, `npm run build`, `composer install`)
2. Run `docker/start.sh` which:
   - Runs `php artisan migrate --force`
   - Creates the storage symlink
   - Caches config/routes/views
   - Starts PHP-FPM + Nginx

### 4. Seed initial data (optional)
After first deploy, open Railway's **Shell** tab and run:
```bash
php artisan db:seed
```
This creates a test user: `test@example.com` / `password`

### 5. File uploads
Profile pictures are stored in `storage/app/public/profile_pictures`.  
On Railway, the container filesystem is **ephemeral** — uploads will be lost on redeploy.

**For persistent uploads**, add a Railway Volume:
- Mount path: `/var/www/app/storage`

Or switch to S3 by setting `FILESYSTEM_DISK=s3` and adding AWS credentials.

---

## Local development (XAMPP)

1. Copy `.env.example` to `.env` and set:
   ```
   APP_ENV=local
   APP_DEBUG=true
   DB_CONNECTION=sqlite
   SESSION_DRIVER=database
   CACHE_STORE=database
   ```
2. Run:
   ```bash
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   npm install && npm run build
   ```
3. Visit `http://localhost/final/public`
