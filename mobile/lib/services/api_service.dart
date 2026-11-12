import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static final ApiService instance = ApiService._internal();
  ApiService._internal();

  // URL base do backend Laravel (pode ser configurada pelo usuário nas definições)
  String baseUrl = 'http://10.0.2.2:8000'; // 10.0.2.2 para emulador Android; ou IP local para celular real

  void setBaseUrl(String url) {
    baseUrl = url.replaceAll(RegExp(r'/+$'), '');
  }

  Future<Map<String, dynamic>> entrarNaSala({
    required String codigoAcesso,
    required String nome,
    String? equipeNome,
  }) async {
    final uri = Uri.parse('$baseUrl/api/salas/$codigoAcesso/participar');
    final resp = await http.post(
      uri,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'nome': nome,
        'equipe_nome': equipeNome,
      }),
    );

    final data = jsonDecode(resp.body) as Map<String, dynamic>;
    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      return data;
    } else {
      throw Exception(data['error'] ?? data['mensagem'] ?? 'Erro ao entrar na sala.');
    }
  }

  Future<Map<String, dynamic>> obterEstadoSala(int salaId) async {
    final uri = Uri.parse('$baseUrl/api/salas/$salaId');
    final resp = await http.get(uri, headers: {'Accept': 'application/json'});

    if (resp.statusCode == 200) {
      return jsonDecode(resp.body) as Map<String, dynamic>;
    } else {
      throw Exception('Não foi possível sincronizar com a sala.');
    }
  }

  Future<Map<String, dynamic>> responderRodada({
    required int salaId,
    required int participanteId,
    required int questaoId,
    required int indiceOpcao,
  }) async {
    final uri = Uri.parse('$baseUrl/api/salas/$salaId/resposta');
    final resp = await http.post(
      uri,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'participante_id': participanteId,
        'questao_id': questaoId,
        'indice_opcao': indiceOpcao,
      }),
    );

    final data = jsonDecode(resp.body) as Map<String, dynamic>;
    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      return data;
    } else {
      throw Exception(data['error'] ?? 'Erro ao enviar resposta.');
    }
  }
}
