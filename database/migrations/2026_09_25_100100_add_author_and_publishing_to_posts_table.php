<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->timestamp('published_at')->nullable()->after('user_id')->index();
        });

        // Dosavadní příspěvky byly veřejné hned po vytvoření.
        DB::table('posts')->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropIndex(['published_at']);
            $table->dropColumn('published_at');
        });
    }
};
