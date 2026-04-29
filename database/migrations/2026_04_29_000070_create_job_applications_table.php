<?php

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->ulid('cv_version_id')->nullable();
            $table->string('role_title');
            $table->string('job_url')->nullable();
            $table->enum('source', array_column(ApplicationSource::cases(), 'value'));
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->enum('status', array_column(ApplicationStatus::cases(), 'value'))->default(ApplicationStatus::Saved->value);
            $table->date('applied_at')->nullable();
            $table->date('responded_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->date('deadline')->nullable();
            $table->unsignedTinyInteger('excitement_level')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->restrictOnDelete();

            $table->foreign('cv_version_id')
                ->references('id')
                ->on('cv_versions')
                ->nullOnDelete();

            $table->index(['status', 'applied_at', 'source']);
            $table->fullText(['role_title', 'notes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
