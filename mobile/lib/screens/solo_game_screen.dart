import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/constants/app_colors.dart';
import '../services/game_engine_service.dart';
import '../widgets/confirm_exit_dialog.dart';
import '../widgets/sm_option_card.dart';
import 'result_screen.dart';

class SoloGameScreen extends StatelessWidget {
  const SoloGameScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<GameEngineService>(
      builder: (context, game, child) {
        if (game.status == GameStatus.finished) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(builder: (_) => const ResultScreen()),
            );
          });
        }

        return PopScope(
          canPop: false,
          onPopInvoked: (didPop) async {
            if (didPop) return;
            final querSair = await ConfirmExitDialog.mostrar(
              context,
              pontuacaoAtual: game.pontuacao,
              perguntaAtual: game.perguntaNumero,
              onConfirmExit: () {
                game.encerrarPartida(motivo: 'Partida encerrada pelo jogador.');
                Navigator.of(context).pop();
              },
            );
          },
          child: Scaffold(
            body: Container(
              decoration: const BoxDecoration(gradient: AppColors.stageGradient),
              child: SafeArea(
                child: game.status == GameStatus.loading
                    ? const Center(
                        child: CircularProgressIndicator(color: AppColors.gold),
                      )
                    : Column(
                        children: [
                          _buildTopBar(context, game),
                          Expanded(
                            child: SingleChildScrollView(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.stretch,
                                children: [
                                  _buildPerguntaCard(game),
                                  if (game.dicaAtiva != null) _buildDicaBox(game.dicaAtiva!),
                                  if (game.convidadoAtivo != null) _buildConvidadoBox(game.convidadoAtivo!),
                                  const SizedBox(height: 12),
                                  _buildAlternativas(game),
                                ],
                              ),
                            ),
                          ),
                          _buildBottomBar(game),
                        ],
                      ),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildTopBar(BuildContext context, GameEngineService game) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      decoration: BoxDecoration(
        color: AppColors.panelBg,
        border: const Border(bottom: BorderSide(color: AppColors.panelBorder)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.between,
        children: [
          Row(
            children: [
              IconButton(
                icon: const Icon(Icons.arrow_back_ios_new, color: Colors.white, size: 20),
                onPressed: () {
                  ConfirmExitDialog.mostrar(
                    context,
                    pontuacaoAtual: game.pontuacao,
                    perguntaAtual: game.perguntaNumero,
                    onConfirmExit: () {
                      game.encerrarPartida();
                      Navigator.of(context).pop();
                    },
                  );
                },
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'PERGUNTA ${game.perguntaNumero} DE ${game.totalPerguntas}',
                    style: const TextStyle(
                      color: AppColors.gold,
                      fontSize: 12,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 0.5,
                    ),
                  ),
                  Text(
                    '${game.faseAtual}ª Fase • Vale ${game.valorPerguntaAtual} pts',
                    style: const TextStyle(color: Colors.white70, fontSize: 11),
                  ),
                ],
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.black45,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: game.tempoRestante <= 10 ? AppColors.wrongRed : AppColors.goldBorder,
                width: 1.5,
              ),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.timer,
                  size: 16,
                  color: game.tempoRestante <= 10 ? AppColors.wrongRed : AppColors.gold,
                ),
                const SizedBox(width: 6),
                Text(
                  '${game.tempoRestante}s',
                  style: TextStyle(
                    color: game.tempoRestante <= 10 ? AppColors.wrongRed : Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    fontFamily: 'monospace',
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              gradient: AppColors.goldGradient,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              '${game.pontuacao} pts',
              style: const TextStyle(color: Colors.black, fontSize: 13, fontWeight: FontWeight.w900),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPerguntaCard(GameEngineService game) {
    final q = game.perguntaAtual;
    if (q == null) return const SizedBox.shrink();

    return Container(
      margin: const EdgeInsets.symmetric(vertical: 8),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: AppColors.questionGradient,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.goldBorder, width: 2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.6),
            blurRadius: 15,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (q.livroNome != null)
            Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                decoration: BoxDecoration(
                  color: Colors.black38,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.white24),
                ),
                child: Text(
                  '📖 ${q.livroNome} • ${q.dificuldadeNome}',
                  style: const TextStyle(color: AppColors.goldLight, fontSize: 11, fontWeight: FontWeight.bold),
                ),
              ),
            ),
          Text(
            q.texto,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 17,
              fontWeight: FontWeight.w900,
              height: 1.35,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDicaBox(String dica) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 4),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0x33F59E0B),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.goldBorder),
      ),
      child: Row(
        children: [
          const Icon(Icons.lightbulb, color: AppColors.gold, size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              dica,
              style: const TextStyle(color: AppColors.goldLight, fontSize: 12, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildConvidadoBox(String msg) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 4),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0x333B82F6),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.blueAccent),
      ),
      child: Row(
        children: [
          const Icon(Icons.record_voice_over, color: Colors.blueAccent, size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              msg,
              style: const TextStyle(color: Color(0xFFBFDBFE), fontSize: 12, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAlternativas(GameEngineService game) {
    final q = game.perguntaAtual;
    if (q == null) return const SizedBox.shrink();

    final letters = ['A', 'B', 'C', 'D'];

    return Column(
      children: List.generate(q.opcoes.length, (idx) {
        final letter = letters[idx];
        final text = q.opcoes[idx];
        final isEliminada = game.opcoesEliminadas.contains(idx);

        OptionState state = OptionState.idle;
        if (isEliminada) {
          state = OptionState.eliminated;
        } else if (game.opcaoSelecionada == idx) {
          if (game.acertouUltima == null) {
            state = OptionState.selected;
          } else if (game.acertouUltima == true) {
            state = OptionState.correct;
          } else {
            state = OptionState.wrong;
          }
        } else if (game.acertouUltima != null &&
            text.trim().toLowerCase() == q.respostaCorreta.trim().toLowerCase()) {
          state = OptionState.correct;
        }

        return SmOptionCard(
          letter: letter,
          text: text,
          state: state,
          onTap: (isEliminada || game.status != GameStatus.playing)
              ? null
              : () => game.responder(idx),
        );
      }),
    );
  }

  Widget _buildBottomBar(GameEngineService game) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: AppColors.panelBgSolid,
        border: const Border(top: BorderSide(color: AppColors.panelBorder)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildActionButton(
            label: 'Cartas',
            count: game.cartasRestantes,
            icon: Icons.style,
            onTap: game.cartasRestantes > 0 && game.status == GameStatus.playing
                ? () => game.usarCartas()
                : null,
          ),
          _buildActionButton(
            label: 'Dica',
            count: game.dicasRestantes,
            icon: Icons.lightbulb_outline,
            onTap: game.dicasRestantes > 0 && game.status == GameStatus.playing
                ? () => game.usarDica()
                : null,
          ),
          _buildActionButton(
            label: 'Convidados',
            count: game.convidadosRestantes,
            icon: Icons.people_outline,
            onTap: game.convidadosRestantes > 0 && game.status == GameStatus.playing
                ? () => game.usarConvidado()
                : null,
          ),
          _buildActionButton(
            label: 'Pular',
            count: game.pulosRestantes,
            icon: Icons.skip_next,
            onTap: game.pulosRestantes > 0 && game.status == GameStatus.playing
                ? () => game.usarPulo()
                : null,
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF881337),
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: game.status == GameStatus.playing ? () => game.pararEJogo() : null,
            child: const Text('Parar', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12)),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButton({
    required String label,
    required int count,
    required IconData icon,
    VoidCallback? onTap,
  }) {
    final disabled = onTap == null;
    return Opacity(
      opacity: disabled ? 0.35 : 1.0,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(icon, color: AppColors.gold, size: 22),
              const SizedBox(height: 2),
              Text(
                '$label ($count)',
                style: const TextStyle(color: Colors.white70, fontSize: 10, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
