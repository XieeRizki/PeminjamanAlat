<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedBigInteger('user_id')->nullable(); // FK ke users.id (Laravel default bigint)
            $table->string('aktivitas', 255);
            $table->string('modul', 50);
            $table->timestamp('timestamp')->useCurrent();

            $table->foreign('user_id', 'fk_log_user')
                  ->references('user_id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->index('user_id', 'idx_log_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
