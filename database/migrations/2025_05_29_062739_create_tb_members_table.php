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
        Schema::create('tb_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->string('full_name');
            $table->string('address');
            $table->string('phone');
            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('room_id')->on('tb_rooms')->onDelete('cascade');
            $table->date('move_in_date');
            $table->date('move_out_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_members');
    }
};
