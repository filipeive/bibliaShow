<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            if (!Schema::hasColumn('salas', 'premio_primeiro_lugar')) {
                $table->string('premio_primeiro_lugar')->nullable()->after('status');
            }
            if (!Schema::hasColumn('salas', 'premio_segundo_lugar')) {
                $table->string('premio_segundo_lugar')->nullable()->after('premio_primeiro_lugar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('salas', 'premio_primeiro_lugar')) {
                $cols[] = 'premio_primeiro_lugar';
            }
            if (Schema::hasColumn('salas', 'premio_segundo_lugar')) {
                $cols[] = 'premio_segundo_lugar';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
