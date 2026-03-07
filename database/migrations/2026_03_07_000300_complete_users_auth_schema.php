<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $this->ensureEmailColumn();

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }

            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable();
            }
        });
    }

    public function down(): void
    {
        // This migration completes an existing production schema and is not safely reversible.
    }

    private function ensureEmailColumn(): void
    {
        if (Schema::hasColumn('users', 'email')) {
            return;
        }

        if (Schema::hasColumn('users', 'mail')) {
            $driver = DB::getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement('ALTER TABLE users CHANGE mail email VARCHAR(255) NULL');
                return;
            }

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('mail', 'email');
            });

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable();
        });
    }
};
