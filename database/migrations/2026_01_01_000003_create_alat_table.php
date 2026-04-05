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
        Schema::create('alat', function (Blueprint $table) {
            $table->increments('alat_id');
            $table->unsignedInteger('kategori_id');
            $table->string('nama_alat', 100);
            $table->text('deskripsi')->nullable();
            $table->string('kode_alat', 50)->unique();
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->string('lokasi', 100)->nullable();
            $table->timestamps(); // created_at & updated_at

            // Foreign key
            $table->foreign('kategori_id', 'fk_alat_kategori')
                  ->references('kategori_id')->on('kategori')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Index
            $table->index('kategori_id', 'idx_alat_kategori');
        });

        // Tambahkan kolom kondisi dengan ENUM PostgreSQL
        DB::statement("ALTER TABLE alat ADD COLUMN kondisi kondisi_alat DEFAULT 'baik'::kondisi_alat");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE TRIGGER update_alat_updated_at
            BEFORE UPDATE ON alat
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
