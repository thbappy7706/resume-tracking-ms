<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_section_tag', function (Blueprint $table): void {
            $table->ulid('profile_section_id');
            $table->ulid('tag_id');

            $table->foreign('profile_section_id')
                ->references('id')
                ->on('profile_sections')
                ->cascadeOnDelete();

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->cascadeOnDelete();

            $table->primary(['profile_section_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_section_tag');
    }
};
