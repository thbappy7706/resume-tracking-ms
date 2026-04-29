<?php

use App\Enums\CompanySize;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->enum('size', array_column(CompanySize::cases(), 'value'))->nullable();
            $table->string('location')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->fullText(['name', 'industry']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
