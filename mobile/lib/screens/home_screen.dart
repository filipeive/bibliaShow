import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/audio/audio_service.dart';
import '../core/constants/app_colors.dart';
import '../core/database/database_helper.dart';
import '../services/game_engine_service.dart';
import 'arena_join_screen.dart';
import 'solo_game_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final TextEditingController _nomeController = TextEditingController(text: 'Jogador');
  final TextEditingController _pinController = TextEditingController();
  List<Map<String, dynamic>> _recordes = [];

  @override
  void initState() {
    super.initState();
    _carregarRecordes();
    AudioService.instance.playBackground('abertura');
  }

  Future<void> _carregarRecordes() async {
    try {
      final list = await DatabaseHelper.instance.obterMelhoresRecordes(limite: 5);
      if (mounted) {
        setState(() => _recordes = list);
      }
    } catch (_) {}
  }

  @override
  void dispose() {
    _nomeController.dispose();
    _pinController.dispose();
    super.dispose();
  }

  void _iniciarSolo(BuildContext context) {
    final game = Provider.of<GameEngineService>(context, listen: false);
    final nome = _nomeController.text.trim().isEmpty ? 'Jogador' : _nomeController.text.trim();
    game.iniciarNovaPartida(nome: nome);

    Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => const SoloGameScreen()),
    ).then((_) => _carregarRecordes());
  }

  void _abrirArena(BuildContext context) {
    final nome = _nomeController.text.trim().isEmpty ? 'Jogador' : _nomeController.text.trim();
    Navigator.of(context).push(
      MaterialPageRoute(builder: (_) => ArenaJoinScreen(nomeInicial: nome)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.stageGradient),
        child: SafeArea(
          child: Column(
            children: [
              _buildHeader(),
              Expanded(
                child: LayoutBuilder(
                  builder: (context, constraints) {
                    final isWide = constraints.maxWidth >= 760;
                    return SingleChildScrollView(
                      padding: EdgeInsets.symmetric(
                        horizontal: isWide ? 32 : 20,
                        vertical: isWide ? 24 : 12,
                      ),
                      child: Center(
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 1020),
                          child: Column(
                            children: [
                              _buildTitleBanner(),
                              SizedBox(height: isWide ? 28 : 18),
                              _buildClassicStage(context, isWide),
                              const SizedBox(height: 28),
                              ConstrainedBox(
                                constraints: const BoxConstraints(maxWidth: 680),
                                child: _buildPlacarRecordes(),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
      decoration: const BoxDecoration(
        color: AppColors.panelBg,
        border: Border(bottom: BorderSide(color: AppColors.panelBorder)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.amber.withOpacity(0.14),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: AppColors.goldBorder),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.offline_bolt_rounded, size: 15, color: AppColors.gold),
                    SizedBox(width: 6),
                    Text(
                      '100% Offline Pronto',
                      style: TextStyle(
                        color: AppColors.goldLight,
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          Row(
            children: [
              IconButton(
                tooltip: 'Instruções e Regras',
                icon: const Icon(Icons.help_outline_rounded, color: AppColors.goldLight),
                onPressed: () => _abrirModalInstrucoes(context),
              ),
              IconButton(
                tooltip: AudioService.instance.isMuted ? 'Ativar Áudio' : 'Silenciar Áudio',
                icon: Icon(
                  AudioService.instance.isMuted ? Icons.volume_off_rounded : Icons.volume_up_rounded,
                  color: Colors.white,
                ),
                onPressed: () async {
                  await AudioService.instance.toggleMute();
                  setState(() {});
                },
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTitleBanner() {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
          decoration: BoxDecoration(
            color: Colors.red.shade900.withOpacity(0.85),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppColors.goldBorder),
          ),
          child: const Text(
            'EDIÇÃO 2.0 • SHOW DO MILHÃO BÍBLICO',
            style: TextStyle(
              color: AppColors.goldLight,
              fontSize: 10,
              fontWeight: FontWeight.w900,
              letterSpacing: 1.4,
            ),
          ),
        ),
        const SizedBox(height: 10),
        ShaderMask(
          shaderCallback: (bounds) => const LinearGradient(
            colors: [Color(0xFFFEF08A), Color(0xFFF59E0B), Color(0xFFFDE047)],
          ).createShader(bounds),
          child: const Text(
            'Bíblia Show',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.white,
              fontSize: 38,
              fontWeight: FontWeight.w900,
              letterSpacing: 1.5,
              height: 1.05,
            ),
          ),
        ),
        const SizedBox(height: 6),
        const Text(
          'A Nova Maneira de Aprender a Bíblia',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: Color(0xFFBFDBFE),
            fontSize: 13,
            fontWeight: FontWeight.w600,
            letterSpacing: 0.4,
          ),
        ),
      ],
    );
  }

  Widget _buildClassicStage(BuildContext context, bool isWide) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.symmetric(
        horizontal: isWide ? 36 : 16,
        vertical: isWide ? 32 : 20,
      ),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            const Color(0xFF0E256E).withOpacity(0.85),
            const Color(0xFF07133B).withOpacity(0.95),
          ],
        ),
        border: Border.all(color: AppColors.goldBorder.withOpacity(0.55), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.45),
            blurRadius: 30,
            offset: const Offset(0, 14),
          ),
        ],
      ),
      child: isWide ? _buildWideStageContent(context) : _buildMobileStageContent(context),
    );
  }

  Widget _buildWideStageContent(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // Cápsulas em arco à esquerda abraçando o medalhão
        SizedBox(
          width: 290,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Padding(
                padding: const EdgeInsets.only(right: 0),
                child: _buildCapsuleButton(
                  label: 'Jogar',
                  icon: Icons.play_arrow_rounded,
                  isPrimary: true,
                  onTap: () => _abrirModalJogar(context),
                ),
              ),
              const SizedBox(height: 14),
              Padding(
                padding: const EdgeInsets.only(right: 26),
                child: _buildCapsuleButton(
                  label: 'Entrar na Sala',
                  icon: Icons.qr_code_scanner_rounded,
                  onTap: () => _abrirArena(context),
                ),
              ),
              const SizedBox(height: 14),
              Padding(
                padding: const EdgeInsets.only(right: 26),
                child: _buildCapsuleButton(
                  label: 'Instruções',
                  icon: Icons.menu_book_rounded,
                  onTap: () => _abrirModalInstrucoes(context),
                ),
              ),
              const SizedBox(height: 14),
              Padding(
                padding: const EdgeInsets.only(right: 0),
                child: _buildCapsuleButton(
                  label: 'Ranking',
                  icon: Icons.emoji_events_rounded,
                  onTap: () => _abrirModalRanking(context),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: 34),
        _buildMedallionHero(size: 250),
      ],
    );
  }

  Widget _buildMobileStageContent(BuildContext context) {
    return Column(
      children: [
        _buildMedallionHero(size: 185),
        const SizedBox(height: 22),
        _buildCapsuleButton(
          label: 'Jogar',
          icon: Icons.play_arrow_rounded,
          isPrimary: true,
          fullWidth: true,
          onTap: () => _abrirModalJogar(context),
        ),
        const SizedBox(height: 12),
        _buildCapsuleButton(
          label: 'Entrar na Sala',
          icon: Icons.qr_code_scanner_rounded,
          fullWidth: true,
          onTap: () => _abrirArena(context),
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _buildCapsuleButton(
                label: 'Instruções',
                icon: Icons.menu_book_rounded,
                fullWidth: true,
                onTap: () => _abrirModalInstrucoes(context),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: _buildCapsuleButton(
                label: 'Ranking',
                icon: Icons.emoji_events_rounded,
                fullWidth: true,
                onTap: () => _abrirModalRanking(context),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildMedallionHero({required double size}) {
    return Container(
      width: size,
      height: size,
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFFFDE68A), Color(0xFFD97706), Color(0xFF1D4ED8)],
        ),
        boxShadow: [
          BoxShadow(
            color: AppColors.gold.withOpacity(0.35),
            blurRadius: 32,
            spreadRadius: 4,
          ),
        ],
      ),
      child: Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: const RadialGradient(
            colors: [Color(0xFF1E3A8A), Color(0xFF071138)],
          ),
          border: Border.all(color: AppColors.goldLight.withOpacity(0.6), width: 2),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.menu_book_rounded,
              color: AppColors.goldLight,
              size: size * 0.28,
            ),
            const SizedBox(height: 8),
            Text(
              '1.000+',
              style: TextStyle(
                color: AppColors.gold,
                fontSize: size * 0.11,
                fontWeight: FontWeight.w900,
              ),
            ),
            Text(
              'PERGUNTAS BÍBLICAS',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: Colors.white70,
                fontSize: size * 0.048,
                fontWeight: FontWeight.w800,
                letterSpacing: 1.0,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCapsuleButton({
    required String label,
    required IconData icon,
    required VoidCallback onTap,
    bool isPrimary = false,
    bool fullWidth = false,
  }) {
    return SizedBox(
      width: fullWidth ? double.infinity : 245,
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(999),
          child: Ink(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(999),
              gradient: isPrimary
                  ? AppColors.goldGradient
                  : const LinearGradient(
                      colors: [Color(0xFF1D4ED8), Color(0xFF0F256E)],
                    ),
              border: Border.all(
                color: isPrimary ? AppColors.goldLight : const Color(0xFF60A5FA).withOpacity(0.55),
                width: 1.5,
              ),
              boxShadow: [
                BoxShadow(
                  color: (isPrimary ? AppColors.gold : const Color(0xFF1D4ED8)).withOpacity(0.32),
                  blurRadius: 14,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  icon,
                  color: isPrimary ? Colors.black : AppColors.goldLight,
                  size: 20,
                ),
                const SizedBox(width: 10),
                Flexible(
                  child: Text(
                    label,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      color: isPrimary ? Colors.black : Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 0.4,
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

  void _abrirModalJogar(BuildContext context) {
    showDialog<void>(
      context: context,
      builder: (ctx) {
        return Dialog(
          backgroundColor: AppColors.panelBgSolid,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
            side: const BorderSide(color: AppColors.goldBorder, width: 1.5),
          ),
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 520),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(22),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Escolha seu Modo de Jogo',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.close, color: Colors.white60),
                        onPressed: () => Navigator.of(ctx).pop(),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  // Card 1: Jogar Sozinho (Offline)
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0A1A4F),
                      borderRadius: BorderRadius.circular(18),
                      border: Border.all(color: AppColors.goldBorder),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                              decoration: BoxDecoration(
                                color: AppColors.gold.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: const Text(
                                '100% OFFLINE',
                                style: TextStyle(
                                  color: AppColors.goldLight,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w900,
                                ),
                              ),
                            ),
                            const Text(
                              '4 Fases • 24 Perguntas',
                              style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),
                        const Text(
                          'Jogar Sozinho (Modo Individual)',
                          style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Jogue sem internet usando Cartas 50/50, Dicas Bíblicas e Convidados.',
                          style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 12),
                        ),
                        const SizedBox(height: 12),
                        TextField(
                          controller: _nomeController,
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                          decoration: InputDecoration(
                            filled: true,
                            fillColor: AppColors.backgroundBottom,
                            prefixIcon: const Icon(Icons.person, color: AppColors.gold, size: 20),
                            hintText: 'Digite seu nome...',
                            hintStyle: const TextStyle(color: Colors.white38),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(color: AppColors.panelBorder),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: const BorderSide(color: AppColors.panelBorder),
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),
                        ElevatedButton.icon(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.gold,
                            foregroundColor: Colors.black,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
                          ),
                          icon: const Icon(Icons.play_arrow_rounded),
                          label: const Text(
                            'Iniciar Partida Solo',
                            style: TextStyle(fontWeight: FontWeight.w900),
                          ),
                          onPressed: () {
                            Navigator.of(ctx).pop();
                            _iniciarSolo(context);
                          },
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 14),
                  // Card 2: Entrar na Sala (Arena Ao Vivo)
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0A1A4F),
                      borderRadius: BorderRadius.circular(18),
                      border: Border.all(color: const Color(0xFF38BDF8).withOpacity(0.6)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'ARENA AO VIVO (ONLINE)',
                              style: TextStyle(
                                color: Color(0xFF38BDF8),
                                fontSize: 10,
                                fontWeight: FontWeight.w900,
                              ),
                            ),
                            Text(
                              'Igrejas & Grupos',
                              style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'Entrar numa Sala',
                          style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Conecte-se ao telão da igreja usando o código PIN de 6 caracteres.',
                          style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 12),
                        ),
                        const SizedBox(height: 12),
                        OutlinedButton.icon(
                          style: OutlinedButton.styleFrom(
                            foregroundColor: Colors.white,
                            side: const BorderSide(color: Color(0xFF38BDF8)),
                            padding: const EdgeInsets.symmetric(vertical: 13),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
                          ),
                          icon: const Icon(Icons.qr_code_scanner_rounded, color: Color(0xFF38BDF8)),
                          label: const Text(
                            'Entrar na Sala com PIN',
                            style: TextStyle(fontWeight: FontWeight.w900),
                          ),
                          onPressed: () {
                            Navigator.of(ctx).pop();
                            _abrirArena(context);
                          },
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  void _abrirModalInstrucoes(BuildContext context) {
    showDialog<void>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppColors.panelBgSolid,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(22),
          side: const BorderSide(color: AppColors.goldBorder),
        ),
        title: const Row(
          children: [
            Icon(Icons.menu_book_rounded, color: AppColors.gold),
            SizedBox(width: 10),
            Text('Como Jogar', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
          ],
        ),
        content: const SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              _RegraItem(
                titulo: '4 Fases Progressivas',
                desc: '1ª Fase (10 pts), 2ª Fase (20 pts), 3ª Fase (30 pts) e 4ª Fase Final (50 pts).',
              ),
              _RegraItem(
                titulo: 'Ajudas Especiais',
                desc: 'Use Cartas 50/50 (elimina 2 erradas), Dicas Bíblicas, Convidados ou Pular.',
              ),
              _RegraItem(
                titulo: 'Parar ou Errar',
                desc: 'Ao parar você garante 100% dos pontos acumulados. 3 erros encerram a partida.',
              ),
              _RegraItem(
                titulo: 'Atalhos no Computador',
                desc: 'No Windows, use as teclas A, B, C, D (ou 1-4) para responder mais rápido.',
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Fechar', style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _abrirModalRanking(BuildContext context) {
    showDialog<void>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: AppColors.panelBgSolid,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(22),
          side: const BorderSide(color: AppColors.goldBorder),
        ),
        title: const Row(
          children: [
            Icon(Icons.emoji_events_rounded, color: AppColors.gold),
            SizedBox(width: 10),
            Text('Ranking Offline', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
          ],
        ),
        content: SizedBox(
          width: 360,
          child: _recordes.isEmpty
              ? const Text(
                  'Nenhuma partida offline registrada ainda.',
                  style: TextStyle(color: Colors.white70, fontSize: 13),
                )
              : Column(
                  mainAxisSize: MainAxisSize.min,
                  children: List.generate(_recordes.length, (idx) {
                    final r = _recordes[idx];
                    return ListTile(
                      dense: true,
                      contentPadding: EdgeInsets.zero,
                      leading: CircleAvatar(
                        radius: 14,
                        backgroundColor: idx == 0 ? AppColors.gold : Colors.white12,
                        child: Text(
                          '${idx + 1}º',
                          style: TextStyle(
                            color: idx == 0 ? Colors.black : Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                      title: Text(
                        r['jogador'] as String? ?? 'Jogador',
                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                      ),
                      trailing: Text(
                        '${r['pontuacao']} pts',
                        style: const TextStyle(
                          color: AppColors.goldLight,
                          fontWeight: FontWeight.w900,
                          fontFamily: 'monospace',
                        ),
                      ),
                    );
                  }),
                ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Fechar', style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildPlacarRecordes() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.panelBgSolid,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.panelBorder),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.emoji_events_rounded, color: AppColors.gold, size: 20),
              SizedBox(width: 8),
              Text(
                'SEUS RECORDES LOCAIS (OFFLINE)',
                style: TextStyle(
                  color: AppColors.gold,
                  fontSize: 12,
                  fontWeight: FontWeight.w900,
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          if (_recordes.isEmpty)
            const Text(
              'Nenhuma partida registrada ainda. Clique em Jogar para começar!',
              style: TextStyle(color: Colors.white60, fontSize: 12),
            )
          else
            ...List.generate(_recordes.length, (idx) {
              final r = _recordes[idx];
              return Padding(
                padding: const EdgeInsets.symmetric(vertical: 6),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Text(
                          '${idx + 1}º',
                          style: TextStyle(
                            color: idx == 0 ? AppColors.gold : Colors.white60,
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                          ),
                        ),
                        const SizedBox(width: 10),
                        Text(
                          r['jogador'] as String? ?? 'Jogador',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                        ),
                      ],
                    ),
                    Text(
                      '${r['pontuacao']} pts',
                      style: const TextStyle(
                        color: AppColors.goldLight,
                        fontWeight: FontWeight.w900,
                        fontFamily: 'monospace',
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }
}

class _RegraItem extends StatelessWidget {
  final String titulo;
  final String desc;

  const _RegraItem({required this.titulo, required this.desc});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(titulo, style: const TextStyle(color: AppColors.goldLight, fontWeight: FontWeight.w900, fontSize: 13)),
          const SizedBox(height: 2),
          Text(desc, style: const TextStyle(color: Colors.white70, fontSize: 12)),
        ],
      ),
    );
  }
}
