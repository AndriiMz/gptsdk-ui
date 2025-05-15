<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_variable_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('repository_id');
            $table->string('path');
            $table->json('variable_values');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('repository_id')->references('id')->on('repositories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_variable_values');
    }
};
