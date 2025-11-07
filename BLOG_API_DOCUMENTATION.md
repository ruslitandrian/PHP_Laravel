# Blog API Documentation

This document describes the Blog API endpoints for the Laravel application.

## Database Structure

### Blogs Table

The `blogs` table includes the following fields:

- `id` - Primary key
- `title` - Blog post title (required)
- `slug` - URL-friendly identifier (unique, auto-generated from title if not provided)
- `content` - Blog post content (required)
- `excerpt` - Short summary (optional)
- `featured_image` - Featured image URL (optional)
- `author_id` - Foreign key to users table (required)
- `is_active` - Active status (boolean, default: true)
- `order` - Display order (integer, default: 0)
- `meta_title` - SEO meta title (optional)
- `meta_description` - SEO meta description (optional)
- `meta_keywords` - SEO keywords (optional)
- `views` - View count (integer, default: 0)
- `published_at` - Publication date (timestamp, optional)
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp
- `deleted_at` - Soft delete timestamp

## API Endpoints

All endpoints are prefixed with `/api/blogs`.

### 1. List Blogs (GET /api/blogs)

Retrieve a paginated list of blogs with support for search, sorting, and filtering.

**Query Parameters:**
- `search` (optional) - Search in title, content, and excerpt
- `is_active` (optional) - Filter by active status (1 for active, 0 for inactive)
- `sort_by` (optional) - Sort by field: `id`, `title`, `created_at`, `updated_at`, `order`, `published_at`, `views` (default: `created_at`)
- `sort_order` (optional) - Sort direction: `asc` or `desc` (default: `desc`)
- `per_page` (optional) - Number of items per page (default: 5)
- `page` (optional) - Page number for pagination

**Example Request:**
```
GET /api/blogs?search=laravel&is_active=1&sort_by=created_at&sort_order=desc&per_page=10
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Blog Post Title",
        "slug": "blog-post-title",
        "content": "Blog content...",
        "excerpt": "Short summary",
        "is_active": true,
        "order": 0,
        "views": 10,
        "author": {
          "id": 1,
          "name": "Author Name",
          "email": "author@example.com"
        },
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 100
  },
  "message": "Blogs retrieved successfully"
}
```

### 2. Create Blog (POST /api/blogs)

Create a new blog post.

**Request Body:**
```json
{
  "title": "Blog Post Title",
  "slug": "blog-post-title", // Optional - auto-generated from title if not provided
  "content": "Full blog post content...",
  "excerpt": "Short summary",
  "featured_image": "https://example.com/image.jpg",
  "author_id": 1,
  "is_active": true,
  "order": 0,
  "meta_title": "SEO Title",
  "meta_description": "SEO Description",
  "meta_keywords": "keyword1, keyword2",
  "published_at": "2024-01-01 12:00:00"
}
```

**Required Fields:**
- `title` - Blog post title
- `content` - Blog post content
- `author_id` - User ID of the author

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Blog Post Title",
    "slug": "blog-post-title",
    "content": "Full blog post content...",
    "author": {
      "id": 1,
      "name": "Author Name"
    },
    "created_at": "2024-01-01T00:00:00.000000Z"
  },
  "message": "Blog created successfully"
}
```

### 3. Get Single Blog (GET /api/blogs/{id})

Retrieve a specific blog post by ID. Automatically increments the view count.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Blog Post Title",
    "slug": "blog-post-title",
    "content": "Full blog post content...",
    "author": {
      "id": 1,
      "name": "Author Name"
    },
    "views": 11
  },
  "message": "Blog retrieved successfully"
}
```

### 4. Update Blog (PUT /api/blogs/{id} or PATCH /api/blogs/{id})

Update an existing blog post.

**Request Body:**
```json
{
  "title": "Updated Title",
  "content": "Updated content...",
  "is_active": false,
  "order": 5
}
```

All fields are optional. Only provided fields will be updated.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Updated Title",
    "content": "Updated content...",
    "is_active": false,
    "order": 5
  },
  "message": "Blog updated successfully"
}
```

### 5. Delete Blog (DELETE /api/blogs/{id})

Soft delete a blog post (uses soft deletes, so it can be restored).

**Response:**
```json
{
  "success": true,
  "message": "Blog deleted successfully"
}
```

### 6. Set Blog Active (POST /api/blogs/{id}/set-active)

Set a blog post as active.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "is_active": true
  },
  "message": "Blog set as active successfully"
}
```

### 7. Set Blog Inactive (POST /api/blogs/{id}/set-inactive)

Set a blog post as inactive.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "is_active": false
  },
  "message": "Blog set as inactive successfully"
}
```

### 8. Update Blog Order (PUT /api/blogs/{id}/order)

Update the display order of a specific blog post.

**Request Body:**
```json
{
  "order": 10
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "order": 10
  },
  "message": "Blog order updated successfully"
}
```

### 9. Bulk Update Order (POST /api/blogs/bulk-update-order)

Update the display order of multiple blog posts at once.

**Request Body:**
```json
{
  "orders": [
    {
      "id": 1,
      "order": 10
    },
    {
      "id": 2,
      "order": 20
    },
    {
      "id": 3,
      "order": 30
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Blog orders updated successfully"
}
```

## Model Scopes

The Blog model includes several useful scopes:

- `Blog::active()` - Only active blogs
- `Blog::inactive()` - Only inactive blogs
- `Blog::published()` - Only published blogs (active and published_at <= now)
- `Blog::ordered($direction)` - Order by order field
- `Blog::search($search)` - Search in title, content, and excerpt

**Example Usage:**
```php
// Get only active blogs
$activeBlogs = Blog::active()->get();

// Get published blogs ordered by order field
$publishedBlogs = Blog::published()->ordered('asc')->get();

// Search blogs
$searchResults = Blog::search('laravel')->get();
```

## Relationships

The Blog model has a relationship with the User model:

- `$blog->author` - Returns the User who authored the blog post

## Running Migrations

To create the blogs table, run:

```bash
php artisan migrate
```

## Validation Rules

### Create Blog
- `title`: Required, string, max 255 characters
- `slug`: Optional, string, max 255 characters, must be unique
- `content`: Required, string
- `excerpt`: Optional, string, max 500 characters
- `featured_image`: Optional, string, max 500 characters
- `author_id`: Required, must exist in users table
- `is_active`: Optional, boolean
- `order`: Optional, integer, minimum 0
- `meta_title`: Optional, string, max 255 characters
- `meta_description`: Optional, string, max 500 characters
- `meta_keywords`: Optional, string, max 255 characters
- `published_at`: Optional, date

### Update Blog
- Same as create, but all fields are optional (use `sometimes` validation)
- Slug must be unique, excluding the current blog ID

