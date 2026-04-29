<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_histories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulid('job_application_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('note')->nullable();
            $table->timestamp('changed_at');

            $table->foreign('job_application_id')
                ->references('id')
                ->on('job_applications')
                ->cascadeOnDelete();

            $table->index(['job_application_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
