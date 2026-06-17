<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\Theater;
use App\Services\ShowtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffShowtimeController extends Controller
{
    public function __construct(protected ShowtimeService $service) {}

    /**
     * Danh sách suất chiếu của rạp staff quản lý
     * GET /staff/showtimes
     */
    public function index(Request $request)
    {
        $theaterId = Auth::user()->theater_id;
        $date      = $request->input('date', today()->toDateString());

        $showtimes = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', fn($q) => $q->where('theater_id', $theaterId))
            ->where('show_date', $date)
            ->orderBy('screen_id')
            ->orderBy('show_time')
            ->get();

        $screens = Screen::where('theater_id', $theaterId)->get();

        return view('staff.showtimes.index', compact('showtimes', 'date', 'screens'));
    }

    /**
     * Form tạo suất chiếu
     * GET /staff/showtimes/create
     */
    public function create()
    {
        $theaterId = Auth::user()->theater_id;
        $movies    = Movie::where('status', 'Chiếu rạp')->orderBy('title')->get();
        $screens   = Screen::where('theater_id', $theaterId)->get();

        return view('staff.showtimes.form', compact('movies', 'screens'));
    }

    /**
     * Lưu suất chiếu mới
     * POST /staff/showtimes
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'movie_id'   => 'required|exists:movies,id',
            'screen_id'  => 'required|exists:theater_screens,id',
            'show_date'  => 'required|date',
            'show_time'  => 'required|date_format:H:i',
            'price'      => 'required|numeric|min:1',
        ]);

        $data['theater_id'] = Auth::user()->theater_id;

        try {
            $showtime = $this->service->create($data);

            return redirect()->route('staff.showtimes.index', ['date' => $showtime->show_date])
                ->with('success', 'Tạo suất chiếu thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Form sửa suất chiếu
     * GET /staff/showtimes/{id}/edit
     */
    public function edit(Showtime $showtime)
    {
        $theaterId = Auth::user()->theater_id;
        $this->authorizeShowtime($showtime, $theaterId);

        $movies  = Movie::where('status', 'Chiếu rạp')->orderBy('title')->get();
        $screens = Screen::where('theater_id', $theaterId)->get();

        return view('staff.showtimes.form', compact('showtime', 'movies', 'screens'));
    }

    /**
     * Cập nhật suất chiếu
     * PUT /staff/showtimes/{id}
     */
    public function update(Request $request, Showtime $showtime)
    {
        $theaterId = Auth::user()->theater_id;
        $this->authorizeShowtime($showtime, $theaterId);

        $data = $request->validate([
            'movie_id'   => 'required|exists:movies,id',
            'screen_id'  => 'required|exists:theater_screens,id',
            'show_date'  => 'required|date',
            'show_time'  => 'required|date_format:H:i',
            'price'      => 'required|numeric|min:1',
        ]);

        $data['theater_id'] = $theaterId;

        try {
            $this->service->update($showtime, $data);

            return redirect()->route('staff.showtimes.index', ['date' => $data['show_date']])
                ->with('success', 'Cập nhật suất chiếu thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Xoá suất chiếu
     * DELETE /staff/showtimes/{id}
     */
    public function destroy(Showtime $showtime)
    {
        $this->authorizeShowtime($showtime, Auth::user()->theater_id);

        try {
            $this->service->delete($showtime);
            return back()->with('success', 'Đã xoá suất chiếu.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizeShowtime(Showtime $showtime, int $theaterId): void
    {
        if ($showtime->screen->theater_id !== $theaterId) {
            abort(403, 'Bạn không có quyền thao tác suất chiếu này.');
        }
    }
}
