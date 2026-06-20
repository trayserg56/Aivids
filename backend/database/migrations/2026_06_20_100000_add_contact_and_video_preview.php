<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->string('preview_path')->nullable()->after('video_path');
            $table->string('source_filename')->nullable()->after('preview_path');
            $table->unsignedBigInteger('file_size_bytes')->nullable()->after('source_filename');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['preview_path', 'source_filename', 'file_size_bytes']);
        });

        Schema::dropIfExists('contact_submissions');
    }
};
