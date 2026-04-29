<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_versions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('cv_template_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('target_role')->nullable();
            $table->string('target_industry')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_base')->default(false);
            $table->timestamp('last_exported_at')->nullable();
            $table->unsignedInteger('export_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cv_template_id')
                ->references('id')
                ->on('cv_templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_versions');
    }
};
