<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log request information
        $this->logRequest($request);

        // Process the request and get the response
        $response = $next($request);

        // Log response information
        $this->logResponse($request, $response);

        return $response;
    }

    /**
     * Log request information
     */
    protected function logRequest(Request $request): void
    {
        $method = strtoupper($request->method());
        $uri = $request->path();
        $fullUrl = $request->fullUrl();
        $ip = $request->ip();
        $headers = $request->headers->all();
        
        // Filter sensitive information
        $params = $request->except([
            'password',
            'password_confirmation',
            'token',
            'api_token',
            'authorization',
        ]);

        // Log complete request information
        Log::info("===== Incoming Request =====");
        Log::info("Method: {$method}");
        Log::info("URI: {$uri}");
        Log::info("Full URL: {$fullUrl}");
        Log::info("IP: {$ip}");
        Log::info("Headers: " . json_encode($headers, JSON_PRETTY_PRINT));
        Log::info("Params: " . json_encode($params, JSON_PRETTY_PRINT));
        Log::info("Content Type: " . $request->getContentType());
        Log::info("Wants JSON: " . ($request->wantsJson() ? 'true' : 'false'));
        Log::info("Expects JSON: " . ($request->expectsJson() ? 'true' : 'false'));
        Log::info("Is JSON: " . ($request->isJson() ? 'true' : 'false'));
        Log::info("Is API: " . ($request->is('api/*') ? 'true' : 'false'));
    }

    /**
     * Log response information
     */
    protected function logResponse(Request $request, Response $response): void
    {
        $method = strtoupper($request->method());
        $uri = $request->path();
        $status = $response->getStatusCode();
        
        $content = $response->getContent();
        
        // Log complete response information
        Log::info("===== Outgoing Response =====");
        Log::info("Status: {$status}");
        
        // Log response headers
        $responseHeaders = [];
        foreach ($response->headers->all() as $name => $values) {
            $responseHeaders[$name] = $values[0];
        }
        Log::info("Headers: " . json_encode($responseHeaders, JSON_PRETTY_PRINT));
        
        // Log response content
        $responseData = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            Log::info("Response (JSON): " . json_encode($responseData, JSON_PRETTY_PRINT));
        } else {
            // If not JSON, log first 2000 characters
            $responsePreview = mb_strlen($content) > 2000 ? 
                mb_substr($content, 0, 2000) . '... [TRUNCATED]' : 
                $content;
            Log::info("Response (Raw): {$responsePreview}");
        }
        
        // Log redirect information
        if ($status >= 300 && $status < 400) {
            $location = $response->headers->get('Location');
            if ($location) {
                Log::info("Redirecting to: {$location}");
                
                // Check if this is an unexpected redirect to frontend
                if (strpos($location, 'http://localhost:5173') === 0) {
                    Log::warning("Unexpected redirect to frontend detected!");
                    Log::warning("This usually happens when:");
                    Log::warning("1. The request is missing 'Accept: application/json' header");
                    Log::warning("2. The route is protected by auth middleware without 'api' guard");
                    Log::warning("3. The request is not properly authenticated");
                    
                    // Log current authentication status
                    if (auth()->check()) {
                        Log::info("User is authenticated. ID: " . auth()->id());
                    } else {
                        Log::warning("User is not authenticated!");
                    }
                }
            }
        }
    }
}
