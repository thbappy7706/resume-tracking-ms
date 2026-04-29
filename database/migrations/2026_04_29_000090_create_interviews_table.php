<?php

use App\Enums\InterviewOutcome;
use App\Enums\InterviewType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('job_application_id');
            $table->unsignedTinyInteger('round')->default(1);
            $table->enum('type', array_column(InterviewType::cases(), 'value'));
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('interviewer_names')->nullable();
            $table->string('platform')->nullable();
            $table->enum('outcome', array_column(InterviewOutcome::cases(), 'value'))->nullable()->default(InterviewOutcome::Pending->value);
            $table->text('feedback')->nullable();
            $table->text('prep_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_application_id')
                ->references('id')
                ->on('job_applications')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
