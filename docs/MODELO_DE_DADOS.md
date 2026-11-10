# Modelo de Dados — Bíblia Show 2.1

## Tabelas Principais

### `igrejas`
Multi-tenant lógico para congregações e células.
- `id`, `nome`, `slug`, `plano`, `configuracoes` (JSON), `created_at`, `updated_at`

### `usuarios`
- `id`, `igreja_id`, `nome`, `email`, `password`, `papel` (`admin_geral`, `admin_igreja`, `apresentador`, `editor`, `participante`, `convidado`), `remember_token`, `created_at`, `updated_at`, `deleted_at`

### `livros_biblicos`
Todos os 66 livros da Bíblia Sagrada (39 do Antigo Testamento e 27 do Novo Testamento).
- `id`, `abreviacao`, `nome`, `testamento` (`AT` / `NT`), `codigo_osis`, `ordem`

### `capitulos`
- `id`, `livro_id`, `numero`

### `niveis_dificuldade`
Representa as 4 Fases do clássico Bíblia Show (1ª Fase: 10 pts, 2ª Fase: 20 pts, 3ª Fase: 30 pts, 4ª Fase: 50 pts).
- `id`, `nome`, `nivel`, `pontos_base`, `tempo_segundos`

### `questoes`
Armazena as 1.000+ perguntas bíblicas, incluindo metadados originais do clássico (`cadastro.dbf`).
- `id`, `igreja_id`, `codigo_classico`, `livro_id`, `capitulo_id`, `versiculo_de`, `versiculo_ate`, `referencia_biblica`, `texto`, `opcoes` (JSON array com 4 alternativas), `resposta_correta`, `ajudas` (JSON com `convidados`, `errado`, `comentario_ajuda`), `explicacao`, `contexto`, `dificuldade_id`, `categoria_id`, `referencia_verificada`, `aprovada`

### `salas`, `equipes`, `participantes`, `jogos` e `respostas`
- `salas`: `id`, `igreja_id`, `codigo_acesso` (6 caracteres), `nome`, `apresentador_id`, `status`
- `equipes`: `id`, `sala_id`, `nome`, `cor`, `pontuacao_total`
- `participantes`: `id`, `sala_id`, `equipe_id`, `nome`, `codigo_acesso`, `conectado`, `pontuacao_total`
- `jogos`: `id`, `sala_id`, `estado`, `pergunta_atual_id`, `pontuacao_total`, `dados_motor` (estado serializado do `GameEngine`)
- `respostas`: `id`, `jogo_id`, `questao_id`, `participante_id`, `equipe_id`, `resposta`, `correta`, `tempo_resposta`, `pontos_ganhos`
