import 'dart:async';
import 'package:flutter/material.dart';
import '../core/audio/audio_service.dart';
import '../core/constants/app_colors.dart';
import '../services/api_service.dart';
import '../widgets/confirm_exit_dialog.dart';
import '../widgets/sm_option_card.dart';

class ArenaPlayScreen extends StatefulWidget {
  final int salaId;
  final String codigoSala;
  final int participanteId;
  final String jogadorNome;

  const ArenaPlayScreen({
    super.key,
    required this.salaId,
    required this.codigoSala,
    required this.participanteId,
    required this.jogadorNome,
  });

  @override
  State<ArenaPlayScreen> createState() => _ArenaPlayScreenState();
}

class _ArenaPlayScreenState extends State<ArenaPlayScreen> {
  Timer? _syncTimer;
  Map<String, dynamic>? _salaData;
  int? _questaoIdAtual;
  int? _opcaoBloqueada;
  bool _jaRevelado = false;
  int _pontuacao = 0;

  @override
  void initState() {
    super.initState();
    _sincronizar();
    _syncTimer = Timer.periodic(const Duration(milliseconds: 1500), (_) => _sincronizar());
  }

  @override
  void dispose() {
    _syncTimer?.cancel();
    super.dispose();
  }

  Future<void> _sincronizar() async {
    try {
      final data = await ApiService.instance.obterEstadoSala(
        widget.salaId,
        participanteId: widget.participanteId,
      );
      if (!mounted) return;

      setState(() {
        _salaData = data;
        final ranking = (data['ranking'] as List?) ?? [];
        final eu = ranking.firstWhere(
          (p) => p['id'] == widget.participanteId,
          orElse: () => null,
        );
        if (eu != null) {
          _pontuacao = (eu['pontuacao'] as num?)?.toInt() ?? 0;
        }

        final q = data['pergunta_atual'] as Map<String, dynamic>?;
        if (q != null) {
          final id = (q['id'] as num?)?.toInt() ?? (data['numero_pergunta'] as num?)?.toInt();
          if (_questaoIdAtual != id) {
            _questaoIdAtual = id;
            _opcaoBloqueada = null;
            _jaRevelado = false;
          }
        }

        final ajudas = (data['ajudas'] as Map<String, dynamic>?) ?? {};
        final gabaritoRevelado = ajudas['gabarito_revelado'] == true;

        if (gabaritoRevelado && !_jaRevelado && _opcaoBloqueada != null) {
          _jaRevelado = true;
          final correta = (q?['resposta_correta'] as String? ?? '').trim().toLowerCase();
          final opcoes = (q?['opcoes'] as List?) ?? [];
          final minhaEscolha = opcoes.length > _opcaoBloqueada! ? opcoes[_opcaoBloqueada!].toString().trim().toLowerCase() : '';

          if (minhaEscolha == correta) {
            AudioService.instance.playFx('certa1');
            AudioService.instance.vibrateSuccess();
          } else {
            AudioService.instance.playFx('errou1');
            AudioService.instance.vibrateError();
          }
        }
      });
    } catch (_) {}
  }

  Future<void> _bloquearResposta(int idx) async {
    if (_opcaoBloqueada != null) return;
    final q = _salaData?['pergunta_atual'] as Map<String, dynamic>?;
    final opcoes = (q?['opcoes'] as List?) ?? [];
    if (idx >= opcoes.length) return;

    setState(() => _opcaoBloqueada = idx);
    AudioService.instance.playFx('tecla');
    AudioService.instance.playTension();
    AudioService.instance.vibrate(50);

    try {
      final opcaoTexto = opcoes[idx].toString();
      await ApiService.instance.responderRodada(
        salaId: widget.salaId,
        participanteId: widget.participanteId,
        questaoId: _questaoIdAtual,
        resposta: opcaoTexto,
      );
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final q = _salaData?['pergunta_atual'] as Map<String, dynamic>?;
    final estado = _salaData?['estado'] as String? ?? 'aguardando';
    final tempo = _salaData?['tempo_restante'] as num? ?? 60;
    final ajudas = (_salaData?['ajudas'] as Map<String, dynamic>?) ?? {};
    final gabaritoRevelado = ajudas['gabarito_revelado'] == true;
    final eliminadas = (ajudas['opcoes_eliminadas'] as List?)?.map((e) => (e as num).toInt()).toList() ?? [];

    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        await ConfirmExitDialog.mostrar(
          context,
          pontuacaoAtual: _pontuacao,
          perguntaAtual: _salaData?['numero_pergunta'] as int? ?? 1,
          onConfirmExit: () => Navigator.of(context).pop(),
        );
      },
      child: Scaffold(
        body: Container(
          decoration: const BoxDecoration(gradient: AppColors.stageGradient),
          child: SafeArea(
            child: Column(
              children: [
                _buildHeader(tempo.toInt()),
                Expanded(
                  child: q == null || estado != 'em_curso'
                      ? _buildAguardandoState(estado)
                      : SingleChildScrollView(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              _buildPerguntaBox(q),
                              const SizedBox(height: 12),
                              _buildAlternativas(q, eliminadas, gabaritoRevelado),
                            ],
                          ),
                        ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(int tempo) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      decoration: const BoxDecoration(
        color: AppColors.panelBg,
        border: Border(bottom: BorderSide(color: AppColors.panelBorder)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              IconButton(
                icon: const Icon(Icons.arrow_back_ios_new, color: Colors.white, size: 18),
                onPressed: () {
                  ConfirmExitDialog.mostrar(
                    context,
                    pontuacaoAtual: _pontuacao,
                    perguntaAtual: _salaData?['numero_pergunta'] as int? ?? 1,
                    onConfirmExit: () => Navigator.of(context).pop(),
                  );
                },
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'SALA ${widget.codigoSala}',
                    style: const TextStyle(color: AppColors.gold, fontWeight: FontWeight.w900, fontSize: 13),
                  ),
                  Text(
                    widget.jogadorNome,
                    style: const TextStyle(color: Colors.white70, fontSize: 11),
                  ),
                ],
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.black45,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: tempo <= 5 ? AppColors.wrongRed : AppColors.goldBorder),
            ),
            child: Text(
              '${tempo}s',
              style: TextStyle(
                color: tempo <= 5 ? AppColors.wrongRed : Colors.white,
                fontWeight: FontWeight.w900,
                fontFamily: 'monospace',
                fontSize: 14,
              ),
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              gradient: AppColors.goldGradient,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              '$_pontuacao pts',
              style: const TextStyle(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 12),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAguardandoState(String estado) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const CircularProgressIndicator(color: AppColors.gold),
            const SizedBox(height: 20),
            Text(
              estado == 'pausado' ? 'Partida Pausada pelo Apresentador' : 'Aguardando o Telão...',
              style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 6),
            const Text(
              'Fique atento ao telão da igreja para a próxima rodada!',
              style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 12),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPerguntaBox(Map<String, dynamic> q) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: AppColors.questionGradient,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.goldBorder, width: 2),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (q['livro_nome'] != null)
            Text(
              '📖 ${q['livro_nome']}',
              style: const TextStyle(color: AppColors.goldLight, fontSize: 11, fontWeight: FontWeight.bold),
            ),
          const SizedBox(height: 4),
          Text(
            q['texto']?.toString() ?? '',
            style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900, height: 1.3),
          ),
        ],
      ),
    );
  }

  Widget _buildAlternativas(Map<String, dynamic> q, List<int> eliminadas, bool gabaritoRevelado) {
    final opcoes = (q['opcoes'] as List?)?.map((e) => e.toString()).toList() ?? [];
    final correta = (q['resposta_correta'] as String? ?? '').trim().toLowerCase();
    final letters = ['A', 'B', 'C', 'D'];

    return Column(
      children: List.generate(opcoes.length, (idx) {
        final isEliminada = eliminadas.contains(idx);
        final text = opcoes[idx];

        OptionState state = OptionState.idle;
        if (isEliminada) {
          state = OptionState.eliminated;
        } else if (_opcaoBloqueada == idx) {
          if (!gabaritoRevelado) {
            state = OptionState.selected;
          } else {
            state = (text.trim().toLowerCase() == correta) ? OptionState.correct : OptionState.wrong;
          }
        } else if (gabaritoRevelado && text.trim().toLowerCase() == correta) {
          state = OptionState.correct;
        }

        return SmOptionCard(
          letter: letters[idx],
          text: text,
          state: state,
          onTap: (isEliminada || _opcaoBloqueada != null) ? null : () => _bloquearResposta(idx),
        );
      }),
    );
  }
}
