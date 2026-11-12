class QuestaoModel {
  final int id;
  final String texto;
  final List<String> opcoes;
  final String respostaCorreta;
  final String? explicacao;
  final String? contexto;
  final String? referenciaBiblica;
  final int nivel;
  final String dificuldadeNome;
  final String? livroNome;
  final String? livroAbrev;
  final String? testamento;
  final String? categoriaNome;
  final Map<String, dynamic>? ajudas;

  QuestaoModel({
    required this.id,
    required this.texto,
    required this.opcoes,
    required this.respostaCorreta,
    this.explicacao,
    this.contexto,
    this.referenciaBiblica,
    required this.nivel,
    required this.dificuldadeNome,
    this.livroNome,
    this.livroAbrev,
    this.testamento,
    this.categoriaNome,
    this.ajudas,
  });

  factory QuestaoModel.fromJson(Map<String, dynamic> json) {
    List<String> parsedOpcoes = [];
    if (json['opcoes'] is List) {
      parsedOpcoes = (json['opcoes'] as List).map((e) => e.toString()).toList();
    }

    return QuestaoModel(
      id: json['id'] as int,
      texto: json['texto'] as String? ?? '',
      opcoes: parsedOpcoes,
      respostaCorreta: json['resposta_correta'] as String? ?? '',
      explicacao: json['explicacao'] as String?,
      contexto: json['contexto'] as String?,
      referenciaBiblica: json['referencia_biblica'] as String?,
      nivel: (json['nivel'] as num?)?.toInt() ?? 1,
      dificuldadeNome: json['dificuldade_nome'] as String? ?? 'Iniciante',
      livroNome: json['livro_nome'] as String?,
      livroAbrev: json['livro_abrev'] as String?,
      testamento: json['testamento'] as String?,
      categoriaNome: json['categoria_nome'] as String?,
      ajudas: json['ajudas'] is Map<String, dynamic> ? json['ajudas'] as Map<String, dynamic> : null,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'texto': texto,
      'opcoes': opcoes.join('|||'),
      'resposta_correta': respostaCorreta,
      'explicacao': explicacao,
      'contexto': contexto,
      'referencia_biblica': referenciaBiblica,
      'nivel': nivel,
      'dificuldade_nome': dificuldadeNome,
      'livro_nome': livroNome,
      'livro_abrev': livroAbrev,
      'testamento': testamento,
      'categoria_nome': categoriaNome,
    };
  }

  factory QuestaoModel.fromMap(Map<String, dynamic> map) {
    final rawOpcoes = map['opcoes'] as String? ?? '';
    return QuestaoModel(
      id: map['id'] as int,
      texto: map['texto'] as String? ?? '',
      opcoes: rawOpcoes.isNotEmpty ? rawOpcoes.split('|||') : [],
      respostaCorreta: map['resposta_correta'] as String? ?? '',
      explicacao: map['explicacao'] as String?,
      contexto: map['contexto'] as String?,
      referenciaBiblica: map['referencia_biblica'] as String?,
      nivel: (map['nivel'] as num?)?.toInt() ?? 1,
      dificuldadeNome: map['dificuldade_nome'] as String? ?? 'Iniciante',
      livroNome: map['livro_nome'] as String?,
      livroAbrev: map['livro_abrev'] as String?,
      testamento: map['testamento'] as String?,
      categoriaNome: map['categoria_nome'] as String?,
    );
  }
}
