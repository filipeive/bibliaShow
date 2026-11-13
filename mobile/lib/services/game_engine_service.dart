import 'dart:async';
import 'package:flutter/foundation.dart';
import '../core/audio/audio_service.dart';
import '../core/database/database_helper.dart';
import '../models/questao_model.dart';

enum GameStatus { loading, playing, answered, paused, finished }

class GameEngineService extends ChangeNotifier {
  final AudioService _audio = AudioService.instance;
  final DatabaseHelper _db = DatabaseHelper.instance;

  String jogadorNome = 'Filipe';
  List<QuestaoModel> _perguntas = [];
  int _indicePerguntaAtual = 0;

  int _pontuacaoAcumulada = 0;
  int _errosCometidos = 0;
  int _temposEsgotados = 0;

  // Ajudas
  int dicasRestantes = 3;
  int cartasRestantes = 1;
  int pulosRestantes = 3;
  int convidadosRestantes = 3;

  List<int> opcoesEliminadas = [];
  String? dicaAtiva;
  String? convidadoAtivo;

  int tempoRestante = 60;
  Timer? _timer;
  GameStatus status = GameStatus.loading;

  int? opcaoSelecionada;
  bool? acertouUltima;
  String? motivoFim;
  bool perdeuPartida = false;

  // Getters
  int get perguntaNumero => _indicePerguntaAtual + 1;
  int get totalPerguntas => _perguntas.isNotEmpty ? _perguntas.length : 24;
  int get pontuacao => _pontuacaoAcumulada;
  int get errosCometidos => _errosCometidos;
  int get temposEsgotados => _temposEsgotados;
  QuestaoModel? get perguntaAtual =>
      (_perguntas.isNotEmpty && _indicePerguntaAtual < _perguntas.length)
          ? _perguntas[_indicePerguntaAtual]
          : null;

  int get faseAtual {
    final num = perguntaNumero;
    if (num <= 6) return 1;
    if (num <= 12) return 2;
    if (num <= 18) return 3;
    return 4;
  }

  int get valorPerguntaAtual {
    switch (faseAtual) {
      case 1:
        return 10;
      case 2:
        return 20;
      case 3:
        return 30;
      case 4:
        return 50;
      default:
        return 10;
    }
  }

  int get valorAoParar => _pontuacaoAcumulada;
  int get valorAoErrar => faseAtual >= 4 ? 0 : (_pontuacaoAcumulada / 2).floor();
  int get valorAoAcertar => _pontuacaoAcumulada + valorPerguntaAtual;

  Future<void> iniciarNovaPartida({String nome = 'Filipe'}) async {
    jogadorNome = nome;
    status = GameStatus.loading;
    notifyListeners();

    _pontuacaoAcumulada = 0;
    _errosCometidos = 0;
    _temposEsgotados = 0;
    _indicePerguntaAtual = 0;
    motivoFim = null;
    perdeuPartida = false;
    dicasRestantes = 3;
    cartasRestantes = 1;
    pulosRestantes = 3;
    convidadosRestantes = 3;
    _resetarAjudasRodada();

    // Carregar 6 questões de cada uma das 4 fases do banco SQLite
    final f1 = await _db.buscarQuestoesPorFase(1, 6);
    final f2 = await _db.buscarQuestoesPorFase(2, 6);
    final f3 = await _db.buscarQuestoesPorFase(3, 6);
    final f4 = await _db.buscarQuestoesPorFase(4, 6);

    _perguntas = [...f1, ...f2, ...f3, ...f4];
    status = GameStatus.playing;

    _audio.playFase(1);
    _audio.playBackground('suspense');
    _iniciarCronometro();
    notifyListeners();
  }

  @visibleForTesting
  void carregarPartidaTeste(List<QuestaoModel> questoes, {String nome = 'Teste'}) {
    jogadorNome = nome;
    _pontuacaoAcumulada = 0;
    _errosCometidos = 0;
    _temposEsgotados = 0;
    _indicePerguntaAtual = 0;
    motivoFim = null;
    perdeuPartida = false;
    dicasRestantes = 3;
    cartasRestantes = 1;
    pulosRestantes = 3;
    convidadosRestantes = 3;
    _perguntas = List<QuestaoModel>.from(questoes);
    _resetarAjudasRodada();
    status = GameStatus.playing;
    notifyListeners();
  }

  void _resetarAjudasRodada() {
    opcoesEliminadas = [];
    dicaAtiva = null;
    convidadoAtivo = null;
    opcaoSelecionada = null;
    acertouUltima = null;
  }

  void _iniciarCronometro() {
    _timer?.cancel();
    tempoRestante = 60;

    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (status != GameStatus.playing) return;

      if (tempoRestante > 0) {
        tempoRestante--;
        if (tempoRestante <= 10 && tempoRestante > 0) {
          _audio.playTimerTick(tempoRestante);
        } else if (tempoRestante == 0) {
          _audio.playTimerTick(0);
          _tratarTempoEsgotado();
        }
        notifyListeners();
      }
    });
  }

  void _tratarTempoEsgotado() {
    _temposEsgotados++;
    _audio.vibrateError();

    if (_temposEsgotados >= 3) {
      encerrarPartida(
        motivo: 'Você esgotou as 3 chances de tempo!',
        aplicarPenalidadeErro: true,
      );
    } else {
      proximaPergunta();
    }
  }

  Future<void> responder(int indiceOpcao) async {
    if (status != GameStatus.playing || opcaoSelecionada != null) return;

    opcaoSelecionada = indiceOpcao;
    status = GameStatus.answered;
    _timer?.cancel();

    final q = perguntaAtual;
    if (q == null) return;

    final opcaoTexto = q.opcoes[indiceOpcao];
    final ehCorreta = opcaoTexto.trim().toLowerCase() == q.respostaCorreta.trim().toLowerCase();
    acertouUltima = ehCorreta;

    _audio.playFx('tecla');
    _audio.playTension(variante: _indicePerguntaAtual);
    _audio.vibrate(50);
    notifyListeners();

    // Delay de suspense de auditório (1.8 segundos com voz/tensão clássica)
    await Future.delayed(const Duration(milliseconds: 1800));

    if (ehCorreta) {
      _pontuacaoAcumulada += valorPerguntaAtual;
      _audio.playVerdict(isCorrect: true, fase: faseAtual);
      _audio.vibrateSuccess();
    } else {
      _errosCometidos++;
      _audio.playVerdict(isCorrect: false, fase: faseAtual);
      _audio.vibrateError();
    }

    notifyListeners();

    // Aguardar revelação e avançar
    await Future.delayed(const Duration(milliseconds: 2200));

    if (!ehCorreta && _errosCometidos >= 3) {
      encerrarPartida(
        motivo: 'Você cometeu 3 erros.',
        aplicarPenalidadeErro: true,
      );
    } else if (perguntaNumero >= totalPerguntas) {
      encerrarPartida(motivo: 'Parabéns! Você completou o Bíblia Show!');
    } else {
      proximaPergunta();
    }
  }

  void proximaPergunta() {
    if (_indicePerguntaAtual + 1 < _perguntas.length) {
      final faseAnterior = faseAtual;
      _indicePerguntaAtual++;
      _resetarAjudasRodada();
      status = GameStatus.playing;

      // Se mudou de fase (1->2, 2->3, 3->4) toca vinheta de abertura da fase
      if (faseAtual != faseAnterior) {
        _audio.playFase(faseAtual);
      }
      // Mantém/retoma a clássica trilha de suspense no fundo da pergunta
      _audio.playBackground('suspense');
      _iniciarCronometro();
      notifyListeners();
    } else {
      encerrarPartida();
    }
  }

  // Ajudas
  void usarCartas() {
    if (cartasRestantes <= 0 || status != GameStatus.playing) return;
    final q = perguntaAtual;
    if (q == null) return;

    cartasRestantes--;
    _audio.playFx('ajuda');
    _audio.vibrate(40);

    // Encontrar os índices das erradas
    final List<int> erradas = [];
    for (int i = 0; i < q.opcoes.length; i++) {
      if (q.opcoes[i].trim().toLowerCase() != q.respostaCorreta.trim().toLowerCase()) {
        erradas.add(i);
      }
    }

    erradas.shuffle();
    opcoesEliminadas = erradas.take(2).toList();
    notifyListeners();
  }

  void usarDica() {
    if (dicasRestantes <= 0 || status != GameStatus.playing || dicaAtiva != null) return;
    final q = perguntaAtual;
    if (q == null) return;

    dicasRestantes--;
    _audio.playFx('dica');
    _audio.vibrate(40);

    dicaAtiva = q.referenciaBiblica ?? q.contexto ?? 'Consulte o livro de ${q.livroNome ?? 'Gênesis'}.';
    notifyListeners();
  }

  void usarConvidado({int indiceConvidado = 0}) {
    if (convidadosRestantes <= 0 || status != GameStatus.playing || convidadoAtivo != null) return;
    final q = perguntaAtual;
    if (q == null) return;

    convidadosRestantes--;
    final nomes = ['Apóstolo Paulo', 'Apóstolo Pedro', 'Moisés'];
    final nome = nomes[indiceConvidado.clamp(0, nomes.length - 1)];
    final somIndex = (indiceConvidado + 1).clamp(1, 3);
    _audio.playFx('sabe$somIndex');
    _audio.vibrate(40);

    final letras = ['A', 'B', 'C', 'D'];
    int? idxCorreta;
    for (int i = 0; i < q.opcoes.length; i++) {
      if (q.opcoes[i].trim().toLowerCase() == q.respostaCorreta.trim().toLowerCase()) {
        idxCorreta = i;
        break;
      }
    }
    final letraTexto = idxCorreta != null && idxCorreta < letras.length
        ? 'a alternativa ${letras[idxCorreta]} — ${q.respostaCorreta}'
        : q.respostaCorreta;

    convidadoAtivo = '$nome: "Pelo contexto bíblico, creio que a resposta certa é $letraTexto!"';
    notifyListeners();
  }

  void usarPulo() {
    if (pulosRestantes <= 0 || status != GameStatus.playing) return;
    pulosRestantes--;
    _audio.playFx('inicio');
    _audio.vibrate(40);
    proximaPergunta();
  }

  void pararEJogo() {
    _audio.playFx('ranking');
    encerrarPartida(motivo: 'Você decidiu parar e garantir seus pontos!');
  }

  void encerrarPartida({String? motivo, bool aplicarPenalidadeErro = false}) {
    _timer?.cancel();
    status = GameStatus.finished;
    motivoFim = motivo;
    perdeuPartida = aplicarPenalidadeErro;
    if (aplicarPenalidadeErro) {
      _pontuacaoAcumulada = valorAoErrar;
    }
    _audio.stopBackground();
    _audio.playFx('fim1');

    _db.salvarRecorde(
      jogador: jogadorNome,
      pontuacao: _pontuacaoAcumulada,
      acertos: _indicePerguntaAtual,
      fase: faseAtual,
    ).catchError((_) {});

    notifyListeners();
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }
}
