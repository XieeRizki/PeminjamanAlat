<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('pengembalian_id');
            $table->string('kondisi_alat')->default('baik'); // Change from enum to string
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_alat', 10, 2)->nullable();
            $table->integer('persen_denda')->default(0);
            $table->decimal('denda_barang', 10, 2)->default(0);
            $table->timestamps();

            // Foreign key
            $table->foreign('pengembalian_id')
                  ->references('pengembalian_id')
                  ->on('pengembalian')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_detail');
    }
};