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
        Schema::create('tb_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('member_id')->on('tb_members')->onDelete('cascade');
            $table->string('duration');
            $table->date('payment_date');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_payments');
    }
};
