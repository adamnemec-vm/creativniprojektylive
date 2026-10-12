<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilterOption>
 */
class FilterOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'filter' => 'work_type',
            'name' => fake()->unique()->words(2, true),
            'sort_order' => 0,
        ];
    }
}
