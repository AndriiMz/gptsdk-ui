<?php
// database/migrations/YYYY_MM_DD_create_repositories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('repository_id');
            $table->unsignedBigInteger('ai_api_key_id');

            $table->string('path');

            $table->json('llm_options');
            $table->json('variable_values');
            $table->json('input');
            $table->json('output');

            $table->enum('status', ['error', 'success']);

            $table->index(
                ['repository_id', 'path'],
                'repository_id_path'
            );

            $table->index(
                ['repository_id', 'created_at'],
                'repository_id_created_at'
            );

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_logs');
    }
};
