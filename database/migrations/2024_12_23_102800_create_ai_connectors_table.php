<?php
// database/migrations/YYYY_MM_DD_create_repositories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_connectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_api_key_id')->constrained('ai_api_keys')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');

            $table->string('name');
            $table->json('llm_options');


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_connector');
    }
};
