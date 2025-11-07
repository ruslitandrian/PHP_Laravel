# Laravel PHP Project

This is a complete PHP application built with the Laravel framework showcasing core features and best practices.

## 🚀 Features

- **MVC Architecture**: Built with the Model-View-Controller pattern
- **RESTful API**: Complete API routes and controllers
- **Database Integration**: Eloquent ORM and database migrations
- **Modern UI**: Bootstrap 5 and Font Awesome icons
- **Form Validation**: Built-in validation
- **User Management**: Full CRUD example

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL/SQLite
- Web server (Apache/Nginx)

## 🛠️ Setup

### 1. Install Composer

If Composer is not installed on your system, install it first:

**Windows:**
```bash
# Download and run the Composer installer
# https://getcomposer.org/download/
```

**macOS:**
```bash
brew install composer
```

**Linux:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Copy environment file:
```bash
copy env.example .env
```

Edit `.env` to configure your database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate app key

```bash
php artisan key:generate
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Seed sample data

```bash
php artisan db:seed
```

### 7. Start the dev server

```bash
php artisan serve
```

The app will be available at `http://localhost:8000`.

## 📁 Project Structure

```
laravel/
├── app/                    # Application core
│   ├── Http/              # HTTP
│   │   ├── Controllers/   # Controllers
│   │   └── Middleware/    # Middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── config/                # Configuration
├── database/              # Database
│   ├── migrations/        # Migrations
│   ├── seeders/           # Seeders
│   └── factories/         # Model factories
├── public/                # Public assets
├── resources/             # Frontend assets
│   └── views/             # Blade templates
├── routes/                # Route definitions
└── storage/               # Storage
```

## 🎯 Features Overview

### Home Page
- Introduces the application
- Provides navigation to other features

### About Page
- Highlights Laravel features
- Explains the project structure

### User Management
- **List**: Display all users
- **Create**: Create a new user
- **Details**: View user details
- **Edit**: Update user data
- **Delete**: Remove a user record

### API Endpoints
See `BLOG_API_DOCUMENTATION.md` for full details.

- `GET /api/blogs` - List blogs (search, filter, sort, paginate)
- `POST /api/blogs` - Create a blog
- `GET /api/blogs/{id}` - Get a blog (increments views)
- `PUT/PATCH /api/blogs/{id}` - Update a blog
- `DELETE /api/blogs/{id}` - Delete a blog (soft delete)
- `POST /api/blogs/{id}/set-active` - Set active
- `POST /api/blogs/{id}/set-inactive` - Set inactive
- `PUT /api/blogs/{id}/order` - Update order
- `POST /api/blogs/bulk-update-order` - Bulk update order

## 🔧 Development Commands

### Artisan commands

```bash
# Create a controller
php artisan make:controller ControllerName

# Create a model
php artisan make:model ModelName

# Create a migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🎨 Frontend Tech

- **Bootstrap 5**: Responsive UI framework
- **Font Awesome**: Icon library
- **Blade**: Laravel's templating engine

## 📊 Database Design

### Users table
- `id`: Primary key
- `name`: User name
- `email`: Email (unique)
- `password`: Password (hashed)
- `email_verified_at`: Email verification time
- `remember_token`: Remember token
- `created_at`: Created time
- `updated_at`: Updated time

## 🔒 Security

- CSRF protection
- SQL injection protection
- XSS protection
- Password hashing
- Form validation

## 🚀 Deployment

### Production settings

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure database
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Run `php artisan view:cache`

## 📝 Resources

- [Laravel Docs](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Blade Templates](https://laravel.com/docs/blade)

## 🤝 Contributing

Issues and PRs are welcome.

## 📄 License

This project is licensed under the MIT License.

---

**Author**: Laravel Team  
**Version**: 1.0.0  
**Last Updated**: 2024
