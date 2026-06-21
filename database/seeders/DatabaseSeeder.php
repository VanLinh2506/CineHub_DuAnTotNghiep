<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ──────────────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@cinehub.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Test User',
            'email'    => 'test@cinehub.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        // ── Categories ──────────────────────────────────────────
        $categories = [
            ['name' => 'Hành động',   'slug' => 'hanh-dong'],
            ['name' => 'Tình cảm',    'slug' => 'tinh-cam'],
            ['name' => 'Hài hước',    'slug' => 'hai-huoc'],
            ['name' => 'Kinh dị',     'slug' => 'kinh-di'],
            ['name' => 'Hoạt hình',   'slug' => 'hoat-hinh'],
            ['name' => 'Khoa học viễn tưởng', 'slug' => 'khoa-hoc-vien-tuong'],
            ['name' => 'Phiêu lưu',   'slug' => 'phieu-luu'],
            ['name' => 'Tâm lý',      'slug' => 'tam-ly'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ── Movies (ảnh từ TMDB) ────────────────────────────────
        $movies = [
            [
                'title'       => 'Avengers: Endgame',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 1,
                'duration'    => 182,
                'rating'      => 8.4,
                'country'     => 'Mỹ',
                'director'    => 'Anthony & Joe Russo',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Sau sự kiện Infinity War, các Avengers còn lại phải tập hợp lại để đảo ngược hành động của Thanos.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/7RyHsO4yDXtBv1zUU3mTpHeQ0d5.jpg',
            ],
            [
                'title'       => 'Spider-Man: No Way Home',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 1,
                'duration'    => 148,
                'rating'      => 8.2,
                'country'     => 'Mỹ',
                'director'    => 'Jon Watts',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Peter Parker nhờ Doctor Strange giúp mọi người quên rằng anh là Spider-Man, nhưng phép thuật bị sai.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/iQFcwSGbZXMkeyKrxbPnwnRo5fl.jpg',
            ],
            [
                'title'       => 'The Dark Knight',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 1,
                'duration'    => 152,
                'rating'      => 9.0,
                'country'     => 'Mỹ',
                'director'    => 'Christopher Nolan',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T16',
                'level'       => 'Free',
                'description' => 'Batman phải đối mặt với Joker — tên tội phạm hỗn loạn muốn đẩy Gotham City vào vô chính phủ.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/hkBaDkMWbLaf8B1lsWsKX7Ew3Xq.jpg',
            ],
            [
                'title'       => 'Interstellar',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 6,
                'duration'    => 169,
                'rating'      => 8.6,
                'country'     => 'Mỹ',
                'director'    => 'Christopher Nolan',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Một nhóm phi hành gia du hành qua lỗ sâu trong không gian để tìm kiếm ngôi nhà mới cho nhân loại.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/pbrkL804c8yAv3zBZR4QPEafpAR.jpg',
            ],
            [
                'title'       => 'Inception',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 6,
                'duration'    => 148,
                'rating'      => 8.8,
                'country'     => 'Mỹ',
                'director'    => 'Christopher Nolan',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Một tên trộm chuyên đột nhập vào giấc mơ để lấy cắp bí mật nhận nhiệm vụ cài ý tưởng vào não người.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
            ],
            [
                'title'       => 'Guardians of the Galaxy',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 7,
                'duration'    => 121,
                'rating'      => 8.0,
                'country'     => 'Mỹ',
                'director'    => 'James Gunn',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Một nhóm tội phạm vũ trụ hợp lực để bảo vệ thiên hà khỏi kẻ thù nguy hiểm.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/r7vmZjiyZw9rpJMQJdXpjgiCOk9.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/bHarw8xrmQeqf3t8HpuMY7zoK4x.jpg',
            ],
            [
                'title'       => 'Joker',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 8,
                'duration'    => 122,
                'rating'      => 8.4,
                'country'     => 'Mỹ',
                'director'    => 'Todd Phillips',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T18',
                'level'       => 'Free',
                'description' => 'Câu chuyện về Arthur Fleck — một diễn viên hài thất bại dần biến thành tên tội phạm hỗn loạn Joker.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/n6bUvigpRFqSwmPp1m2YADdbRBc.jpg',
            ],
            [
                'title'       => 'Parasite',
                'type'        => 'phimle',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 8,
                'duration'    => 132,
                'rating'      => 8.5,
                'country'     => 'Hàn Quốc',
                'director'    => 'Bong Joon-ho',
                'language'    => 'Tiếng Hàn',
                'age_rating'  => 'T16',
                'level'       => 'Free',
                'description' => 'Một gia đình nghèo dần thâm nhập vào cuộc sống của gia đình giàu có theo cách bất ngờ.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/TU9NIjwzjoKPwQHoHshkFcQUCG.jpg',
            ],
            [
                'title'       => 'Oppenheimer',
                'type'        => 'phimle',
                'status'      => 'Chiếu rạp',
                'status_admin'=> 'published',
                'category_id' => 8,
                'duration'    => 180,
                'rating'      => 8.3,
                'country'     => 'Mỹ',
                'director'    => 'Christopher Nolan',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T16',
                'level'       => 'Free',
                'description' => 'Câu chuyện về J. Robert Oppenheimer và quá trình phát triển bom nguyên tử trong Thế chiến II.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/feSiISwgEpVzR1v3zv2n2NSa2MC.jpg',
            ],
            [
                'title'       => 'Barbie',
                'type'        => 'phimle',
                'status'      => 'Chiếu rạp',
                'status_admin'=> 'published',
                'category_id' => 2,
                'duration'    => 114,
                'rating'      => 6.9,
                'country'     => 'Mỹ',
                'director'    => 'Greta Gerwig',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'description' => 'Barbie sống trong Barbieland hoàn hảo cho đến khi bị đuổi ra thế giới thực.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/iuFNMS8vlzmfa8XSmff7yzm49GU.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/ctMserH8g2SeOAnCw5gFjdQF8mo.jpg',
            ],
            [
                'title'       => 'One Piece',
                'type'        => 'phimbo',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 7,
                'duration'    => 24,
                'rating'      => 8.9,
                'country'     => 'Nhật Bản',
                'director'    => 'Konosuke Uda',
                'language'    => 'Tiếng Nhật',
                'age_rating'  => 'T13',
                'level'       => 'Free',
                'total_episodes' => 1100,
                'description' => 'Monkey D. Luffy và đồng đội hành trình tìm kiếm kho báu One Piece để trở thành Vua hải tặc.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/fcFQFPbMsLGJMoULFhqOLpCQJNw.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/2rmK7mnchw9Xr3XdiAwASFMfUFB.jpg',
            ],
            [
                'title'       => 'Breaking Bad',
                'type'        => 'phimbo',
                'status'      => 'Chiếu online',
                'status_admin'=> 'published',
                'category_id' => 8,
                'duration'    => 47,
                'rating'      => 9.5,
                'country'     => 'Mỹ',
                'director'    => 'Vince Gilligan',
                'language'    => 'Tiếng Anh',
                'age_rating'  => 'T18',
                'level'       => 'Free',
                'total_episodes' => 62,
                'description' => 'Giáo viên hóa học mắc bệnh ung thư hợp tác với học sinh cũ sản xuất ma túy đá.',
                'thumbnail'   => 'https://image.tmdb.org/t/p/w500/3xnWaLQjelJDDF7LT1WBo6f4BRe.jpg',
                'banner'      => 'https://image.tmdb.org/t/p/original/tsRy63Mu5cu8etL1X7ZLyf7UP1M.jpg',
            ],
        ];

        foreach ($movies as $movie) {
            Movie::create(array_merge([
                'slug'           => \Illuminate\Support\Str::slug($movie['title']),
                'release_date'   => now()->subYears(rand(1, 5))->toDateString(),
                'total_episodes' => 1,
                'type'           => 'phimle',
            ], $movie));
        }

        // ── Theater + Screen ────────────────────────────────────
        $theater = Theater::create([
            'name'    => 'CineHub Quận 1',
            'address' => '123 Nguyễn Huệ, Q.1',
            'city'    => 'TP. Hồ Chí Minh',
            'phone'   => '028 1234 5678',
        ]);

        Screen::create([
            'theater_id'  => $theater->id,
            'screen_name' => 'Phòng 1 - Standard',
            'screen_type' => '2D',
            'total_seats' => 100,
        ]);

        Screen::create([
            'theater_id'  => $theater->id,
            'screen_name' => 'Phòng 2 - IMAX',
            'screen_type' => 'IMAX',
            'total_seats' => 80,
        ]);
    }
}
