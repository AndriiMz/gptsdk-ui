<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // In the generated migration file
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('free_repositories_limit')->default(1);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('free_repositories_limit');
        });
    }
};
