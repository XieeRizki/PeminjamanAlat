<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat function trigger update_updated_at_column dan ENUM types global.
     */
    public function up(): void
    {
        // Function untuk auto-update kolom updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // ENUM: kondisi_alat
        DB::statement("CREATE TYPE kondisi_alat AS ENUM ('baik', 'rusak', 'hilang')");

        // ENUM: status_peminjaman
        DB::statement("CREATE TYPE status_peminjaman AS ENUM ('menunggu', 'disetujui', 'ditolak', 'dikembalikan')");

        // ENUM: status_denda
        DB::statement("CREATE TYPE status_denda AS ENUM ('lunas', 'belum_lunas')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE IF EXISTS status_denda");
        DB::statement("DROP TYPE IF EXISTS status_peminjaman");
        DB::statement("DROP TYPE IF EXISTS kondisi_alat");
        DB::statement("DROP FUNCTION IF EXISTS update_updated_at_column()");
    }
};
