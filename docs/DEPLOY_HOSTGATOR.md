# Guia de Deploy e SFTP — Bíblia Show (HostGator)

Este documento reúne a configuração de acesso SFTP na HostGator e todos os comandos úteis para deploy e manutenção do projeto usando o `deploy.py`, `sftp` e `scp`.

---

## 1. Dados de Conexão SFTP

> **Nota:** O domínio temporário `*.meusitehostgator.com.br` passa pelo Cloudflare (que bloqueia as portas `21`, `22` e `2222`). Por isso, a conexão SFTP/FTP deve apontar sempre para o **IP real do servidor**.

| Parâmetro | Valor |
|---|---|
| **Protocolo** | SFTP (`SSH File Transfer Protocol`) |
| **Host / IP** | `192.185.210.165` |
| **Porta** | `2222` |
| **Usuário** | `fili7297` |
| **Chave privada SSH** | `/home/fdev-ms/.ssh/id_rsa` (permissão `600`) |
| **Pasta remota do projeto** | `/home1/fili7297/lifechurchmz.com/bibliashow` |
| **URL pública** | `https://lifechurchmz.com/bibliashow` |

---

## 2. Aplicativos Recomendados

### FileZilla (Interface Gráfica)
Instalação no Ubuntu/Debian:
```bash
sudo apt install filezilla
```

Configuração em **Arquivo → Gerenciador de Sites → Novo Site**:
- **Protocolo:** `SFTP - SSH File Transfer Protocol`
- **Host:** `192.185.210.165`
- **Porta:** `2222`
- **Tipo de logon:** `Arquivo de chave` (*Key file*)
- **Usuário:** `fili7297`
- **Arquivo de chave:** `/home/fdev-ms/.ssh/id_rsa`
- **Diretório remoto padrão** *(aba Avançado)*: `/home1/fili7297/lifechurchmz.com/bibliashow`

### Gerenciador de Arquivos (Nautilus / Ubuntu)
Pressione `Ctrl + L` na barra de endereços e cole:
```text
sftp://fili7297@192.185.210.165:2222/home1/fili7297/lifechurchmz.com/bibliashow
```

### Atalho opcional no `~/.ssh/config`
Adicione em `/home/fdev-ms/.ssh/config`:
```sshconfig
Host hostgator
    HostName 192.185.210.165
    Port 2222
    User fili7297
    IdentityFile /home/fdev-ms/.ssh/id_rsa
```
Depois basta usar:
```bash
sftp hostgator
```

---

## 3. Comandos do `deploy.py`

### Diagnóstico e cache
```bash
python3 deploy.py --status        # Ver ficheiros novos ou modificados (sem enviar)
python3 deploy.py --dry           # Simular deploy incremental com tamanhos
python3 deploy.py --full --dry    # Simular deploy completo
python3 deploy.py --clear-cache   # Limpar .deploy-cache.json (próximo deploy envia tudo)
```

### Envio (Deploy)
```bash
python3 deploy.py                              # Enviar apenas ficheiros alterados
python3 deploy.py --full                       # Enviar todos os ficheiros (exceto vendor/ e .env)
python3 deploy.py --vendor                     # Enviar a pasta vendor/
python3 deploy.py --file caminho/arquivo       # Enviar um ficheiro específico
python3 deploy.py --file a.php --file b.php    # Enviar vários ficheiros específicos
```

### Envio por grupos específicos
| Comando | O que envia |
|---|---|
| `python3 deploy.py --views` | `resources/views/` |
| `python3 deploy.py --assets` | `public/build/`, `public/images/`, `public/audio/`, `public/css/`, `public/data/`, `public/downloads/`, `index.php`, `.htaccess`, etc. |
| `python3 deploy.py --routes` | `routes/web.php`, `api.php`, `channels.php`, `console.php` |
| `python3 deploy.py --controllers` | `app/Http/Controllers/`, `Middleware/`, `Requests/` |
| `python3 deploy.py --models` | `app/Models/`, `Services/`, `Game/`, `Events/`, `Providers/` |
| `python3 deploy.py --jobs` | `app/Jobs/` |
| `python3 deploy.py --database` | `database/migrations/`, `seeders/`, `factories/`, `database/data/` |
| `python3 deploy.py --config` | `config/`, `bootstrap/app.php`, `bootstrap/providers.php`, `artisan`, `composer.json`, `composer.lock` |
| `python3 deploy.py --console` | `app/Console/` |

### Sobrescrever configurações via variáveis de ambiente
```bash
# Enviar para outra pasta/projeto sem editar o deploy.py
REMOTE_BASE=/home1/fili7297/public_html/outro-projeto python3 deploy.py

# Usar modo FTP caso necessário
DEPLOY_MODE=ftp FTP_USER=usuario FTP_PASS=senha python3 deploy.py --status
```

---

## 4. Comandos Interativos do `sftp`

Conectar:
```bash
sftp -P 2222 -i /home/fdev-ms/.ssh/id_rsa fili7297@192.185.210.165
```

### Navegação
| Comando | Ação |
|---|---|
| `pwd` / `lpwd` | Mostra pasta atual no **servidor** / no **PC local** |
| `cd pasta` / `lcd pasta` | Entra na pasta no **servidor** / no **PC local** |
| `ls -la` / `lls -la` | Lista ficheiros no **servidor** / no **PC local** |

### Transferência e gerenciamento
| Comando | Ação |
|---|---|
| `put arquivo` | Envia um ficheiro para o servidor |
| `put -r pasta/` | Envia uma pasta recursivamente para o servidor |
| `get arquivo` | Baixa um ficheiro do servidor |
| `get -r pasta/` | Baixa uma pasta recursivamente do servidor |
| `mkdir nome` | Cria uma pasta no servidor |
| `rmdir nome` | Remove uma pasta vazia no servidor |
| `rm arquivo` | Apaga um ficheiro no servidor |
| `rename antigo novo` | Renomeia ou move ficheiro/pasta no servidor |
| `chmod 775 storage` | Altera permissões no servidor |
| `df -h` | Mostra uso de disco no servidor |
| `exit` | Encerra a sessão |

---

## 5. Comandos Rápidos com `scp`

```bash
# Enviar um ficheiro direto para o servidor
scp -P 2222 -i /home/fdev-ms/.ssh/id_rsa .env fili7297@192.185.210.165:/home1/fili7297/public_html/bibliashow/.env

# Enviar uma pasta inteira
scp -P 2222 -i /home/fdev-ms/.ssh/id_rsa -r public/build fili7297@192.185.210.165:/home1/fili7297/public_html/bibliashow/public/

# Baixar o log de erros do Laravel do servidor
scp -P 2222 -i /home/fdev-ms/.ssh/id_rsa fili7297@192.185.210.165:/home1/fili7297/public_html/bibliashow/storage/logs/laravel.log ./
```

---

## 6. Fluxo Recomendado de Deploy

1. Compilar os assets do frontend:
   ```bash
   npm run build
   ```
2. Conferir o que mudou:
   ```bash
   python3 deploy.py --status
   ```
3. Enviar as alterações:
   ```bash
   python3 deploy.py
   ```
4. No primeiro deploy, enviar também as dependências PHP (caso não use Composer no cPanel) e o `.env` de produção:
   ```bash
   python3 deploy.py --vendor
   python3 deploy.py --file .env
   ```
