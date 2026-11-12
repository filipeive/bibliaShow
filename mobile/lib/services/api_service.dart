import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static final ApiService instance = ApiService._internal();
  ApiService._internal();

  // URL padrão de produção com subcaminho /bibliashow
  static const String defaultServerUrl = 'http://146.235.224.99/bibliashow';
  String baseUrl = defaultServerUrl;

  Future<void> init() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final savedUrl = prefs.getString('biblia_show_server_url');
      if (savedUrl != null && savedUrl.trim().isNotEmpty) {
        baseUrl = normalizarUrl(savedUrl);
      }
    } catch (_) {}
  }

  String normalizarUrl(String url) {
    String u = url.trim();
    if (!u.startsWith('http://') && !u.startsWith('https://')) {
      u = 'http://$u';
    }
    // Assegurar subcaminho se for IP de produção e usuário não digitou /bibliashow
    if ((u.contains('146.235.224.99') || u.contains('bibliashow')) && !u.contains('/bibliashow')) {
      u = '$u/bibliashow';
    }
    return u.replaceAll(RegExp(r'/+$'), '');
  }

  Future<void> setBaseUrl(String url) async {
    baseUrl = normalizarUrl(url);
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('biblia_show_server_url', baseUrl);
    } catch (_) {}
  }

  Future<Map<String, dynamic>> entrarNaSala({
    required String codigoAcesso,
    required String nome,
    String? equipeNome,
  }) async {
    final uri = Uri.parse('$baseUrl/api/salas/entrar');
    final resp = await http.post(
      uri,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'codigo_acesso': codigoAcesso.trim().toUpperCase(),
        'nome': nome.trim(),
        if (equipeNome != null && equipeNome.trim().isNotEmpty) 'equipe_nome': equipeNome.trim(),
      }),
    ).timeout(const Duration(seconds: 10));

    Map<String, dynamic>? data;
    try {
      data = jsonDecode(resp.body) as Map<String, dynamic>?;
    } catch (_) {}

    if (resp.statusCode >= 200 && resp.statusCode < 300 && data != null) {
      return data;
    } else {
      final msg = data?['error'] ??
          (data?['errors'] is Map ? (data!['errors'] as Map).values.map((v) => v is List ? v.join(' ') : v.toString()).join(' ') : null) ??
          'Erro ao entrar na sala (${resp.statusCode}). Verifique o código e o servidor.';
      throw Exception(msg);
    }
  }

  Future<Map<String, dynamic>> obterEstadoSala(int salaId, {int? participanteId}) async {
    final query = participanteId != null ? '?participante_id=$participanteId' : '';
    final uri = Uri.parse('$baseUrl/api/salas/$salaId$query');
    final resp = await http.get(
      uri,
      headers: {'Accept': 'application/json'},
    ).timeout(const Duration(seconds: 6));

    if (resp.statusCode == 200) {
      return jsonDecode(resp.body) as Map<String, dynamic>;
    } else {
      throw Exception('Não foi possível sincronizar com a sala (${resp.statusCode}).');
    }
  }

  Future<Map<String, dynamic>> responderRodada({
    required int salaId,
    required int participanteId,
    int? questaoId,
    required String resposta,
    int? tempoResposta,
  }) async {
    final uri = Uri.parse('$baseUrl/api/salas/$salaId/responder');
    final resp = await http.post(
      uri,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'participante_id': participanteId,
        'questao_id': questaoId,
        'resposta': resposta,
        'tempo_resposta': tempoResposta ?? 0,
      }),
    ).timeout(const Duration(seconds: 8));

    Map<String, dynamic>? data;
    try {
      data = jsonDecode(resp.body) as Map<String, dynamic>?;
    } catch (_) {}

    if (resp.statusCode >= 200 && resp.statusCode < 300 && data != null) {
      return data;
    } else {
      throw Exception(data?['error'] ?? 'Erro ao enviar resposta à arena.');
    }
  }

  Future<Map<String, dynamic>> usarAjuda({
    required int salaId,
    required int participanteId,
    required String tipo,
    int? indiceConvidado,
  }) async {
    final uri = Uri.parse('$baseUrl/api/salas/$salaId/ajuda');
    final resp = await http.post(
      uri,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'participante_id': participanteId,
        'tipo': tipo,
        if (indiceConvidado != null) 'indice_convidado': indiceConvidado,
      }),
    ).timeout(const Duration(seconds: 8));

    Map<String, dynamic>? data;
    try {
      data = jsonDecode(resp.body) as Map<String, dynamic>?;
    } catch (_) {}

    if (resp.statusCode >= 200 && resp.statusCode < 300 && data != null) {
      return data;
    } else {
      throw Exception(data?['error'] ?? 'Erro ao solicitar ajuda.');
    }
  }
}
