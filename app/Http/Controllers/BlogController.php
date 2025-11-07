<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     * Supports: Search, Sort, Filter by Active/Inactive, Ordering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Blog::with('author');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by active/inactive status
        if ($request->has('is_active')) {
            if ($request->is_active === '1' || $request->is_active === true) {
                $query->active();
            } elseif ($request->is_active === '0' || $request->is_active === false) {
                $query->inactive();
            }
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort_by to prevent SQL injection
        $allowedSortFields = ['id', 'title', 'created_at', 'updated_at', 'order', 'published_at', 'views'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 5);
        $blogs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'message' => 'Blogs retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        \Log::info('Blog Store Request:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'input' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $validated = $request->validated();

            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
                
                // Ensure slug is unique
                $originalSlug = $validated['slug'];
                $counter = 1;
                while (Blog::where('slug', $validated['slug'])->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $blog = Blog::create($validated);
            $blog->load('author');

            $response = [
                'success' => true,
                'data' => $blog,
                'message' => 'Blog created successfully'
            ];

            \Log::info('Blog Store Response:', [
                'status_code' => 201,
                'response' => $response
            ]);

            return response()->json($response, 201);
        } catch (\Exception $e) {
            \Log::error('Blog Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog): JsonResponse
    {
        $blog->load('author');
        
        // Increment views
        $blog->increment('views');

        return response()->json([
            'success' => true,
            'data' => $blog,
            'message' => 'Blog retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        $validated = $request->validated();

        // Generate slug if title is updated and slug is not provided
        if (isset($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure slug is unique (excluding current blog)
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Blog::where('slug', $validated['slug'])->where('id', '!=', $blog->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $blog->update($validated);
        $blog->load('author');

        return response()->json([
            'success' => true,
            'data' => $blog,
            'message' => 'Blog updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog): JsonResponse
    {
        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully'
        ]);
    }

    /**
     * Set blog as active.
     */
    public function setActive(Blog $blog): JsonResponse
    {
        $blog->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'data' => $blog,
            'message' => 'Blog set as active successfully'
        ]);
    }

    /**
     * Set blog as inactive.
     */
    public function setInactive(Blog $blog): JsonResponse
    {
        $blog->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'data' => $blog,
            'message' => 'Blog set as inactive successfully'
        ]);
    }

    /**
     * Update blog order.
     */
    public function updateOrder(Request $request, Blog $blog): JsonResponse
    {
        $request->validate([
            'order' => 'required|integer|min:0'
        ]);

        $blog->update(['order' => $request->order]);

        return response()->json([
            'success' => true,
            'data' => $blog,
            'message' => 'Blog order updated successfully'
        ]);
    }

    /**
     * Bulk update order for multiple blogs.
     */
    public function bulkUpdateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:blogs,id',
            'orders.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->orders as $orderData) {
            Blog::where('id', $orderData['id'])->update(['order' => $orderData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog orders updated successfully'
        ]);
    }
}
