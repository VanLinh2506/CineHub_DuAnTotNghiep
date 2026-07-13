<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_index_can_search_by_title(): void
    {
        $category = Category::create([
            'name' => 'Science Fiction',
            'slug' => 'science-fiction',
        ]);

        Movie::create([
            'title' => 'Interstellar Test Movie',
            'category_id' => $category->id,
            'description' => 'Space exploration',
            'director' => 'Christopher Nolan',
            'actors' => 'Actor A',
            'status' => 'Online',
            'status_admin' => 'published',
            'type' => 'phimle',
            'level' => 'Free',
            'duration' => 120,
            'country' => 'USA',
            'rating' => 9.1,
        ]);

        Movie::create([
            'title' => 'Unrelated Test Movie',
            'category_id' => $category->id,
            'description' => 'Different movie',
            'director' => 'Director B',
            'actors' => 'Actor B',
            'status' => 'Online',
            'status_admin' => 'published',
            'type' => 'phimle',
            'level' => 'Free',
            'duration' => 90,
            'country' => 'USA',
            'rating' => 6.5,
        ]);

        $this->get('/movies?search=Interstellar')
            ->assertOk()
            ->assertSee('Interstellar Test Movie')
            ->assertDontSee('Unrelated Test Movie');
    }

    public function test_movie_index_can_filter_by_category(): void
    {
        $action = Category::create(['name' => 'Action', 'slug' => 'action']);
        $drama = Category::create(['name' => 'Drama', 'slug' => 'drama']);

        Movie::create([
            'title' => 'Action Category Movie',
            'category_id' => $action->id,
            'status' => 'Online',
            'status_admin' => 'published',
            'type' => 'phimle',
            'level' => 'Free',
            'duration' => 100,
            'rating' => 7.5,
        ]);

        Movie::create([
            'title' => 'Drama Category Movie',
            'category_id' => $drama->id,
            'status' => 'Online',
            'status_admin' => 'published',
            'type' => 'phimle',
            'level' => 'Free',
            'duration' => 100,
            'rating' => 7.5,
        ]);

        $this->get('/movies?category='.$action->id)
            ->assertOk()
            ->assertSee('Action Category Movie')
            ->assertDontSee('Drama Category Movie');
    }
}
