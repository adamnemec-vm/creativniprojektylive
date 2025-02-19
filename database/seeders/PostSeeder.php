<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $sampleImages = ['image1.jpg', 'image2.jpg', 'image3.jpg', 'image4.jpg']; // názvy vašich obrázků
        
        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                $post = Post::create([
                    'title' => $this->getTitleForCategory($category->name, $i),
                    'content' => $this->getContentForCategory($category->name),
                    'category_id' => $category->id,
                ]);

                // Přidáme obrázek jen k některým příspěvkům (např. k sudým)
                if ($i % 2 == 0) {
                    $sourcePath = public_path('storage/posts/first_image.jpg');
                    if (File::exists($sourcePath)) {
                        // Použijeme náhodný obrázek z kolekce
                        $randomImage = $sampleImages[array_rand($sampleImages)];
                        $newPath = 'posts/' . uniqid() . '.jpg';
                        Storage::disk('public')->copy('posts/' . $randomImage, $newPath);
                        $post->images()->create(['image_path' => $newPath]);
                    }
                }
            }
        }
    }

    private function getTitleForCategory($category, $number): string
    {
        $titles = [
            'Vývojáři' => [
                'Nový framework pro PHP',
                'Optimalizace výkonu aplikací',
                'Bezpečnostní praktiky v kódu',
                'Moderní architektura aplikací',
                'Testování a automatizace'
            ],
            'Grafici' => [
                'Trendy v designu pro rok 2024',
                'Práce s barevnými schématy',
                'UX design v praxi',
                'Moderní typografie',
                'Responzivní design'
            ],
            'Filmaři' => [
                'Nové techniky střihu',
                'Práce se světlem',
                'Zvuková postprodukce',
                'Kamerové pohyby',
                'Barevné korekce'
            ]
        ];

        return $titles[$category][$number - 1];
    }

    private function getContentForCategory($category): string
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

        return $contents[$category][array_rand($contents[$category])] . "\n\n" .
               "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    }
} 