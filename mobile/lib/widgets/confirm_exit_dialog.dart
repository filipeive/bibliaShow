import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

class ConfirmExitDialog extends StatelessWidget {
  final int pontuacaoAtual;
  final int perguntaAtual;
  final VoidCallback onConfirmExit;

  const ConfirmExitDialog({
    super.key,
    required this.pontuacaoAtual,
    required this.perguntaAtual,
    required this.onConfirmExit,
  });

  static Future<bool> mostrar(
    BuildContext context, {
    required int pontuacaoAtual,
    required int perguntaAtual,
    required VoidCallback onConfirmExit,
  }) async {
    final result = await showDialog<bool>(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => ConfirmExitDialog(
        pontuacaoAtual: pontuacaoAtual,
        perguntaAtual: perguntaAtual,
        onConfirmExit: onConfirmExit,
      ),
    );
    return result ?? false;
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: Colors.transparent,
      insetPadding: const EdgeInsets.symmetric(horizontal: 24),
      child: Container(
        decoration: BoxDecoration(
          color: AppColors.panelBgSolid,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: AppColors.goldBorder, width: 2),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.8),
              blurRadius: 30,
              spreadRadius: 5,
            ),
          ],
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: Colors.amber.withOpacity(0.15),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.gold, width: 2),
              ),
              child: const Icon(Icons.pause_circle_outline, color: AppColors.gold, size: 32),
            ),
            const SizedBox(height: 16),
            const Text(
              'Pausar Partida?',
              style: TextStyle(
                color: Colors.white,
                fontSize: 20,
                fontWeight: FontWeight.w900,
                letterSpacing: -0.5,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Você está na Pergunta $perguntaAtual de 24 com $pontuacaoAtual pontos acumulados.',
              textAlign: TextAlign.center,
              style: const TextStyle(
                color: Color(0xFFBFDBFE),
                fontSize: 13,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      side: BorderSide(color: Colors.white.withOpacity(0.2)),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    onPressed: () {
                      Navigator.of(context).pop(false);
                    },
                    child: const Text(
                      'Continuar',
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFE11D48),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    onPressed: () {
                      Navigator.of(context).pop(true);
                      onConfirmExit();
                    },
                    child: const Text(
                      'Sair do Jogo',
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
