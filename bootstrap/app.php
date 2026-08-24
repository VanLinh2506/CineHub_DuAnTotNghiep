<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\PublishScheduledMovies::class,
        ]);

        // Legacy route middleware để tương thích với PHP cũ
        $middleware->append(\App\Http\Middleware\LegacyRouteMiddleware::class);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'moderator' => \App\Http\Middleware\ModeratorMiddleware::class,
            'counter_staff' => \App\Http\Middleware\CounterStaffMiddleware::class,
            'movie.age' => \App\Http\Middleware\EnsureMovieAge::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.',
                ], 419);
            }

            return redirect()->route('home')->with(
                'error',
                'Phiên làm việc đã hết hạn hoặc đã được thay thế. Vui lòng đăng nhập lại.'
            );
        });
    })->create();
