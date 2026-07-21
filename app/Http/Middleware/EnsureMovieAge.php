<?php

namespace App\Http\Middleware;

use App\Models\Movie;
use App\Models\Showtime;
use App\Services\AgeRatingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMovieAge
{
    public function __construct(private readonly AgeRatingService $ages)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $movie = $this->resolveMovie($request);
        if (!$movie || $this->ages->minimumAge($movie->age_rating) === 0 || $user->isAdmin()) {
            return $next($request);
        }

        if (!$user->birthdate) {
            return redirect()->route('profile.index')
                ->with('error', 'Vui lòng cập nhật ngày sinh để hệ thống kiểm tra độ tuổi của phim.');
        }

        if (!$this->ages->canAccess($user, $movie)) {
            $minimumAge = $this->ages->minimumAge($movie->age_rating);
            return redirect()->route('movies.index')
                ->with('error', "Phim này dành cho khán giả từ {$minimumAge} tuổi. Tài khoản của bạn chưa đủ tuổi.");
        }

        return $next($request);
    }

    private function resolveMovie(Request $request): ?Movie
    {
        $movieId = $request->route('id') ?? $request->route('movieId');
        if ($movieId) {
            return Movie::find($movieId);
        }

        $showtimeId = $request->route('showtimeId')
            ?? $request->input('showtime_id')
            ?? $request->input('selected_showtime_id');
        return $showtimeId ? Showtime::with('movie')->find($showtimeId)?->movie : null;
    }
}
