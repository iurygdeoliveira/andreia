<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use App\Models\Module;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }
}
