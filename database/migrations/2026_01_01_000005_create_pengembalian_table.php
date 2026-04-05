<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->increments('pengembalian_id');
            $table->unsignedInteger('peminjaman_id');
            $table->date('tanggal_kembali_aktual');
            $table->integer('keterlambatan_hari')->default(0);
            $table->decimal('tarif_denda_per_hari', 10, 2)->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps(); // created_at & updated_at

            // Foreign key
            $table->foreign('peminjaman_id', 'fk_pengembalian_peminjaman')
                  ->references('peminjaman_id')->on('peminjaman')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Index
            $table->index('peminjaman_id', 'idx_pengembalian_peminjaman');
        });

        // Tambahkan kolom-kolom ENUM PostgreSQL
        DB::statement("ALTER TABLE pengembalian ADD COLUMN kondisi_alat kondisi_alat NOT NULL");
        DB::statement("ALTER TABLE pengembalian ADD COLUMN status_denda status_denda DEFAULT 'belum_lunas'::status_denda");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE TRIGGER update_pengembalian_updated_at
            BEFORE UPDATE ON pengembalian
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
