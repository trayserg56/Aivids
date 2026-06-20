<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('conversion_status', 20)->default('completed')->after('is_published');
            $table->unsignedTinyInteger('conversion_progress')->default(100)->after('conversion_status');
            $table->string('conversion_step')->nullable()->after('conversion_progress');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['conversion_status', 'conversion_progress', 'conversion_step']);
        });
    }
};
