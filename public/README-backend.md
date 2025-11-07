# Pure PHP Backend Solution - Table Search, Sort, Pagination

## Files

1. **table-backend.php** - Main script handling logic and rendering
2. **config/db-config.php** - Database configuration
3. **config/db-connection.php** - Database connection and table helper classes
4. **table-backend-styles.css** - Stylesheet
5. **init-database.php** - Database initialization script

## Installation

### 1. Configure the database

Edit `config/db-config.php` to set your database connection:

```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'laravel');      // your database name
define('DB_USER', 'root');         // your database user
define('DB_PASS', '');             // your database password
```

### 2. Initialize the database

In a browser, open:
```
http://localhost/init-database.php
```

Or run from CLI:
```bash
php public/init-database.php
```

This will automatically:
- Create the `employees` table (if not exists)
- Insert 20 sample records

### 3. Access the app

Open in your browser:
```
http://localhost/table-backend.php
```

## Features

### Search
- Use GET parameter `search`
- Searches all columns: Name, Position, Office, Age, Start Date
- Case-insensitive SQL LIKE queries

### Sorting
- Click table headers to sort
- Cycle through: none → ASC → DESC
- Use GET parameters `sort` and `dir`
- SQL injection protection (whitelist)

### Pagination
- Use GET parameter `page` for page number
- Use GET parameter `page_size` for page size
- Supports 5, 10, 25, 50, 100 per page
- Calculates total pages and range automatically

## URL Parameters

- `search` - keyword
- `sort` - column (name, position, office, age, start_date)
- `dir` - direction (ASC, DESC)
- `page` - current page
- `page_size` - items per page

### Example URLs

```
# Search for "London"
table-backend.php?search=London&page=1&page_size=10

# Sort by Name ascending
table-backend.php?sort=name&dir=ASC&page=1&page_size=10

# Combined: search + sort + paginate
table-backend.php?search=London&sort=age&dir=DESC&page=2&page_size=25
```

## Technical Notes

1. **Pure PHP, no framework**
   - Uses native PDO for DB operations
   - No external frameworks/libraries

2. **Security**
   - SQL injection protection (PDO prepared statements)
   - XSS protection (`htmlspecialchars()`)
   - Parameter validation (whitelisting)

3. **Performance**
   - SQL LIMIT and OFFSET for pagination
   - Database indexes
   - Query only required fields

4. **UX**
   - Preserve search and sort state
   - Clear URL parameters
   - Responsive design

## Table Schema

```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    office VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    start_date DATE NOT NULL,
    INDEX idx_name (name),
    INDEX idx_position (position),
    INDEX idx_office (office),
    INDEX idx_age (age),
    INDEX idx_start_date (start_date)
);
```

## Notes

1. Ensure PHP has PDO and PDO_MySQL extensions
2. Verify database connection settings
3. Ensure web server can read config files
4. For production, consider moving `config` outside the web root

## Troubleshooting

### Database connection failed
- Check `config/db-config.php`
- Ensure DB service is running
- Ensure the DB user has permissions

### Table not found
- Run `init-database.php` to initialize

### Page errors
- Check PHP error logs
- Verify file permissions

