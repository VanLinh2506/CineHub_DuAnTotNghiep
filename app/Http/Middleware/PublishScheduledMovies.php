<?php

namespace App\Http\Middleware;

use App\Services\ScheduledMoviePublisher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublishScheduledMovies
{
    public function __construct(
        private readonly ScheduledMoviePublisher $publisher,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Keep scheduled publishing working in local/shared-hosting environments
        // where `php artisan schedule:run` may not be running continuously.
        $this->publisher->publishDue();

        return $next($request);
    }
}
