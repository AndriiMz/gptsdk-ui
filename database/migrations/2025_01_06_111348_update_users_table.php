<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('external_id')->nullable();
            $table->boolean('email_verified')->default(false);

            $table->unique('external_id', 'users_external_id_unique');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_external_id_unique');

            $table->dropColumn('external_id');
            $table->dropColumn('email_verified');
        });
    }
};
