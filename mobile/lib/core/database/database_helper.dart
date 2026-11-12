import 'dart:convert';
import 'package:flutter/services.dart';
import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';
import '../../models/questao_model.dart';

class DatabaseHelper {
  static final DatabaseHelper instance = DatabaseHelper._internal();
  DatabaseHelper._internal();

  static Database? _database;

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'biblia_show_v2.db');

    return await openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        await db.execute('''
          CREATE TABLE questoes (
            id INTEGER PRIMARY KEY,
            texto TEXT NOT NULL,
            opcoes TEXT NOT NULL,
            resposta_correta TEXT NOT NULL,
            explicacao TEXT,
            contexto TEXT,
            referencia_biblica TEXT,
            nivel INTEGER NOT NULL,
            dificuldade_nome TEXT NOT NULL,
            livro_nome TEXT,
            livro_abrev TEXT,
            testamento TEXT,
            categoria_nome TEXT
          )
        ''');

        await db.execute('''
          CREATE TABLE recordes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            jogador TEXT NOT NULL,
            pontuacao INTEGER NOT NULL,
            perguntas_acertadas INTEGER NOT NULL,
            fase_alcancada INTEGER NOT NULL,
            data_hora TEXT NOT NULL
          )
        ''');

        await _seedQuestoes(db);
      },
    );
  }

  Future<void> _seedQuestoes(Database db) async {
    try {
      final jsonString = await rootBundle.loadString('assets/data/questoes.json');
      final List<dynamic> list = jsonDecode(jsonString);

      final batch = db.batch();
      for (final item in list) {
        final q = QuestaoModel.fromJson(item as Map<String, dynamic>);
        batch.insert('questoes', q.toMap(), conflictAlgorithm: ConflictAlgorithm.replace);
      }
      await batch.commit(noResult: true);
    } catch (_) {}
  }

  Future<List<QuestaoModel>> buscarQuestoesPorFase(int fase, int limite) async {
    final db = await database;
    // Fase 1: nivel 1; Fase 2: nivel 2; Fase 3: nivel 3; Fase 4: nivel 4
    final result = await db.query(
      'questoes',
      where: 'nivel = ?',
      whereArgs: [fase],
      orderBy: 'RANDOM()',
      limit: limite,
    );

    if (result.isEmpty) {
      // Fallback para qualquer questão
      final fallback = await db.query('questoes', orderBy: 'RANDOM()', limit: limite);
      return fallback.map((m) => QuestaoModel.fromMap(m)).toList();
    }

    return result.map((m) => QuestaoModel.fromMap(m)).toList();
  }

  Future<void> salvarRecorde({
    required String jogador,
    required int pontuacao,
    required int acertos,
    required int fase,
  }) async {
    final db = await database;
    await db.insert('recordes', {
      'jogador': jogador,
      'pontuacao': pontuacao,
      'perguntas_acertadas': acertos,
      'fase_alcancada': fase,
      'data_hora': DateTime.now().toIso8601String(),
    });
  }

  Future<List<Map<String, dynamic>>> obterMelhoresRecordes({int limite = 10}) async {
    final db = await database;
    return await db.query(
      'recordes',
      orderBy: 'pontuacao DESC, acertos DESC',
      limit: limite,
    );
  }
}
