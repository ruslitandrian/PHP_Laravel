# Docker Setup Guide

This Laravel project includes a Docker environment with the following services:

## Services

- **app**: Laravel application (PHP 8.1-FPM)
- **nginx**: Web server
- **db**: MySQL 8.0 database
- **redis**: Redis cache
- **mailpit**: Mail testing tool

## Quick Start

### 1. Copy environment file
```bash
cp .env.example .env
```

### 2. Configure database in .env
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=root

REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Build and start containers
```bash
docker-compose up -d --build
```

### 4. Install dependencies and set up the app
```bash
# Enter the app container
docker-compose exec app bash

# Install Composer dependencies
composer install

# Generate app key
php artisan key:generate

# Run database migrations
php artisan migrate

# Install frontend dependencies and build
npm install
npm run build
```

## Access

- **Application**: http://localhost:8000
- **Mailpit**: http://localhost:8025

## Common Commands

### Enter container
```bash
docker-compose exec app bash
```

### View logs
```bash
docker-compose logs -f app
docker-compose logs -f nginx
```

### Stop services
```bash
docker-compose down
```

### Rebuild
```bash
docker-compose up -d --build
```

### Clean database volumes
```bash
docker-compose down -v
```

## Development Mode

For hot-reload style development (auto file sync):

```bash
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

## Production

When deploying to production, ensure:

1. Set `APP_DEBUG=false` in `.env`
2. Use strong passwords
3. Configure SSL certificates
4. Use production-grade database settings

## Troubleshooting

### Permission issues
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/bootstrap/cache
```

### Clear caches
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```
