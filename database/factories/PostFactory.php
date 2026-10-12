<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'published_at' => now()->subDay(),
        ];
    }

    public function withFilters(?int $workType = null, ?int $schoolYear = null, ?int $grade = null): static
    {
        $pick = fn (string $filter) => \App\Models\FilterOption::where('filter', $filter)->inRandomOrder()->value('id');

        return $this->state(fn () => [
            'work_type_id' => $workType ?? $pick('work_type'),
            'school_year_id' => $schoolYear ?? $pick('school_year'),
            'grade_id' => $grade ?? $pick('grade'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['published_at' => now()->addWeek()]);
    }
}
