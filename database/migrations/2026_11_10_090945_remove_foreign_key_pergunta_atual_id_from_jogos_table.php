<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta ALTER TABLE DROP CONSTRAINT diretamente.
        // Recria a tabela sem a FK constraint em pergunta_atual_id,
        // pois o dado fonte de verdade está em dados_motor (serializado).
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('jogos_novos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['aguardando', 'em_curso', 'pausado', 'terminado'])->default('aguardando');
            $table->unsignedBigInteger('pergunta_atual_id')->nullable();
            $table->integer('pontuacao_total')->default(0);
            $table->longText('dados_motor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('INSERT INTO jogos_novos SELECT * FROM jogos');

        Schema::drop('jogos');
        DB::statement('ALTER TABLE jogos_novos RENAME TO jogos');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('jogos_antigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['aguardando', 'em_curso', 'pausado', 'terminado'])->default('aguardando');
            $table->foreignId('pergunta_atual_id')->nullable()->constrained('questoes')->nullOnDelete();
            $table->integer('pontuacao_total')->default(0);
            $table->longText('dados_motor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('INSERT INTO jogos_antigos SELECT * FROM jogos');

        Schema::drop('jogos');
        DB::statement('ALTER TABLE jogos_antigos RENAME TO jogos');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
