<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ UPDATE TABEL ALAT
        Schema::table('alat', function (Blueprint $table) {
            if (!Schema::hasColumn('alat', 'harga_alat')) {
                $table->decimal('harga_alat', 14, 2)
                    ->default(0)
                    ->after('deskripsi')
                    ->comment('Harga alat untuk perhitungan denda');
            }

            if (!Schema::hasColumn('alat', 'persen_denda_rusak')) {
                $table->integer('persen_denda_rusak')
                    ->default(30)
                    ->after('harga_alat')
                    ->comment('Persentase denda jika alat rusak (0-100)');
            }
        });

        // ✅ UPDATE TABEL PENGEMBALIAN
        Schema::table('pengembalian', function (Blueprint $table) {
            if (!Schema::hasColumn('pengembalian', 'denda_keterlambatan')) {
                $table->decimal('denda_keterlambatan', 14, 2)
                    ->default(0)
                    ->after('tarif_denda_per_hari')
                    ->comment('Denda keterlambatan');
            }

            if (!Schema::hasColumn('pengembalian', 'denda_barang')) {
                $table->decimal('denda_barang', 14, 2)
                    ->default(0)
                    ->after('denda_keterlambatan')
                    ->comment('Denda kerusakan atau hilang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            if (Schema::hasColumn('alat', 'harga_alat')) {
                $table->dropColumn('harga_alat');
            }
            if (Schema::hasColumn('alat', 'persen_denda_rusak')) {
                $table->dropColumn('persen_denda_rusak');
            }
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            if (Schema::hasColumn('pengembalian', 'denda_keterlambatan')) {
                $table->dropColumn('denda_keterlambatan');
            }
            if (Schema::hasColumn('pengembalian', 'denda_barang')) {
                $table->dropColumn('denda_barang');
            }
        });
    }
};