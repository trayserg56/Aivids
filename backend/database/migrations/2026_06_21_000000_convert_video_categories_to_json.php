<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('description');
        });

        DB::table('videos')->orderBy('id')->each(function (object $video): void {
            $categories = [];

            if (! empty($video->category)) {
                $categories = [$video->category];
            }

            DB::table('videos')
                ->where('id', $video->id)
                ->update(['categories' => json_encode($categories, JSON_UNESCAPED_UNICODE)]);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
        });

        DB::table('videos')->orderBy('id')->each(function (object $video): void {
            $categories = json_decode($video->categories ?? '[]', true);
            $first = is_array($categories) ? ($categories[0] ?? null) : null;

            DB::table('videos')
                ->where('id', $video->id)
                ->update(['category' => $first]);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
