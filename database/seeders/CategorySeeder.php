<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Vývojáři',
                'description' => 'Naši vývojáři se specializují na tvorbu moderních webových a mobilních aplikací.'
            ],
            [
                'name' => 'Grafici',
                'description' => 'Náš grafický tým vytváří jedinečné vizuální identity a designová řešení.'
            ],
            [
                'name' => 'Filmaři',
                'description' => 'Zachycujeme příběhy prostřednictvím videí a filmů.'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 