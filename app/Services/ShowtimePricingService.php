<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\MovieViewEvent;
use App\Models\Showtime;
use App\Models\TheaterContract;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ShowtimePricingService
{
    public const INTER_THEATER_PRICE_GAP = 5000;
    public const NEW_RELEASE_DAYS = 30;

    /**
     * The group is data-driven so a theater cannot select a more favourable band.
     */
    public function classify(Movie $movie, string $showDate): string
    {
        $date = Carbon::parse($showDate)->startOfDay();
        $publishedAt = $movie->publish_date?->copy()->startOfDay();

        if ($publishedAt && $publishedAt->lte($date) && $publishedAt->diffInDays($date) <= self::NEW_RELEASE_DAYS) {
            return TheaterContract::PRICE_TYPE_NEW_RELEASE;
        }

        $recentViews = MovieViewEvent::where('movie_id', $movie->id)
            ->where('created_at', '>=', $date->copy()->subDays(30))
            ->where('created_at', '<', $date->copy()->addDay())
            ->count();

        if ($recentViews > 0 || (float) $movie->rating >= 8.0) {
            return TheaterContract::PRICE_TYPE_HOT_MOVIE;
        }

        return TheaterContract::PRICE_TYPE_BESTSELLER;
    }

    public function analyze(
        int $theaterId,
        Movie $movie,
        string $showDate,
        ?int $ignoreShowtimeId = null
    ): array {
        $contract = TheaterContract::where('theater_id', $theaterId)
            ->whereIn('status', [TheaterContract::STATUS_ACTIVE, TheaterContract::STATUS_PENDING])
            ->whereDate('start_date', '<=', $showDate)
            ->whereDate('end_date', '>=', $showDate)
            ->orderByDesc('start_date')
            ->first();

        if (!$contract) {
            throw ValidationException::withMessages([
                'show_date' => 'Ngày chiếu không nằm trong thời hạn hợp đồng của rạp.',
            ]);
        }

        $priceType = $this->classify($movie, $showDate);
        [$contractMinimum, $contractMaximum] = $contract->listedPriceRange($priceType);

        // Average each theater first, preventing a theater with many showtimes from
        // having more influence on the market reference price.
        $theaterAverages = Showtime::query()
            ->where('movie_id', $movie->id)
            ->whereDate('show_date', $showDate)
            ->where('theater_id', '!=', $theaterId)
            ->when($ignoreShowtimeId, fn ($query) => $query->whereKeyNot($ignoreShowtimeId))
            ->selectRaw('theater_id, AVG(price) as average_price')
            ->groupBy('theater_id')
            ->pluck('average_price');

        $marketAverage = $theaterAverages->isEmpty()
            ? null
            : (int) round((float) $theaterAverages->avg());

        $minimum = $contractMinimum;
        $maximum = $contractMaximum;
        if ($marketAverage !== null) {
            $minimum = max($minimum, $marketAverage - self::INTER_THEATER_PRICE_GAP);
            $maximum = min($maximum, $marketAverage + self::INTER_THEATER_PRICE_GAP);
        }

        if ($minimum > $maximum) {
            throw ValidationException::withMessages([
                'price' => 'Khung giá hợp đồng không giao với biên giá thị trường ±5.000 VNĐ. Vui lòng liên hệ quản trị viên để điều chỉnh hợp đồng.',
            ]);
        }

        return compact(
            'contract', 'priceType', 'contractMinimum', 'contractMaximum',
            'marketAverage', 'minimum', 'maximum'
        );
    }

    public function validatePrice(
        int $theaterId,
        Movie $movie,
        string $showDate,
        int $price,
        ?int $ignoreShowtimeId = null
    ): array {
        $analysis = $this->analyze($theaterId, $movie, $showDate, $ignoreShowtimeId);

        if ($price < $analysis['minimum'] || $price > $analysis['maximum']) {
            $marketText = $analysis['marketAverage'] === null
                ? ''
                : ' Giá trung bình của các rạp khác là ' . number_format($analysis['marketAverage']) . ' VNĐ; mức chênh tối đa là ' . number_format(self::INTER_THEATER_PRICE_GAP) . ' VNĐ.';

            throw ValidationException::withMessages([
                'price' => 'Giá vé hợp lệ phải từ ' . number_format($analysis['minimum']) . ' đến ' . number_format($analysis['maximum']) . ' VNĐ.' . $marketText,
            ]);
        }

        return $analysis;
    }
}
