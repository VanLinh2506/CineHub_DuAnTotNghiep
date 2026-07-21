<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\User;

class AgeRatingService
{
    public function minimumAge(?string $rating): int
    {
        $rating = strtoupper(trim((string) $rating));
        if ($rating === '' || in_array($rating, ['P', 'G', 'K'], true)) {
            return 0;
        }

        if (preg_match('/(\d{1,2})/', $rating, $matches)) {
            return (int) $matches[1];
        }

        return match ($rating) {
            'PG-13' => 13,
            'R', 'NC-17', 'TV-MA' => 18,
            default => 0,
        };
    }

    public function userAge(User $user): ?int
    {
        return $user->birthdate?->age;
    }

    public function canAccess(User $user, Movie $movie): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $minimumAge = $this->minimumAge($movie->age_rating);
        return $minimumAge === 0
            || ($this->userAge($user) !== null && $this->userAge($user) >= $minimumAge);
    }
}
