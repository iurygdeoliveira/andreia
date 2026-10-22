<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'course_id' => Course::factory(),
            // MoneyCast recebe reais e grava centavos no banco.
            'unit_price' => fake()->randomFloat(2, 10, 999),
        ];
    }
}
