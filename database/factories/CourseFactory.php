<?php

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(3),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'cover_image_path' => null,
            'price_in_cents' => fake()->randomFloat(2, 10, 999),
            'checkout_code' => fake()->uuid(),
            'status' => CourseStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CourseStatus::Published,
        ]);
    }
}
