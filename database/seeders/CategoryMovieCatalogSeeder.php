<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryMovieCatalogSeeder extends Seeder
{
    private const TARGET_PER_CATEGORY = 15;

    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = DB::table('categories')->orderBy('id')->get(['id', 'name']);

            foreach ($categories as $category) {
                $currentCount = DB::table('movies')
                    ->where('category_id', $category->id)
                    ->count();
                $sequence = 1;

                while ($currentCount < self::TARGET_PER_CATEGORY) {
                    $title = sprintf('[BỔ SUNG] %s %02d', $category->name, $sequence++);

                    if (DB::table('movies')->where('title', $title)->exists()) {
                        continue;
                    }

                    DB::table('movies')->insert([
                        'title' => $title,
                        'category_id' => $category->id,
                        'level' => ['Free', 'Silver', 'Gold', 'Premium'][$currentCount % 4],
                        'duration' => 85 + (($currentCount * 7) % 55),
                        'description' => 'Phim bổ sung cho thể loại '.$category->name.'. Nội dung, hình ảnh và video sẽ được cập nhật sau.',
                        'director' => 'Đang cập nhật',
                        'actors' => 'Đang cập nhật',
                        'video_url' => null,
                        'trailer_url' => null,
                        'thumbnail' => null,
                        'banner' => null,
                        'status' => 'Chiếu online',
                        'rating' => 6.5 + (($currentCount % 15) / 10),
                        'status_admin' => 'published',
                        'publish_date' => now()->subDays(($currentCount % 30) + 1),
                        'geo_restriction' => null,
                        'drm_enabled' => false,
                        'country' => 'Việt Nam',
                        'language' => 'Tiếng Việt',
                        'age_rating' => $currentCount % 3 === 0 ? 'T16' : 'T13',
                        'type' => $currentCount % 4 === 0 ? 'phimbo' : 'phimle',
                        'max_tickets' => null,
                        'normal_price' => 90000,
                        'vip_price' => 120000,
                        'couple_price' => 180000,
                        'created_at' => now()->subMinutes($category->id * 20 + $currentCount),
                    ]);

                    $movieId = DB::getPdo()->lastInsertId();
                    DB::table('movie_category')->updateOrInsert(
                        ['movie_id' => $movieId, 'category_id' => $category->id],
                        ['created_at' => now(), 'updated_at' => now()]
                    );

                    $currentCount++;
                }
            }

            $titleCatalog = $this->realTitleCatalog();
            foreach ($categories as $category) {
                $candidates = $titleCatalog[$category->name] ?? [];
                $supplementalMoviesForCategory = DB::table('movies')
                    ->where('category_id', $category->id)
                    ->where('description', 'like', 'Phim bổ sung cho thể loại%')
                    ->orderBy('id')
                    ->get(['id']);

                foreach ($supplementalMoviesForCategory as $index => $movie) {
                    $title = $candidates[$index] ?? sprintf('%s - Tuyển tập %02d', $category->name, $index + 1);
                    $duplicate = DB::table('movies')
                        ->where('title', $title)
                        ->where('id', '!=', $movie->id)
                        ->exists();

                    if ($duplicate) {
                        $title .= ' (Bản tuyển chọn)';
                    }

                    DB::table('movies')->where('id', $movie->id)->update(['title' => $title]);
                }
            }

            $animationCategoryId = $categories->firstWhere('name', 'Hoạt hình')->id ?? null;
            if ($animationCategoryId) {
                DB::table('movies')
                    ->where('category_id', $animationCategoryId)
                    ->where('description', 'like', 'Phim bổ sung cho thể loại%')
                    ->update(['age_rating' => 'P']);
            }

            $animationCategoryId = $categories->firstWhere('id', 5)->id ?? null;
            if ($animationCategoryId) {
                DB::table('movies')
                    ->where('category_id', $animationCategoryId)
                    ->where('description', 'like', 'Phim bổ sung cho thể loại%')
                    ->update(['age_rating' => 'P']);
            }

            $categoryIds = $categories->pluck('id')->values();
            $supplementalMovies = DB::table('movies')
                ->where('description', 'like', 'Phim bổ sung cho thể loại%')
                ->orderBy('id')
                ->get(['id', 'category_id']);

            foreach ($supplementalMovies as $index => $movie) {
                // Roughly two thirds of supplemental movies get one or two
                // secondary genres while category_id remains the main genre.
                if ($index % 3 === 2) {
                    continue;
                }

                $primaryPosition = $categoryIds->search($movie->category_id);
                $secondaryIds = [
                    $categoryIds[($primaryPosition + 1) % $categoryIds->count()],
                ];

                if ($index % 4 === 0) {
                    $secondaryIds[] = $categoryIds[($primaryPosition + 3) % $categoryIds->count()];
                }

                foreach (array_unique($secondaryIds) as $secondaryId) {
                    if ((int) $secondaryId === (int) $movie->category_id) {
                        continue;
                    }

                    DB::table('movie_category')->updateOrInsert(
                        ['movie_id' => $movie->id, 'category_id' => $secondaryId],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        });

        $this->command?->info('Đã bảo đảm mỗi thể loại có ít nhất 15 phim và bổ sung phim đa thể loại.');
    }

    private function realTitleCatalog(): array
    {
        return [
            'Hành động' => [
                'John Wick (2014)', 'Mad Max: Fury Road (2015)', 'Die Hard (1988)',
                'The Raid: Redemption (2011)', 'Mission: Impossible - Fallout (2018)',
                'Top Gun: Maverick (2022)', 'The Dark Knight (2008)', 'Police Story (1985)',
                'Gladiator (2000)', 'Kill Bill: Vol. 1 (2003)', 'Heat (1995)',
                'The Bourne Ultimatum (2007)', 'Skyfall (2012)', 'Extraction (2020)', 'Nobody (2021)',
            ],
            'Tình cảm' => [
                'The Notebook (2004)', 'La La Land (2016)', 'Pride & Prejudice (2005)',
                'Before Sunrise (1995)', 'A Walk to Remember (2002)', 'About Time (2013)',
                'Me Before You (2016)', 'The Fault in Our Stars (2014)', 'Notting Hill (1999)',
                'Crazy Rich Asians (2018)', 'Eternal Sunshine of the Spotless Mind (2004)',
                '500 Days of Summer (2009)', 'The Shape of Water (2017)', 'Past Lives (2023)', 'Love Actually (2003)',
            ],
            'Hài' => [
                'Groundhog Day (1993)', 'Superbad (2007)', 'The Grand Budapest Hotel (2014)',
                'Bridesmaids (2011)', 'The Big Lebowski (1998)', 'Mean Girls (2004)',
                'Home Alone (1990)', 'Mrs. Doubtfire (1993)', 'School of Rock (2003)',
                'The Nice Guys (2016)', 'Palm Springs (2020)', 'Game Night (2018)',
                'Jojo Rabbit (2019)', 'Little Miss Sunshine (2006)', 'Borat (2006)',
            ],
            'Kinh dị' => [
                'The Exorcist (1973)', 'The Shining (1980)', 'Get Out (2017)',
                'Hereditary (2018)', 'A Quiet Place (2018)', 'The Babadook (2014)',
                'It (2017)', 'The Ring (2002)', 'Sinister (2012)', 'Insidious (2010)',
                'The Witch (2015)', 'Midsommar (2019)', 'Train to Busan (2016)',
                'Talk to Me (2022)', 'The Invisible Man (2020)',
            ],
            'Hoạt hình' => [
                'Spirited Away (2001)', 'Coco (2017)', 'WALL-E (2008)', 'Up (2009)',
                'Inside Out (2015)', 'Ratatouille (2007)', 'The Lion King (1994)',
                'Spider-Man: Into the Spider-Verse (2018)', 'How to Train Your Dragon (2010)',
                'Zootopia (2016)', 'Klaus (2019)', 'Your Name. (2016)', 'The Incredibles (2004)',
                'Finding Nemo (2003)', 'The Lego Movie (2014)',
            ],
            'Khoa học viễn tưởng' => [
                'Interstellar (2014)', 'Inception (2010)', 'Blade Runner 2049 (2017)',
                'The Matrix (1999)', 'Arrival (2016)', 'Dune (2021)', 'Ex Machina (2014)',
                'Edge of Tomorrow (2014)', 'District 9 (2009)', 'Children of Men (2006)',
                'Moon (2009)', 'Looper (2012)', 'Minority Report (2002)',
                'The Martian (2015)', 'Everything Everywhere All at Once (2022)',
            ],
            'Phiêu lưu' => [
                'The Lord of the Rings: The Fellowship of the Ring (2001)',
                'Raiders of the Lost Ark (1981)', 'Jurassic Park (1993)', 'Life of Pi (2012)',
                'The Revenant (2015)', 'Cast Away (2000)', 'The Secret Life of Walter Mitty (2013)',
                'Pirates of the Caribbean: The Curse of the Black Pearl (2003)',
                'The Hobbit: An Unexpected Journey (2012)', 'Jumanji: Welcome to the Jungle (2017)',
                'The Mummy (1999)', 'King Kong (2005)', 'National Treasure (2004)',
                'The Goonies (1985)', 'Into the Wild (2007)',
            ],
            'Tài liệu' => [
                'Planet Earth (2006)', 'Free Solo (2018)', 'My Octopus Teacher (2020)',
                '13th (2016)', 'Won’t You Be My Neighbor? (2018)', 'The Social Dilemma (2020)',
                'Blackfish (2013)', 'March of the Penguins (2005)', 'Amy (2015)',
                'Senna (2010)', 'Man on Wire (2008)', 'Inside Job (2010)',
                'The Act of Killing (2012)', 'Searching for Sugar Man (2012)', 'Apollo 11 (2019)',
            ],
            'Chiến tranh' => [
                'Saving Private Ryan (1998)', '1917 (2019)', 'Dunkirk (2017)',
                'Schindler’s List (1993)', 'Apocalypse Now (1979)', 'Full Metal Jacket (1987)',
                'Platoon (1986)', 'The Thin Red Line (1998)', 'Hacksaw Ridge (2016)',
                'Letters from Iwo Jima (2006)', 'All Quiet on the Western Front (2022)',
                'The Hurt Locker (2008)', 'Fury (2014)', 'Enemy at the Gates (2001)', 'The Pianist (2002)',
            ],
            'Thể thao' => [
                'Rocky (1976)', 'Raging Bull (1980)', 'Moneyball (2011)', 'Ford v Ferrari (2019)',
                'Remember the Titans (2000)', 'Coach Carter (2005)', 'Rush (2013)',
                'Creed (2015)', 'The Blind Side (2009)', 'Million Dollar Baby (2004)',
                'Warrior (2011)', 'The Wrestler (2008)', 'I, Tonya (2017)',
                'Chariots of Fire (1981)', 'Bend It Like Beckham (2002)',
            ],
        ];
    }
}
