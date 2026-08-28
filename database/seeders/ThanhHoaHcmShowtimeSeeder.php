<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\FoodItem;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\Theater;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ThanhHoaHcmShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $movies = Movie::query()
                ->where('status_admin', 'published')
                ->where('type', 'phimle')
                ->orderByDesc('rating')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            if ($movies->count() < 3) {
                $movies = Movie::query()->orderByDesc('rating')->limit(8)->get();
            }

            if ($movies->isEmpty()) {
                throw new RuntimeException('Cần có ít nhất một phim trước khi tạo lịch chiếu.');
            }

            Movie::query()->whereIn('id', $movies->pluck('id'))->update([
                'status' => 'Chiếu rạp',
                'status_admin' => 'published',
            ]);

            $theaterDefinitions = [
                [
                    'name' => 'CineHub Lam Sơn Thanh Hóa',
                    'location' => 'Thanh Hóa',
                    'address' => 'Đại lộ Lê Lợi, phường Lam Sơn, TP. Thanh Hóa',
                    'latitude' => 19.8075,
                    'longitude' => 105.7764,
                    'phone' => '02373777701',
                ],
                [
                    'name' => 'CineHub Hạc Thành Thanh Hóa',
                    'location' => 'Thanh Hóa',
                    'address' => 'Đường Hạc Thành, phường Điện Biên, TP. Thanh Hóa',
                    'latitude' => 19.8038,
                    'longitude' => 105.7787,
                    'phone' => '02373777702',
                ],
                [
                    'name' => 'Lotte Cinema Gò Vấp',
                    'location' => 'TP. Hồ Chí Minh',
                    'address' => '242 Nguyễn Văn Lượng, Phường 10, Quận Gò Vấp, TP.HCM',
                    'latitude' => 10.8387,
                    'longitude' => 106.6717,
                    'phone' => '02837757501',
                ],
            ];

            $layout = [
                'rows' => range('A', 'J'),
                'cols' => range(1, 12),
                'vip_rows' => ['D', 'E', 'F'],
                'couple_rows' => ['J'],
                'layout_type' => 'standard',
            ];
            $screenTypes = ['2D', '2D', 'IMAX'];
            $slots = ['09:15:00', '12:15:00', '15:15:00', '18:15:00', '21:15:00'];

            foreach ($theaterDefinitions as $theaterIndex => $definition) {
                $theater = Theater::updateOrCreate(
                    ['name' => $definition['name']],
                    [...$definition, 'is_active' => true]
                );

                foreach ([
                    ['name' => 'Combo Solo', 'type' => 'combo', 'price' => 69000, 'description' => '1 bắp rang vừa và 1 nước ngọt vừa'],
                    ['name' => 'Combo Couple', 'type' => 'combo', 'price' => 129000, 'description' => '1 bắp rang lớn và 2 nước ngọt vừa'],
                    ['name' => 'Bắp rang caramel', 'type' => 'snack', 'price' => 59000, 'description' => 'Bắp rang caramel cỡ lớn'],
                    ['name' => 'Nước ngọt', 'type' => 'drink', 'price' => 39000, 'description' => 'Nước ngọt cỡ vừa'],
                ] as $foodDefinition) {
                    FoodItem::updateOrCreate(
                        ['theater_id' => $theater->id, 'name' => $foodDefinition['name']],
                        [...$foodDefinition, 'is_active' => true]
                    );
                }

                $screens = collect(range(1, 3))->map(function (int $number) use ($theater, $layout, $screenTypes): Screen {
                    return Screen::updateOrCreate(
                        ['theater_id' => $theater->id, 'screen_name' => 'Phòng '.$number],
                        [
                            'screen_type' => $screenTypes[$number - 1],
                            'total_seats' => 120,
                            'seat_layout_config' => $layout,
                        ]
                    );
                });

                for ($day = 0; $day < 7; $day++) {
                    $date = Carbon::today()->addDays($day)->toDateString();

                    foreach ($screens as $screenIndex => $screen) {
                        foreach ($slots as $slotIndex => $time) {
                            $movie = $movies[($theaterIndex + $day + $screenIndex + $slotIndex) % $movies->count()];
                            $hour = (int) substr($time, 0, 2);
                            $price = $hour >= 18 ? 110000 : ($hour >= 12 ? 90000 : 70000);

                            Showtime::updateOrCreate(
                                [
                                    'screen_id' => $screen->id,
                                    'show_date' => $date,
                                    'show_time' => $time,
                                ],
                                [
                                    'movie_id' => $movie->id,
                                    'theater_id' => $theater->id,
                                    'price' => $price + ($screen->screen_type === 'IMAX' ? 30000 : 0),
                                    'available_seats' => $screen->total_seats,
                                ]
                            );
                        }
                    }
                }
            }
        });
    }
}
