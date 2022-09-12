<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user1s', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->string('user_id', 20);
            $table->string('referer_id', 20)->default('');
            $table->string('hash', 11);
            $table->string('lang', 3)->default('');
            $table->string('first_name', 64)->default('');
            $table->string('last_name', 64)->default('');
            $table->string('username', 64)->default('');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('is_start_button')->default(0);
            $table->tinyInteger('is_capcha_checked')->default(0);
            $table->tinyInteger('is_subscribe_checked')->default(0);
            $table->tinyInteger('is_referer_bonus_payout')->default(0);
            $table->integer('subscribe_count')->default(0);
            $table->integer('join_group_count')->default(0);
            $table->integer('bonus_count')->default(0);
            $table->decimal('referrals_earned')->default(0.00);
            $table->decimal('expected_to_pay')->default(0.00);
            $table->decimal('output_amount')->default(0.00);
            $table->decimal('earned')->default(0.00);
            $table->decimal('balance')->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user1s');
    }
};
