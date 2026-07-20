<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledMoviePublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_scheduled_movie_is_published_by_the_command(): void
    {
        $movie = Movie::create([
            'title' => 'Due scheduled movie',
            'type' => 'phimle',
            'status' => 'Sắp chiếu',
            'scheduled_status' => 'Chiếu online',
            'publish_date' => now()->subMinute(),
            'status_admin' => 'published',
        ]);

        $this->artisan('movies:update-status')
            ->expectsOutput('Published 1 scheduled online movie(s).')
            ->assertSuccessful();

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'status' => 'Chiếu online',
            'scheduled_status' => null,
        ]);
    }

    public function test_web_request_publishes_due_movie_when_scheduler_is_not_running(): void
    {
        $movie = Movie::create([
            'title' => 'Due movie without scheduler',
            'type' => 'phimle',
            'status' => 'Sắp chiếu',
            'scheduled_status' => 'Chiếu online',
            'publish_date' => now()->subMinute(),
            'status_admin' => 'published',
        ]);

        $this->get('/')->assertSuccessful();

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'status' => 'Chiếu online',
            'scheduled_status' => null,
        ]);
    }

    public function test_future_scheduled_movie_remains_upcoming(): void
    {
        $movie = Movie::create([
            'title' => 'Future scheduled movie',
            'type' => 'phimle',
            'status' => 'Sắp chiếu',
            'scheduled_status' => 'Chiếu online',
            'publish_date' => now()->addMinute(),
            'status_admin' => 'published',
        ]);

        $this->get('/')->assertSuccessful();

        $this->assertDatabaseHas('movies', [
            'id' => $movie->id,
            'status' => 'Sắp chiếu',
            'scheduled_status' => 'Chiếu online',
        ]);
    }
}
