<?php

namespace Tests\Feature;

use App\Models\FoodItem;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeratorFoodItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderator_can_open_create_page_and_list_images_with_storage_urls(): void
    {
        $theater = Theater::factory()->create();
        $moderator = User::factory()->create([
            'role' => 'moderator',
            'theater_id' => $theater->id,
        ]);

        FoodItem::create([
            'theater_id' => $theater->id,
            'name' => 'Popcorn',
            'type' => 'snack',
            'price' => 50000,
            'description' => 'Bỏng ngô',
            'image' => 'food_items/popcorn.jpg',
            'is_active' => true,
        ]);

        $this->actingAs($moderator);

        $createResponse = $this->get('/moderator/food-items/create');
        $createResponse->assertOk();

        $listResponse = $this->get('/moderator/food-items?type=snack');
        $listResponse->assertOk();
        $listResponse->assertViewHas('foodItems', function ($items) {
            return isset($items[0]['image']) && $items[0]['image'] === asset('storage/food_items/popcorn.png');
        });
    }
}
