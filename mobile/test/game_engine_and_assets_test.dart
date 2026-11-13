import 'dart:convert';
import 'dart:io';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:biblia_show/models/questao_model.dart';
import 'package:biblia_show/services/api_service.dart';
import 'package:biblia_show/services/game_engine_service.dart';

List<QuestaoModel> _gerarQuestoes(int quantidade) {
  return List.generate(quantidade, (i) {
    final fase = (i ~/ 6) + 1;
    return QuestaoModel(
      id: i + 1,
      texto: 'Pergunta de teste ${i + 1}',
      opcoes: const ['A Certa', 'B Errada', 'C Errada', 'D Errada'],
      respostaCorreta: 'A Certa',
      referenciaBiblica: 'Gênesis 1:${i + 1}',
      nivel: fase.clamp(1, 4),
      dificuldadeNome: 'Fácil',
      livroNome: 'Gênesis',
    );
  });
}

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  setUpAll(() {
    final messenger = TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger;
    for (final channelName in ['xyz.luan/audioplayers.global', 'xyz.luan/audioplayers']) {
      messenger.setMockMethodCallHandler(MethodChannel(channelName), (MethodCall call) async {
        return null;
      });
    }
  });

  group('GameEngineService - Consistência de Regras', () {
    test('calcula as 4 fases e valores 10, 20, 30, 50 corretamente', () {
      final engine = GameEngineService();
      engine.carregarPartidaTeste(_gerarQuestoes(24));

      // Pergunta 1 -> Fase 1
      expect(engine.perguntaNumero, 1);
      expect(engine.faseAtual, 1);
      expect(engine.valorPerguntaAtual, 10);

      // Pular até pergunta 7 (Fase 2) -> usa 3 pulos e proximaPergunta
      for (int i = 0; i < 6; i++) {
        engine.proximaPergunta();
      }
      expect(engine.perguntaNumero, 7);
      expect(engine.faseAtual, 2);
      expect(engine.valorPerguntaAtual, 20);

      // Avança até pergunta 13 (Fase 3)
      for (int i = 0; i < 6; i++) {
        engine.proximaPergunta();
      }
      expect(engine.perguntaNumero, 13);
      expect(engine.faseAtual, 3);
      expect(engine.valorPerguntaAtual, 30);

      // Avança até pergunta 19 (Fase 4 - Milhão)
      for (int i = 0; i < 6; i++) {
        engine.proximaPergunta();
      }
      expect(engine.perguntaNumero, 19);
      expect(engine.faseAtual, 4);
      expect(engine.valorPerguntaAtual, 50);
      // Regra Fase 4: errar = 0
      expect(engine.valorAoErrar, 0);
    });

    test('cartas 50/50 elimina exatamente 2 alternativas incorretas mantendo a correta', () {
      final engine = GameEngineService();
      engine.carregarPartidaTeste(_gerarQuestoes(24));

      expect(engine.cartasRestantes, 1);
      engine.usarCartas();

      expect(engine.cartasRestantes, 0);
      expect(engine.opcoesEliminadas.length, 2);
      expect(engine.opcoesEliminadas.contains(0), isFalse);

      // Segunda tentativa na mesma partida não faz efeito
      engine.usarCartas();
      expect(engine.cartasRestantes, 0);
    });

    test('dica bíblica e convidado permitem no máximo 1 uso por pergunta e 3 por partida', () {
      final engine = GameEngineService();
      engine.carregarPartidaTeste(_gerarQuestoes(24));

      engine.usarDica();
      expect(engine.dicasRestantes, 2);
      expect(engine.dicaAtiva, 'Gênesis 1:1');

      // Bloqueado na mesma pergunta
      engine.usarDica();
      expect(engine.dicasRestantes, 2);

      engine.usarConvidado(indiceConvidado: 1);
      expect(engine.convidadosRestantes, 2);
      expect(engine.convidadoAtivo, contains('Apóstolo Pedro'));

      // Bloqueado na mesma pergunta
      engine.usarConvidado(indiceConvidado: 0);
      expect(engine.convidadosRestantes, 2);

      // Pular limpa ajudas da rodada
      engine.usarPulo();
      expect(engine.pulosRestantes, 2);
      expect(engine.dicaAtiva, isNull);
      expect(engine.convidadoAtivo, isNull);
    });

    test('pararEJogo mantém 100% da pontuação acumulada', () {
      final engine = GameEngineService();
      engine.carregarPartidaTeste(_gerarQuestoes(24));

      engine.pararEJogo();
      expect(engine.status, GameStatus.finished);
      expect(engine.perdeuPartida, isFalse);
      expect(engine.pontuacao, 0);
    });

    test('encerrarPartida por 3 erros aplica penalidade de errar', () {
      final engine = GameEngineService();
      engine.carregarPartidaTeste(_gerarQuestoes(24));

      engine.encerrarPartida(motivo: 'Você cometeu 3 erros.', aplicarPenalidadeErro: true);
      expect(engine.status, GameStatus.finished);
      expect(engine.perdeuPartida, isTrue);
      expect(engine.motivoFim, 'Você cometeu 3 erros.');
    });
  });

  group('Assets Offline & ApiService', () {
    test('assets/data/questoes.json é válido e possui questões para todas as fases', () {
      final file = File('assets/data/questoes.json');
      expect(file.existsSync(), isTrue);

      final List<dynamic> decoded = jsonDecode(file.readAsStringSync()) as List<dynamic>;
      expect(decoded.length, greaterThan(50));

      final questoes = decoded
          .map((e) => QuestaoModel.fromJson(e as Map<String, dynamic>))
          .toList();

      for (final q in questoes) {
        expect(q.texto.trim(), isNotEmpty);
        expect(q.opcoes.length, greaterThanOrEqualTo(2));
        expect(q.respostaCorreta.trim(), isNotEmpty);
      }
    });

    test('arquivos de áudio essenciais existem em assets/audio/', () {
      const essenciais = [
        'abertura.wav',
        'suspense.wav',
        'tecla.wav',
        'ajuda.wav',
        'dica.wav',
        'certa1.wav',
        'errou1.wav',
        'fase1.wav',
        'fase2.wav',
        'fase3.wav',
        'fase4.wav',
        'fim1.wav',
        'sabe1.wav',
        'tempo_tick.wav',
        'tempo_alerta.wav',
      ];
      for (final nome in essenciais) {
        expect(File('assets/audio/$nome').existsSync(), isTrue, reason: 'Falta áudio $nome');
      }
    });

    test('ApiService normaliza URL com subcaminho /bibliashow', () {
      final api = ApiService.instance;
      expect(api.normalizarUrl('https://146.235.224.99/'), 'https://146.235.224.99/bibliashow');
      expect(api.normalizarUrl('https://146.235.224.99/bibliashow/'), 'https://146.235.224.99/bibliashow');
    });
  });
}
