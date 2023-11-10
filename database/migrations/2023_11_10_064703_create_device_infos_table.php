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
        Schema::create('device_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('model', 100);
            $table->string('supplier', 100)->nullable();
            $table->unsignedTinyInteger('status')->comment('1=Lock,2=Unlock')->default(1);
            $table->unsignedBigInteger('vehicle_id');
            $table->timestamps();
            $table->foreign('vehicle_id')->on('vehicles')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_infos');
    }
};
