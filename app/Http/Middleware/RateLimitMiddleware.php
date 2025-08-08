<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '60'): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, (int) $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        RateLimiter::hit($key);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(implode('|', [
            $request->ip(),
            $request->userAgent(),
            $request->route()?->getDomain(),
        ]));
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildResponse(string $key, string $maxAttempts): JsonResponse
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'error' => 'Too many requests',
            'message' => 'Rate limit exceeded. Please try again later.',
            'retry_after' => $retryAfter,
        ], 429);
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders(Response $response, string $maxAttempts, int $remainingAttempts): Response
    {
        if ($response instanceof \Illuminate\Http\Response) {
            return $response->header('X-RateLimit-Limit', $maxAttempts)
                ->header('X-RateLimit-Remaining', $remainingAttempts);
        }
        
        return $response;
    }

    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, string $maxAttempts): int
    {
        return RateLimiter::remaining($key, (int) $maxAttempts);
    }
}
