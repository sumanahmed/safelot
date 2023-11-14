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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 100)->unique()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedTinyInteger('owner_type')->comment('1=Dealer,2=Consumer')->default(1);
            $table->unsignedBigInteger('dealership_id')->nullable()->index();
            $table->string('nickname', 100)->index();
            $table->integer('stock')->default(1);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('year', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('photo')->nullable();
            $table->string('photo_base64')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('dealership_id')->on('dealerships')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
