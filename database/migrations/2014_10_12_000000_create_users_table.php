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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('name');
            $table->string('mobile', 30)->nullable()->index();
            $table->string('email')->unique()->index();
            $table->string('social_id')->nullable();
            $table->unsignedTinyInteger('account_type')->comment('1=Email,2=Google,3=Apple')->default(1);
            $table->unsignedTinyInteger('type')->comment('1=Admin User,2=Dealer,3=Consumer')->default(1);
            $table->unsignedTinyInteger('status')->comment('1=Active,2=Inactive')->default(2);
            $table->unsignedTinyInteger('otp_verified')->comment('1=Yes,2=No')->default(2);
            $table->string('photo')->nullable();
            $table->string('photo_base64')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable()->comment('for consumer');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('dealer_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
