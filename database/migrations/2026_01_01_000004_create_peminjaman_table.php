<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->increments('peminjaman_id');
            $table->unsignedBigInteger('user_id');       // FK ke users.id (Laravel default bigint)
            $table->unsignedInteger('alat_id');
            $table->integer('jumlah');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_kembali_rencana');
            $table->text('tujuan_peminjaman')->nullable();
            $table->unsignedBigInteger('disetujui_oleh')->nullable(); // FK ke users.id
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'fk_peminjaman_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('alat_id', 'fk_peminjaman_alat')
                  ->references('alat_id')->on('alat')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('disetujui_oleh', 'fk_peminjaman_disetujui')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->index('user_id', 'idx_peminjaman_user');
            $table->index('alat_id', 'idx_peminjaman_alat');
        });

        DB::statement("ALTER TABLE peminjaman ADD COLUMN status status_peminjaman DEFAULT 'menunggu'::status_peminjaman");
        DB::statement("CREATE INDEX idx_peminjaman_status ON peminjaman USING btree (status)");
        DB::statement("
            CREATE TRIGGER update_peminjaman_updated_at
            BEFORE UPDATE ON peminjaman
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
