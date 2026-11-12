import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';
import '../services/api_service.dart';
import 'arena_play_screen.dart';

class ArenaJoinScreen extends StatefulWidget {
  final String nomeInicial;
  const ArenaJoinScreen({super.key, required this.nomeInicial});

  @override
  State<ArenaJoinScreen> createState() => _ArenaJoinScreenState();
}

class _ArenaJoinScreenState extends State<ArenaJoinScreen> {
  final TextEditingController _codigoController = TextEditingController();
  final TextEditingController _nomeController = TextEditingController();
  final TextEditingController _equipeController = TextEditingController();
  final TextEditingController _servidorController = TextEditingController(text: ApiService.instance.baseUrl);

  bool _isLoading = false;
  String? _erro;

  @override
  void initState() {
    super.initState();
    _nomeController.text = widget.nomeInicial;
  }

  @override
  void dispose() {
    _codigoController.dispose();
    _nomeController.dispose();
    _equipeController.dispose();
    _servidorController.dispose();
    super.dispose();
  }

  Future<void> _entrarNaSala() async {
    final codigo = _codigoController.text.trim().toUpperCase();
    final nome = _nomeController.text.trim();
    final equipe = _equipeController.text.trim().isEmpty ? null : _equipeController.text.trim();

    if (codigo.length != 6) {
      setState(() => _erro = 'O código da sala deve ter 6 caracteres.');
      return;
    }
    if (nome.isEmpty) {
      setState(() => _erro = 'Por favor, informe seu nome.');
      return;
    }

    setState(() {
      _isLoading = true;
      _erro = null;
    });

    ApiService.instance.setBaseUrl(_servidorController.text.trim());

    try {
      final res = await ApiService.instance.entrarNaSala(
        codigoAcesso: codigo,
        nome: nome,
        equipeNome: equipe,
      );

      final sala = res['sala'] as Map<String, dynamic>;
      final participante = res['participante'] as Map<String, dynamic>;

      if (mounted) {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(
            builder: (_) => ArenaPlayScreen(
              salaId: (sala['id'] as num).toInt(),
              codigoSala: codigo,
              participanteId: (participante['id'] as num).toInt(),
              jogadorNome: nome,
            ),
          ),
        );
      }
    } catch (e) {
      setState(() {
        _erro = e.toString().replaceFirst('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Entrar na Arena', style: TextStyle(fontWeight: FontWeight.w900)),
        backgroundColor: AppColors.backgroundTop,
        elevation: 0,
      ),
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.stageGradient),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const Text(
                  'Digite o código de 6 dígitos projetado no telão da igreja:',
                  style: TextStyle(color: Color(0xFFBFDBFE), fontSize: 13),
                ),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: AppColors.panelBgSolid,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: AppColors.goldBorder, width: 2),
                  ),
                  child: Column(
                    children: [
                      TextField(
                        controller: _codigoController,
                        textAlign: TextAlign.center,
                        textCapitalization: TextCapitalization.characters,
                        maxLength: 6,
                        style: const TextStyle(
                          color: AppColors.gold,
                          fontSize: 32,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 8,
                          fontFamily: 'monospace',
                        ),
                        decoration: const InputDecoration(
                          hintText: 'AB12CD',
                          hintStyle: TextStyle(color: Colors.white24, letterSpacing: 8),
                          counterText: '',
                          border: InputBorder.none,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                _buildInput(label: 'Seu Nome / Apelido', controller: _nomeController, icon: Icons.person),
                const SizedBox(height: 12),
                _buildInput(label: 'Equipe / Célula (Opcional)', controller: _equipeController, icon: Icons.group),
                const SizedBox(height: 12),
                ExpansionTile(
                  title: const Text('Configurar Servidor Laravel', style: TextStyle(color: Colors.white70, fontSize: 12)),
                  iconColor: Colors.white70,
                  collapsedIconColor: Colors.white38,
                  children: [
                    _buildInput(label: 'URL da API', controller: _servidorController, icon: Icons.dns),
                  ],
                ),
                if (_erro != null)
                  Container(
                    margin: const EdgeInsets.only(top: 14),
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.wrongRedBg,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: AppColors.wrongRed),
                    ),
                    child: Text(
                      _erro!,
                      style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                    ),
                  ),
                const SizedBox(height: 24),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.gold,
                    foregroundColor: Colors.black,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  ),
                  onPressed: _isLoading ? null : _entrarNaSala,
                  child: _isLoading
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.black),
                        )
                      : const Text(
                          'CONECTAR AO TELÃO →',
                          style: TextStyle(fontWeight: FontWeight.w900, fontSize: 15),
                        ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInput({required String label, required TextEditingController controller, required IconData icon}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: AppColors.panelBgSolid,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.panelBorder),
      ),
      child: TextField(
        controller: controller,
        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        decoration: InputDecoration(
          icon: Icon(icon, color: AppColors.gold, size: 20),
          border: InputBorder.none,
          labelText: label,
          labelStyle: const TextStyle(color: Colors.white60, fontSize: 13),
        ),
      ),
    );
  }
}
