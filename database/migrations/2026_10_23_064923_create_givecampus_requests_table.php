<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('givecampus_requests', function (Blueprint $table) {
            $table->id();
            $table->json('payload');
            $table->string('status')->nullable();
            $table->string('request_id')->nullable();
            $table->string('download_url', 1024)->nullable();
            $table->json('data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('givecampus_requests');
    }
};
