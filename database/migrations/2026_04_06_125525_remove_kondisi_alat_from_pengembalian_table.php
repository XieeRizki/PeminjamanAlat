<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom ada sebelum dihapus
        if (Schema::hasColumn('pengembalian', 'kondisi_alat')) {
            Schema::table('pengembalian', function (Blueprint $table) {
                $table->dropColumn('kondisi_alat');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->string('kondisi_alat')->default('baik')->nullable();
        });
    }
};