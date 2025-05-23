<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_api_keys', function (Blueprint $table) {
            $table->boolean('use_for_generation')->default(false)->after('user_id');
            $table->string('default_model')->nullable()->after('use_for_generation');
        });
    }

    public function down(): void
    {
        Schema::table('ai_api_keys', function (Blueprint $table) {
            $table->dropColumn('use_for_generation');
        });
    }
};
