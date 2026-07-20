<?php

namespace Tests\Unit;

use App\Models\Movie;
use PHPUnit\Framework\TestCase;

class MovieProjectionFormatTest extends TestCase
{
    public function test_movies_only_play_in_a_screen_with_the_same_format(): void
    {
        $movie4d = new Movie(['projection_format' => '4DX']);

        $this->assertTrue($movie4d->canPlayInScreen('4DX'));
        $this->assertFalse($movie4d->canPlayInScreen('2D'));
        $this->assertFalse($movie4d->canPlayInScreen('3D'));
    }

    public function test_legacy_movies_default_to_2d(): void
    {
        $movie = new Movie();

        $this->assertTrue($movie->canPlayInScreen('2D'));
        $this->assertFalse($movie->canPlayInScreen('3D'));
    }
}
