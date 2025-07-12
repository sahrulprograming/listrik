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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_levels')->default(2);
            $table->foreign('id_levels')->references('id')->on('levels')->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_levels')->default(2);
            $table->foreign('id_levels')->references('id')->on('levels')->onDelete("CASCADE")->onUpdate("CASCADE");
        });
    }
};
