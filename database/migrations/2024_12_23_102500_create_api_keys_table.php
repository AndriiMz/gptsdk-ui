<?php
// database/migrations/YYYY_MM_DD_create_repositories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_api_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('name');
            $table->string('key', 500);
            $table->enum('ai_vendor', [
                'openai', 'anthropic'
            ]);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_api_key');
    }
};
