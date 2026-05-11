<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_route_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 16);
            $table->text('url');
            $table->json('request_headers')->nullable();
            $table->json('query_params')->nullable();
            $table->longText('request_body')->nullable();
            $table->integer('status_code')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_histories');
    }
};
