<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $faker = Faker::create('cs_CZ');
        
        foreach ($categories as $category) {
            for ($i = 1; $i <= 8; $i++) {
                $title = $this->getTitleForCategory($category->name, $i, $faker);
                
                $post = Post::create([
                    'title' => $title,
                    'content' => $this->getContentForCategory($category->name, $faker),
                    'category_id' => $category->id,
                    'slug' => Str::slug($title) . '-' . uniqid(),
                ]);
            }
        }
    }

    private function getTitleForCategory($category, $number, $faker): string
    {
        $titles = [
            'Vývojáři' => [
                'Nový framework pro PHP',
                'Optimalizace výkonu aplikací',
                'Bezpečnostní praktiky v kódu',
                'Moderní architektura aplikací',
                'Testování a automatizace',
                'Jak na efektivní verzování',
                'Rozdíl mezi Vue a React',
                'Úvod do Docker kontejnerů'
            ],
            'Grafici' => [
                'Trendy v designu pro rok 2024',
                'Práce s barevnými schématy',
                'UX design v praxi',
                'Moderní typografie',
                'Responzivní design',
                'Psychologie barev v reklamě',
                'Nástroje pro 3D modelování',
                'Tvorba profesionálního portfolia'
            ],
            'Filmaři' => [
                'Nové techniky střihu',
                'Práce se světlem',
                'Zvuková postprodukce',
                'Kamerové pohyby',
                'Barevné korekce',
                'Výběr správného objektivu',
                'Tipy pro natáčení venku',
                'Drony ve filmové produkci'
            ]
        ];

        return $titles[$category][$number - 1] ?? $faker->sentence();
    }

    private function getContentForCategory($category, $faker): string
    {
        $contents = [
            'Vývojáři' => [
                'Moderní vývoj software vyžaduje neustálé učení a adaptaci na nové technologie.',
                'Při vývoji je důležité myslet na škálovatelnost a udržitelnost kódu.',
                'Bezpečnost by měla být prioritou při každém vývoji.',
                'Automatizované testy šetří čas a zvyšují kvalitu kódu.',
                'Dokumentace je stejně důležitá jako samotný kód.'
            ],
            'Grafici' => [
                'Design musí být nejen krásný, ale především funkční a intuitivní.',
                'Správné použití barev může významně ovlivnit uživatelský zážitek.',
                'Typografie je základním stavebním kamenem každého designu.',
                'Responzivní design je v dnešní době nutností.',
                'Minimalistický design neznamená nudný design.'
            ],
            'Filmaři' => [
                'Správná práce se světlem je základem kvalitního záběru.',
                'Zvuk tvoří 50% úspěchu každého videa.',
                'Střih může zachránit i průměrný materiál.',
                'Barevné korekce dodávají filmu atmosféru.',
                'Správné vybavení je důležité, ale důležitější je vědět, jak ho použít.'
            ]
        ];

        return $contents[$category][array_rand($contents[$category])] . "\n\n" . $faker->paragraphs(4, true);
    }
}