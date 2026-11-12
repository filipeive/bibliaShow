# Bíblia Show 2.0 — App Mobile Flutter (Offline-First)

Aplicativo oficial em **Flutter** para o jogo bíblico estilo Show do Milhão. 

---

## 🚀 Destaques da Arquitetura

1. **100% Offline-First (Modo Solo)**:
   - Contém as **1.002 perguntas bíblicas** estruturadas em `assets/data/questoes.json` e indexadas no banco local **SQLite** (`sqflite`).
   - Todos os 30 efeitos sonoros e locuções do Show do Milhão (`assets/audio/`) tocam nativamente com **latência zero**.
   - O ranking e histórico de melhores partidas ficam salvos no próprio aparelho.

2. **Prevenção de Saída Acidental (Botão Voltar do Android)**:
   - Utiliza `PopScope` para interceptar o gesto e botão físico de voltar do Android, abrindo o modal de confirmação antes de pausar ou sair da partida.

3. **Modo Arena Ao Vivo (Igreja / Salão)**:
   - Digitando o código de 6 caracteres do telão, o app se conecta via HTTP ao backend Laravel existente (`/api/salas/{id}`), permitindo disputas sincronizadas com feedback sonoro e háptico (vibração) no smartphone de cada participante.

---

## 🛠️ Como Executar o App

### Pré-requisitos:
- Flutter SDK (>= 3.0.0) instalado
- Android Studio ou VS Code com extensão Flutter

### Comandos:
```bash
# Entrar na pasta do app
cd mobile

# Obter dependências
flutter pub get

# Executar no emulador ou celular conectado
flutter run

# Gerar APK de produção para Android
flutter build apk --release
```

O APK gerado ficará em:
`mobile/build/app/outputs/flutter-apk/app-release.apk`
