<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screen;
use Illuminate\Support\Facades\DB;

echo "=== INSERTING SHOWTIMES ===\n\n";

// Get first movie with status "Chiếu rạp"
$movie = Movie::where('status', 'Chiếu rạp')->first();
if (!$movie) {
    die("❌ No movies with status 'Chiếu rạp' found!\n");
}

// Get first theater
$theater = Theater::first();
if (!$theater) {
    die("❌ No theaters found!\n");
}

// Get first screen of this theater
$screen = Screen::where('theater_id', $theater->id)->first();
if (!$screen) {
    die("❌ No screens found for theater!\n");
}

echo "Using:\n";
echo "  Movie: {$movie->title} (ID: {$movie->id})\n";
echo "  Theater: {$theater->name} (ID: {$theater->id})\n";
echo "  Screen: {$screen->screen_name} (ID: {$screen->id})\n\n";

// Insert showtimes for next 7 days
$times = ['10:00:00', '14:00:00', '18:00:00', '21:00:00'];
$inserted = 0;

for ($day = 0; $day < 7; $day++) {
    $date = date('Y-m-d', strtotime("+$day days"));
    
    foreach ($times as $time) {
        // Check if already exists
        $exists = Showtime::where('movie_id', $movie->id)
            ->where('theater_id', $theater->id)
            ->where('screen_id', $screen->id)
            ->where('show_date', $date)
            ->where('show_time', $time)
            ->exists();
        
        if (!$exists) {
            Showtime::create([
                'movie_id' => $movie->id,
                'theater_id' => $theater->id,
                'screen_id' => $screen->id,
                'show_date' => $date,
                'show_time' => $time,
                'price' => 90000,
                'created_at' => now(),
            ]);
            
            echo "✓ Inserted: $date $time\n";
            $inserted++;
        } else {
            echo "- Skipped (exists): $date $time\n";
        }
    }
}

echo "\n✅ Inserted $inserted showtimes!\n";
echo "\nTotal showtimes now: " . Showtime::where('show_date', '>=', date('Y-m-d'))->count() . "\n";
