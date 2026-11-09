# API — Bíblia Show 2.0

Plano de documentação da API (P0: básica documentada; P1/P2: expansão).

## Autenticação

- Sanctum para sessões web e tokens de API.
- Modo convidado: acesso por código de sala sem autenticação.

## Endpoints planeados (P0)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | /api/jogar | Iniciar modo individual offline |
| POST | /api/responder | Submeter resposta a uma pergunta |
| GET | /api/perguntas/{id} | Obter uma pergunta (apenas aprovadas) |
| GET | /api/salas/{codigo} | Obter dados de uma sala por código |
| POST | /api/salas | Criar uma sala (apresentador) |
| GET | /api/projecao/{sala} | Dados para o ecrã de projeção |

## Endpoints planeados (P1)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | /api/salas/{sala}/equipes | Criar equipa |
| POST | /api/salas/{sala}/participantes | Entrar na sala |
| GET | /api/salas/{sala}/ranking | Ranking dinâmico |
| POST | /api/perguntas/importar | Importar Excel/CSV |
| GET | /api/perguntas/exportar | Exportar perguntas |

## Formato de resposta

JSON padronizado:

```json
{
  "sucesso": true,
  "dados": { ... },
  "mensagem": null,
  "erros": null
}
```

## WebSockets (Reverb)

Canais privados por sala: `private-game.{salaId}`.

Eventos:
- `pergunta.iniciada`
- `pergunta.respondida`
- `ranking.atualizado`
- `jogo.pausado` / `jogo.retomado`
- `jogo.terminado`
