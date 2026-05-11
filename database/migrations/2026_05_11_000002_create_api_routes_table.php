<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_project_id')->constrained()->cascadeOnDelete();
            $table->string('method', 16);
            $table->string('uri');
            $table->string('name')->nullable();
            $table->string('action')->nullable();
            $table->json('middleware')->nullable();
            $table->json('parameters')->nullable();
            $table->json('headers')->nullable();
            $table->json('body_schema')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['api_project_id', 'method', 'uri']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_routes');
    }
};
