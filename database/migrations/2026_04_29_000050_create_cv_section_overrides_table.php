<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_section_overrides', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('cv_version_id');
            $table->ulid('profile_section_id')->nullable();
            $table->boolean('is_included')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('override_title')->nullable();
            $table->longText('override_description')->nullable();
            $table->json('override_meta')->nullable();
            $table->timestamps();

            $table->foreign('cv_version_id')
                ->references('id')
                ->on('cv_versions')
                ->cascadeOnDelete();

            $table->foreign('profile_section_id')
                ->references('id')
                ->on('profile_sections')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_section_overrides');
    }
};
