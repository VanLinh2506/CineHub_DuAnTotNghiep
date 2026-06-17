<?php

namespace App\Services;

use App\Models\Showtime;
use App\Models\Screen;
use Illuminate\Support\Carbon;

class ShowtimeService
{
    /**
     * Tạo suất chiếu mới, validate trùng giờ + logic thời gian
     *
     * @throws \Exception nếu vi phạm logic
     */
    public function create(array $data): Showtime
    {
        $this->validateLogic($data);
        $this->checkOverlap($data);

        return Showtime::create($data);
    }

    /**
     * Cập nhật suất chiếu
     */
    public function update(Showtime $showtime, array $data): Showtime
    {
        $this->validateLogic($data, $showtime->id);
        $this->checkOverlap($data, $showtime->id);

        $showtime->update($data);
        return $showtime->fresh();
    }

    /**
     * Xoá suất chiếu — không cho xoá nếu đã có vé bán
     */
    public function delete(Showtime $showtime): void
    {
        $hasSoldTickets = $showtime->tickets()->where('status', 'Đã đặt')->exists();

        if ($hasSoldTickets) {
            throw new \Exception('Không thể xoá suất chiếu đã có vé được đặt.');
        }

        $showtime->delete();
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    /**
     * Validate logic cơ bản:
     * - Ngày chiếu không được trong quá khứ
     * - Giá phải > 0
     * - screen_id phải thuộc theater_id
     */
    private function validateLogic(array $data, ?int $excludeId = null): void
    {
        $showDate = Carbon::parse($data['show_date']);

        if ($showDate->startOfDay()->lt(now()->startOfDay())) {
            throw new \Exception('Ngày chiếu không được là ngày trong quá khứ.');
        }

        if (($data['price'] ?? 0) <= 0) {
            throw new \Exception('Giá vé phải lớn hơn 0.');
        }

        $screen = Screen::find($data['screen_id']);
        if (!$screen || $screen->theater_id != $data['theater_id']) {
            throw new \Exception('Phòng chiếu không thuộc rạp đã chọn.');
        }
    }

    /**
     * Kiểm tra trùng giờ chiếu trong cùng phòng:
     * Suất chiếu mới không được bắt đầu khi suất cũ chưa kết thúc.
     * Kết thúc = show_time + duration phim + 30 phút dọn phòng.
     */
    private function checkOverlap(array $data, ?int $excludeId = null): void
    {
        $screen   = Screen::find($data['screen_id']);
        $newMovie = \App\Models\Movie::find($data['movie_id']);
        $newDuration = $newMovie->duration ?? 120;
        $cleanUp  = 30; // phút dọn phòng

        $newStart = Carbon::parse($data['show_date'] . ' ' . $data['show_time']);
        $newEnd   = $newStart->copy()->addMinutes($newDuration + $cleanUp);

        // Lấy tất cả suất chiếu cùng phòng, cùng ngày
        $existing = Showtime::with('movie')
            ->where('screen_id', $data['screen_id'])
            ->where('show_date', $data['show_date'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->get();

        foreach ($existing as $show) {
            $duration  = $show->movie->duration ?? 120;
            $showStart = Carbon::parse($show->show_date->format('Y-m-d') . ' ' . $show->show_time);
            $showEnd   = $showStart->copy()->addMinutes($duration + $cleanUp);

            // Overlap: hai khoảng thời gian giao nhau
            if ($newStart->lt($showEnd) && $newEnd->gt($showStart)) {
                throw new \Exception(
                    "Trùng giờ chiếu với suất \"{$show->movie->title}\" "
                    . "({$showStart->format('H:i')} - {$showEnd->format('H:i')}) trong phòng {$screen->screen_name}."
                );
            }
        }
    }
}
