<?php

namespace App\Http\Middleware;

use App\Exceptions\RequestRateExceededException;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Cache\RateLimiter;

/**
 * Class ThrottleRequests
 * 
 * This class is based on Illuminate\Routing\Middleware\ThrottleRequests however
 * it throws custom exception instead of just returning response
 *
 * @package App\Http\Middleware
 */
class ThrottleRequests
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new request throttler.
     *
     * @param  \Illuminate\Cache\RateLimiter $limiter
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  int $maxAttempts
     * @param  int $decayMinutes
     *
     * @return mixed
     * @throws RequestRateExceededException
     */
    public function handle(
        $request,
        Closure $next,
        $maxAttempts = 60,
        $decayMinutes = 1
    ) {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts,
            $decayMinutes)
        ) {
            throw (new RequestRateExceededException())->setHeaders(
                [
                    'Retry-After' => $this->limiter->availableIn($key),
                    'X-RateLimit-Limit' => $maxAttempts,
                    'X-RateLimit-Remaining' => 0,
                ]);
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);
        
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $maxAttempts -
                $this->limiter->attempts($key) + 1,
        ]);

        return $response;
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return $request->fingerprint();
    }
}
