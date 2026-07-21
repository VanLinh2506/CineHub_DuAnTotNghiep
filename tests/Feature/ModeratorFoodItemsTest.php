<?php

namespace Tests\Feature;

use App\Models\FoodItem;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModeratorFoodItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderator_can_open_create_page_and_list_images_with_storage_urls(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('food_items/popcorn.jpg', 'fake image');

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
            return isset($items[0]['image']) && $items[0]['image'] === storage_url('food_items/popcorn.jpg');
        });
    }

    public function test_moderator_only_sees_and_edits_food_items_from_their_theater(): void
    {
        $ownTheater = Theater::factory()->create();
        $otherTheater = Theater::factory()->create();
        $moderator = User::factory()->create([
            'role' => 'moderator',
            'theater_id' => $ownTheater->id,
        ]);

        $ownItem = FoodItem::create([
            'theater_id' => $ownTheater->id,
            'name' => 'Combo của rạp mình',
            'type' => 'combo',
            'price' => 80000,
            'is_active' => true,
        ]);
        $otherItem = FoodItem::create([
            'theater_id' => $otherTheater->id,
            'name' => 'Combo của rạp khác',
            'type' => 'combo',
            'price' => 90000,
            'is_active' => true,
        ]);

        $this->actingAs($moderator)
            ->get('/moderator/food-items')
            ->assertOk()
            ->assertSee($ownItem->name)
            ->assertDontSee($otherItem->name);

        $this->actingAs($moderator)
            ->get("/moderator/food-items/{$otherItem->id}/edit")
            ->assertNotFound();
    }

    public function test_admin_food_item_management_routes_do_not_exist(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/food-items')
            ->assertNotFound();
    }

    public function test_food_item_can_be_updated_without_an_updated_at_column(): void
    {
        $theater = Theater::factory()->create();
        $item = FoodItem::create([
            'theater_id' => $theater->id,
            'name' => 'Bắp rang',
            'type' => 'snack',
            'price' => 50000,
            'is_active' => true,
        ]);

        $item->update(['image' => 'food_items/new-image.jpg']);

        $this->assertSame('food_items/new-image.jpg', $item->fresh()->image);
    }
}
