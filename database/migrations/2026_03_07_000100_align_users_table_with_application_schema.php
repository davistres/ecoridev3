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

        if (!Schema::hasColumn('users', 'user_id') && Schema::hasColumn('users', 'id')) {
            $this->renamePrimaryKeyColumn();
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('Passager');
            }

            if (!Schema::hasColumn('users', 'n_credit')) {
                $table->integer('n_credit')->default(20);
            }

            if (!Schema::hasColumn('users', 'phototype')) {
                $table->string('phototype', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'pref_smoke')) {
                $table->string('pref_smoke', 20)->nullable();
            }

            if (!Schema::hasColumn('users', 'pref_pet')) {
                $table->string('pref_pet', 20)->nullable();
            }

            if (!Schema::hasColumn('users', 'pref_libre')) {
                $table->string('pref_libre', 255)->nullable();
            }
        });

        $this->addPhotoColumnIfMissing();
    }

    public function down(): void
    {
        // This migration aligns an existing table in-place and is not safely reversible.
    }

    private function renamePrimaryKeyColumn(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE users CHANGE id user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'user_id');
        });
    }

    private function addPhotoColumnIfMissing(): void
    {
        if (Schema::hasColumn('users', 'photo')) {
            return;
        }

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE users ADD COLUMN photo MEDIUMBLOB NULL');
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->binary('photo')->nullable();
        });
    }
};
