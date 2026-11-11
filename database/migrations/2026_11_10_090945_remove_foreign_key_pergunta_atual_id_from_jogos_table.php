<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // No-op idempotente: em versões anteriores jogos.pergunta_atual_id possuía constraint de FK,
        // mas na definição canônica da migration create_jogos_table já é definida como unsignedBigInteger nullable sem FK.
    }

    public function down(): void
    {
        // No-op
    }
};
