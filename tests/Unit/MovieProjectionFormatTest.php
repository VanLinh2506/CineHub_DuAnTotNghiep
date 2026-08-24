<?php

namespace Tests\Unit;

use App\Models\Movie;
use PHPUnit\Framework\TestCase;

class MovieProjectionFormatTest extends TestCase
{
    public function test_movies_can_only_play_in_a_matching_screen_format(): void
    {
        $movie2d = new Movie(['projection_format' => '2D']);
        $movie3d = new Movie(['projection_format' => '3D']);
        $movie4d = new Movie(['projection_format' => '4DX']);

        $this->assertTrue($movie2d->canPlayInScreen('2D'));
        $this->assertFalse($movie2d->canPlayInScreen('3D'));
        $this->assertFalse($movie2d->canPlayInScreen('4DX'));
        $this->assertFalse($movie3d->canPlayInScreen('2D'));
        $this->assertTrue($movie3d->canPlayInScreen('3D'));
        $this->assertFalse($movie3d->canPlayInScreen('4D'));
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
