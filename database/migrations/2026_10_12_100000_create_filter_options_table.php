<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_options', function (Blueprint $table) {
            $table->id();
            $table->string('filter', 20)->index();
            $table->string('name', 50);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['filter', 'name']);
        });

        $defaults = [
            'work_type' => ['Klauzurní práce', 'Maturitní práce', 'Práce z hodin'],
            'school_year' => ['2025/26', '2026/27', '2027/28'],
            'grade' => ['1. ročník', '2. ročník', '3. ročník', '4. ročník'],
        ];

        foreach ($defaults as $filter => $names) {
            foreach ($names as $i => $name) {
                DB::table('filter_options')->insert([
                    'filter' => $filter,
                    'name' => $name,
                    'sort_order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('posts', function (Blueprint $table) {
            // Nullable kvůli stávajícím příspěvkům; nové musí mít vyplněno (validace v PostRequest).
            $table->foreignId('work_type_id')->nullable()->after('category_id')->constrained('filter_options')->restrictOnDelete();
            $table->foreignId('school_year_id')->nullable()->after('work_type_id')->constrained('filter_options')->restrictOnDelete();
            $table->foreignId('grade_id')->nullable()->after('school_year_id')->constrained('filter_options')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grade_id');
            $table->dropConstrainedForeignId('school_year_id');
            $table->dropConstrainedForeignId('work_type_id');
        });

        Schema::dropIfExists('filter_options');
    }
};
