<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Traits\ToStringFormat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'categories_id' => '['.rand(1 , 6).']',
            'title' => fake()->name(),
            'html' => fake()->name(),
            'ribbon' => fake()->name(),
            'weight' => rand(80,100),
            'width' => rand(80,100),
            'height' => rand(80,100),
            'length' => rand(80,100),
            'stack_status' => rand(0,3),
            'stack_count' => rand(0,300),
            'stack_limit' => rand(0,5),
            'barcode' => rand(1000000,100000000),
            'product_code' => rand(1000000,100000000),
            'sale_price' => rand(1000000,100000000),
            'purchase_price' => rand(1000000,10000000),
            'confirm_discount' => rand(0,1),
            'discount_percent' => rand(0,30),
            'discount_manual' => rand(0,30),
            'discount_price' => rand(0,1),
            'discount_time_from' => now(),
            'discount_time_until' => now(),
            'safe_discount_percent' => rand(5,10),
            'special_discount_percent' => rand(10,15),
            'exceptional_discount_percent' => rand(15,20),
            'page_view' => rand(15,2000),
        ];
    }
}
