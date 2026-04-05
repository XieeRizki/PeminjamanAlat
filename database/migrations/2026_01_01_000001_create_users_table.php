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
        // Create ENUM type for user_level
        DB::statement("CREATE TYPE user_level AS ENUM ('admin', 'petugas', 'peminjam')");

        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->timestamps(); // created_at & updated_at
        });

        // Add level column with custom ENUM type (PostgreSQL)
        DB::statement("ALTER TABLE users ADD COLUMN level user_level NOT NULL");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE TRIGGER update_users_updated_at
            BEFORE UPDATE ON users
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        DB::statement("DROP TYPE IF EXISTS user_level");
    }
};
