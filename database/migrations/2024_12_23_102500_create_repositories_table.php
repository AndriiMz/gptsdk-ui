<?php
// database/migrations/YYYY_MM_DD_create_repositories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('token');
            $table->string('owner');
            $table->string('name');
            $table->string('url');
            $table->enum('type', ['github', 'temp'])->default('github');
            $table->string('subscription_id')->nullable();
            $table->enum('subscription_status', ['paid', 'free'])->default('free');

            $table->index('subscription_id');
            $table->index('user_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repository');
    }
};
