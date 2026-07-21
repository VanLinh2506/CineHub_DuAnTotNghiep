<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use App\Models\Theater;
use Illuminate\Database\Seeder;

class MissingTheaterFoodItemsSeeder extends Seeder
{
    public function run(): void
    {
        $image = 'food_items/AMJOHtCGDp1fDPg6W8lkPrfyvqi3L5eDJKonlioG.jpg';
        $catalog = [
            ['name' => 'Combo Solo', 'description' => '1 bắp rang vừa và 1 nước ngọt vừa', 'price' => 79000],
            ['name' => 'Combo Couple', 'description' => '1 bắp rang lớn và 2 nước ngọt lớn', 'price' => 129000],
            ['name' => 'Combo Family', 'description' => '2 bắp rang lớn và 4 nước ngọt', 'price' => 239000],
        ];

        Theater::query()->where('is_active', true)->orderBy('id')->each(function (Theater $theater) use ($catalog, $image) {
            if (FoodItem::where('theater_id', $theater->id)->where('type', 'combo')->where('is_active', true)->exists()) {
                return;
            }

            foreach ($catalog as $item) {
                FoodItem::updateOrCreate(
                    ['theater_id' => $theater->id, 'name' => $item['name']],
                    $item + ['type' => 'combo', 'image' => $image, 'is_active' => true]
                );
            }
        });
    }
}
