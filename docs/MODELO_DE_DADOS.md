# Modelo de Dados — Bíblia Show 2.0

## Tabelas principais

### igrejas
Multi-tenant lógico. Cada utilizador e dados pertencem a uma igreja.

- id, nome, slug, plano, configuracoes (json), created_at, updated_at

### usuarios
- id, igreja_id, nome, email, password, papel (admin_geral, admin_igreja, apresentador, editor, participante, convidado), created_at, updated_at

### livros_biblicos
- id, abreviacao, nome, testamento, codigo_osis

### capitulos
- id, livro_id, numero

### categorias
- id, nome, slug, descricao

### niveis_dificuldade
- id, nome, nivel (1-5)

### questoes
- id, livro_id, capitulo_id, versiculo_de, versiculo_ate, texto, explicacao, contexto, dificuldade_id, categoria_id, referencia_verificada, aprovada, submetida_por, revisada_por, revisada_em, notas, created_at, updated_at, deleted_at

### questoes_historico
- id, questao_id, campo_alterado, valor_antigo, valor_novo, alterado_por, alterado_em

### questoes_submetidas
- id, dados_questao (json), submetida_por, status (pendente, aprovada, rejeitada), revisada_por, revisada_em, notas

### salas
- id, igreja_id, codigo_acesso, nome, apresentador_id, status (aguardando, em_curso, pausada, terminada), created_at, updated_at

### equipas
- id, sala_id, nome, cor, created_at

### participantes
- id, sala_id, equipa_id, nome, conectado, created_at

### jogos
- id, sala_id, estado (aguardando, em_curso, pausado, terminado), pergunta_atual_id, pontuacao_total, created_at, updated_at

### respostas
- id, jogo_id, questao_id, participante_id, resposta, correta, tempo_resposta, pontos_ganhos, created_at

### colecoes
- id, igreja_id, nome, descricao, tipo_evento, publico_alvo, created_at

### colecao_questoes
- colecao_id, questao_id, ordem

### planos_aprendizagem
- id, igreja_id, nome, descricao, publico_alvo, duracao_dias, created_at

### plano_aprendizagem_passos
- plano_id, dia, questoes (json)

## Relacionamentos

- Igreja 1:N Usuarios, Questoes, Salas, Colecoes, PlanosAprendizagem
- Usuario 1:N Questoes (submetidas/revisadas), Salas (apresentador)
- Livro 1:N Capitulos, Questoes
- Capitulo 1:N Questoes
- Questao 1:N QuestoesHistorico, QuestoesSubmetidas, Respostas
- Sala 1:N Equipes, Participantes, Jogos
- Jogo 1:N Respostas
