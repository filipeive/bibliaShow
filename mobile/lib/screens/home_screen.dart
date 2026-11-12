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
  final TextEditingController _nomeController = TextEditingController(text: 'Filipe');
  List<Map<String, dynamic>> _recordes = [];

  @override
  void initState() {
    super.initState();
    _carregarRecordes();
    AudioService.instance.playBackground('abertura');
  }

  Future<void> _carregarRecordes() async {
    final list = await DatabaseHelper.instance.obterMelhoresRecordes(limite: 5);
    if (mounted) {
      setState(() => _recordes = list);
    }
  }

  @override
  void dispose() {
    _nomeController.dispose();
    super.dispose();
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
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                  child: Column(
                    children: [
                      _buildLogoHero(),
                      const SizedBox(height: 24),
                      _buildNomeInput(),
                      const SizedBox(height: 20),
                      _buildModoSoloButton(context),
                      const SizedBox(height: 14),
                      _buildModoArenaButton(context),
                      const SizedBox(height: 28),
                      _buildPlacarRecordes(),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.amber.withOpacity(0.15),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.goldBorder),
            ),
            child: const Row(
              children: [
                Icon(Icons.wifi_off, size: 14, color: AppColors.gold),
                SizedBox(width: 6),
                Text(
                  'Modo Offline Ativo',
                  style: TextStyle(color: AppColors.goldLight, fontSize: 11, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          IconButton(
            icon: Icon(
              AudioService.instance.isMuted ? Icons.volume_off : Icons.volume_up,
              color: Colors.white,
            ),
            onPressed: () async {
              await AudioService.instance.toggleMute();
              setState(() {});
            },
          ),
        ],
      ),
    );
  }

  Widget _buildLogoHero() {
    return Column(
      children: [
        Container(
          width: 80,
          height: 80,
          decoration: BoxDecoration(
            gradient: AppColors.goldGradient,
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Colors.white, width: 2),
            boxShadow: [
              BoxShadow(
                color: AppColors.gold.withOpacity(0.4),
                blurRadius: 25,
                spreadRadius: 3,
              ),
            ],
          ),
          child: const Center(
            child: Icon(Icons.menu_book, color: Colors.black, size: 42),
          ),
        ),
        const SizedBox(height: 12),
        const Text(
          'Bíblia Show',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w900,
            letterSpacing: -0.5,
          ),
        ),
        Container(
          margin: const EdgeInsets.only(top: 4),
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
          decoration: BoxDecoration(
            color: AppColors.gold,
            borderRadius: BorderRadius.circular(20),
          ),
          child: const Text(
            'EDIÇÃO 2.0 • SHOW DO MILHÃO',
            style: TextStyle(color: Colors.black, fontSize: 10, fontWeight: FontWeight.w900),
          ),
        ),
      ],
    );
  }

  Widget _buildNomeInput() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: AppColors.panelBgSolid,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.panelBorder),
      ),
      child: TextField(
        controller: _nomeController,
        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        decoration: const InputDecoration(
          icon: Icon(Icons.person, color: AppColors.gold, size: 20),
          border: InputBorder.none,
          labelText: 'Seu Nome / Apelido',
          labelStyle: TextStyle(color: Colors.white60, fontSize: 13),
        ),
      ),
    );
  }

  Widget _buildModoSoloButton(BuildContext context) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: AppColors.goldGradient,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: AppColors.gold.withOpacity(0.4),
            blurRadius: 15,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(20),
          onTap: () {
            final game = Provider.of<GameEngineService>(context, listen: false);
            final nome = _nomeController.text.trim().isEmpty ? 'Jogador' : _nomeController.text.trim();
            game.iniciarNovaPartida(nome: nome);

            Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const SoloGameScreen()),
            ).then((_) => _carregarRecordes());
          },
          child: const Padding(
            padding: EdgeInsets.symmetric(vertical: 18, horizontal: 20),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.play_arrow_rounded, color: Colors.black, size: 28),
                SizedBox(width: 10),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'JOGAR MODO SOLO',
                      style: TextStyle(
                        color: Colors.black,
                        fontSize: 16,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.5,
                      ),
                    ),
                    Text(
                      '100% Offline • 1.000 Perguntas • 4 Fases',
                      style: TextStyle(color: Colors.black87, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildModoArenaButton(BuildContext context) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: AppColors.panelBgSolid,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFF38BDF8), width: 1.5),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(20),
          onTap: () {
            final nome = _nomeController.text.trim().isEmpty ? 'Jogador' : _nomeController.text.trim();
            Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => ArenaJoinScreen(nomeInicial: nome)),
            );
          },
          child: const Padding(
            padding: EdgeInsets.symmetric(vertical: 16, horizontal: 20),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.qr_code_scanner, color: Color(0xFF38BDF8), size: 24),
                SizedBox(width: 10),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'ENTRAR NA SALA (IGREJA)',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.5,
                      ),
                    ),
                    Text(
                      'Conectar ao Telão com PIN de 6 dígitos',
                      style: TextStyle(color: Color(0xFF93C5FD), fontSize: 11),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
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
              Icon(Icons.emoji_events, color: AppColors.gold, size: 20),
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
              'Nenhuma partida registrada ainda. Inicie sua primeira jornada!',
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
