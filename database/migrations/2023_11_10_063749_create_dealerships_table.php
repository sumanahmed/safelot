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
        Schema::create('dealerships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name', 100);
            $table->string('address_1');
            $table->string('city_1');
            $table->string('state_1');
            $table->string('zip_1');
            $table->string('address_2')->nullable();
            $table->string('city_2')->nullable();
            $table->string('state_2')->nullable();
            $table->string('zip_2')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealerships');
    }
};
