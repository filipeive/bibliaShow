import 'dart:math';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/constants/app_colors.dart';
import '../services/game_engine_service.dart';
import 'solo_game_screen.dart';

class ResultScreen extends StatefulWidget {
  const ResultScreen({super.key});

  @override
  State<ResultScreen> createState() => _ResultScreenState();
}

class _ResultScreenState extends State<ResultScreen> with SingleTickerProviderStateMixin {
  late final AnimationController _confettiController;
  late final List<_ConfettiPiece> _pieces;

  @override
  void initState() {
    super.initState();
    final rng = Random(42);
    const palette = [
      Color(0xFFF59E0B),
      Color(0xFFFDE68A),
      Color(0xFF38BDF8),
      Color(0xFF10B981),
      Color(0xFFF43F5E),
    ];
    _pieces = List.generate(
      45,
      (_) => _ConfettiPiece(
        x: rng.nextDouble(),
        y: rng.nextDouble(),
        speed: 0.25 + rng.nextDouble() * 0.45,
        size: 5 + rng.nextDouble() * 6,
        color: palette[rng.nextInt(palette.length)],
        drift: (rng.nextDouble() - 0.5) * 0.12,
      ),
    );
    _confettiController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 6),
    )..repeat();
  }

  @override
  void dispose() {
    _confettiController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final game = Provider.of<GameEngineService>(context, listen: false);

    return Scaffold(
      body: Stack(
        children: [
          Container(
            decoration: const BoxDecoration(gradient: AppColors.stageGradient),
          ),
          Positioned.fill(
            child: IgnorePointer(
              child: AnimatedBuilder(
                animation: _confettiController,
                builder: (_, __) => CustomPaint(
                  painter: _ConfettiPainter(
                    pieces: _pieces,
                    progress: _confettiController.value,
                  ),
                ),
              ),
            ),
          ),
          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 520),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 96,
                        height: 96,
                        decoration: BoxDecoration(
                          gradient: AppColors.goldGradient,
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2.5),
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.gold.withOpacity(0.5),
                              blurRadius: 30,
                              spreadRadius: 5,
                            ),
                          ],
                        ),
                        child: const Center(
                          child: Icon(Icons.emoji_events_rounded, color: Colors.black, size: 50),
                        ),
                      ),
                      const SizedBox(height: 18),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppColors.gold.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: AppColors.goldBorder),
                        ),
                        child: const Text(
                          'PARTIDA FINALIZADA',
                          style: TextStyle(
                            color: AppColors.goldLight,
                            fontSize: 10,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 1.2,
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      const Text(
                        'Show Concluído!',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 28,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        'Parabéns, ${game.jogadorNome}!',
                        style: const TextStyle(color: Color(0xFFBFDBFE), fontSize: 15, fontWeight: FontWeight.w600),
                      ),
                      const SizedBox(height: 24),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                        decoration: BoxDecoration(
                          color: AppColors.panelBgSolid,
                          borderRadius: BorderRadius.circular(22),
                          border: Border.all(color: AppColors.goldBorder, width: 2),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.35),
                              blurRadius: 20,
                              offset: const Offset(0, 10),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            const Text(
                              'PONTUAÇÃO FINAL',
                              style: TextStyle(
                                color: AppColors.gold,
                                fontSize: 12,
                                fontWeight: FontWeight.w900,
                                letterSpacing: 1,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              '${game.pontuacao}',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 44,
                                fontWeight: FontWeight.w900,
                                fontFamily: 'monospace',
                              ),
                            ),
                            const Divider(color: Colors.white12, height: 24),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceAround,
                              children: [
                                _buildStatItem('Perguntas', '${game.perguntaNumero}/24'),
                                _buildStatItem('Fase Alcançada', '${game.faseAtual}ª'),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 28),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.gold,
                            foregroundColor: Colors.black,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
                            elevation: 5,
                          ),
                          icon: const Icon(Icons.replay_rounded),
                          onPressed: () {
                            game.iniciarNovaPartida(nome: game.jogadorNome);
                            Navigator.of(context).pushReplacement(
                              MaterialPageRoute(builder: (_) => const SoloGameScreen()),
                            );
                          },
                          label: const Text(
                            'Jogar Novamente',
                            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900),
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton.icon(
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            side: const BorderSide(color: AppColors.panelBorder),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
                          ),
                          icon: const Icon(Icons.home_rounded, color: Colors.white),
                          onPressed: () {
                            Navigator.of(context).popUntil((route) => route.isFirst);
                          },
                          label: const Text(
                            'Voltar ao Menu Inicial',
                            style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem(String label, String value) {
    return Column(
      children: [
        Text(label, style: const TextStyle(color: Colors.white60, fontSize: 11)),
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(color: AppColors.goldLight, fontSize: 16, fontWeight: FontWeight.w900),
        ),
      ],
    );
  }
}

class _ConfettiPiece {
  final double x;
  final double y;
  final double speed;
  final double size;
  final Color color;
  final double drift;

  const _ConfettiPiece({
    required this.x,
    required this.y,
    required this.speed,
    required this.size,
    required this.color,
    required this.drift,
  });
}

class _ConfettiPainter extends CustomPainter {
  final List<_ConfettiPiece> pieces;
  final double progress;

  _ConfettiPainter({required this.pieces, required this.progress});

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()..style = PaintingStyle.fill;
    for (final p in pieces) {
      final dy = ((p.y + progress * p.speed * 2.0) % 1.0) * size.height;
      final dx = ((p.x + sin(progress * 2 * pi + p.y * 10) * p.drift) % 1.0) * size.width;
      paint.color = p.color.withOpacity(0.75);
      canvas.drawRRect(
        RRect.fromRectAndRadius(
          Rect.fromCenter(center: Offset(dx, dy), width: p.size, height: p.size * 0.6),
          const Radius.circular(2),
        ),
        paint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant _ConfettiPainter oldDelegate) => oldDelegate.progress != progress;
}
