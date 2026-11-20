#!/usr/bin/env python3
"""
Deploy script — Bíblia Show → HostGator
Envia apenas ficheiros alterados via SFTP (ou FTP).

Uso:
    python3 deploy.py                  # Apenas ficheiros alterados
    python3 deploy.py --views          # Só views Blade
    python3 deploy.py --assets         # Só assets compilados (CSS/JS/audio/images)
    python3 deploy.py --routes         # Só routes
    python3 deploy.py --controllers    # Só controllers/middleware/requests
    python3 deploy.py --models         # Só models + services + game + events
    python3 deploy.py --jobs           # Só jobs
    python3 deploy.py --database       # Só migrations + seeders + data
    python3 deploy.py --config         # Só config/ + bootstrap/
    python3 deploy.py --vendor         # Enviar vendor/ (quando não houver Composer no servidor)
    python3 deploy.py --full           # Tudo (forçar, sem vendor)
    python3 deploy.py --file path      # Ficheiro específico
    python3 deploy.py --status         # Ver o que mudou sem enviar
    python3 deploy.py --clear-cache    # Limpar cache local de hashes
"""

import ftplib
import os
import sys
import hashlib
import json
import time
import subprocess
import tempfile
from pathlib import Path
from datetime import datetime

# ─── CONFIGURAÇÃO ────────────────────────────────────────────
DEPLOY_MODE = os.environ.get("DEPLOY_MODE", "sftp").lower()  # "sftp" ou "ftp"

# SFTP (HostGator Hospedagem Compartilhada)
SFTP_HOST   = os.environ.get("SFTP_HOST",   "192.185.210.165")
SFTP_PORT   = int(os.environ.get("SFTP_PORT", 2222))
SFTP_USER   = os.environ.get("SFTP_USER",   "fili7297")
SFTP_KEY    = os.environ.get("SFTP_KEY",    "/home/fdev-ms/.ssh/id_rsa")

# FTP (fallback se necessário)
FTP_HOST    = os.environ.get("FTP_HOST",    "192.185.210.165")
FTP_PORT    = int(os.environ.get("FTP_PORT", 21))
FTP_USER    = os.environ.get("FTP_USER",    "")
FTP_PASS    = os.environ.get("FTP_PASS",    "")

REMOTE_BASE = os.environ.get("REMOTE_BASE", "/home1/fili7297/lifechurchmz.com/bibliashow")

LOCAL_BASE  = os.path.dirname(os.path.abspath(__file__))
CACHE_FILE  = os.path.join(LOCAL_BASE, ".deploy-cache.json")
LOG_FILE    = os.path.join(LOCAL_BASE, ".deploy-log.txt")

# ─── GRUPOS DE DEPLOY ────────────────────────────────────────
DEPLOY_GROUPS = {
    "views": [
        "resources/views/",
    ],
    "assets": [
        "public/build/",
        "public/images/",
        "public/audio/",
        "public/css/",
        "public/data/",
        "public/downloads/",
        "public/favicon.ico",
        "public/sw.js",
        "public/manifest.json",
        "public/robots.txt",
        "public/index.php",
        "public/.htaccess",
    ],
    "routes": [
        "routes/web.php",
        "routes/api.php",
        "routes/channels.php",
        "routes/console.php",
    ],
    "controllers": [
        "app/Http/Controllers/",
        "app/Http/Middleware/",
        "app/Http/Requests/",
    ],
    "jobs": [
        "app/Jobs/",
    ],
    "models": [
        "app/Models/",
        "app/Services/",
        "app/Game/",
        "app/Events/",
        "app/Providers/",
    ],
    "config": [
        "config/",
        "bootstrap/app.php",
        "bootstrap/providers.php",
        "artisan",
        "composer.json",
        "composer.lock",
    ],
    "database": [
        "database/migrations/",
        "database/seeders/",
        "database/factories/",
        "database/data/",
    ],
    "console": [
        "app/Console/",
    ],
}

OPTIONAL_GROUPS = {
    "vendor": [
        "vendor/",
    ],
}

# ─── PADRÕES A IGNORAR SEMPRE ────────────────────────────────
SKIP_PATTERNS = [
    ".git",
    "node_modules",
    ".env",
    ".env.example",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/app/public",
    ".deploy-cache.json",
    ".deploy-log.txt",
    ".deploy.env",
    ".ftp-credentials",
    "deploy.py",
    "deploy.sh",
    "deploy.exemple.sh",
    "__pycache__",
    ".DS_Store",
    "Thumbs.db",
    "tests/",
    "phpunit.xml",
    ".phpunit",
    "*.swp",
    "*.swo",
]

# Extensões de ficheiros a ignorar
SKIP_EXTENSIONS = {".pyc", ".pyo", ".log", ".cache"}

# ─── CORES NO TERMINAL ───────────────────────────────────────
class C:
    RESET  = "\033[0m"
    BOLD   = "\033[1m"
    GREEN  = "\033[92m"
    YELLOW = "\033[93m"
    RED    = "\033[91m"
    BLUE   = "\033[94m"
    GOLD   = "\033[33m"
    DIM    = "\033[2m"

def ok(msg):    print(f"  {C.GREEN}✓{C.RESET}  {msg}")
def err(msg):   print(f"  {C.RED}✗{C.RESET}  {msg}")
def info(msg):  print(f"  {C.BLUE}→{C.RESET}  {msg}")
def warn(msg):  print(f"  {C.YELLOW}⚠{C.RESET}  {msg}")
def title(msg): print(f"\n{C.BOLD}{C.GOLD}{msg}{C.RESET}")

# ─── HELPERS ─────────────────────────────────────────────────
def should_skip(rel_path: str) -> bool:
    rel_path = rel_path.replace("\\", "/")
    _, ext = os.path.splitext(rel_path)
    if ext.lower() in SKIP_EXTENSIONS:
        return True
    for pattern in SKIP_PATTERNS:
        if pattern in rel_path:
            return True
    return False

def file_hash(filepath: str) -> str:
    h = hashlib.md5()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()

def load_cache() -> dict:
    if os.path.exists(CACHE_FILE):
        try:
            with open(CACHE_FILE, "r") as f:
                return json.load(f)
        except (json.JSONDecodeError, IOError):
            return {}
    return {}

def save_cache(cache: dict):
    with open(CACHE_FILE, "w") as f:
        json.dump(cache, f, indent=2, sort_keys=True)

def log_deploy(files: list, success: int, errors: int):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as f:
        f.write(f"\n[{timestamp}] Deploy: {success} ok, {errors} erros\n")
        for file in files:
            f.write(f"  {file}\n")

def format_size(size: int) -> str:
    if size < 1024:      return f"{size}B"
    if size < 1048576:   return f"{size/1024:.1f}KB"
    return f"{size/1048576:.1f}MB"

# ─── RECOLHA DE FICHEIROS ────────────────────────────────────
def get_files_for_group(group_name: str) -> list:
    files = []
    all_groups = {**DEPLOY_GROUPS, **OPTIONAL_GROUPS}
    if group_name not in all_groups:
        err(f"Grupo desconhecido: {group_name}")
        print(f"     Grupos: {', '.join(all_groups.keys())}")
        return files

    for path in all_groups[group_name]:
        full_path = os.path.join(LOCAL_BASE, path)

        if os.path.isfile(full_path):
            rel = os.path.relpath(full_path, LOCAL_BASE)
            if not should_skip(rel):
                files.append(rel)

        elif os.path.isdir(full_path):
            for root, dirs, filenames in os.walk(full_path):
                # Ignorar pastas na travessia
                dirs[:] = [
                    d for d in dirs
                    if not should_skip(os.path.relpath(
                        os.path.join(root, d), LOCAL_BASE
                    ))
                ]
                for filename in filenames:
                    abs_path = os.path.join(root, filename)
                    rel      = os.path.relpath(abs_path, LOCAL_BASE)
                    if not should_skip(rel):
                        files.append(rel)
        else:
            # Ficheiro não existe localmente — ignorar silenciosamente
            pass

    return list(set(files))

def get_all_deployable_files() -> list:
    files = []
    for group in DEPLOY_GROUPS:
        files.extend(get_files_for_group(group))
    return list(set(files))

def get_changed_files(file_list: list, cache: dict) -> list:
    changed = []
    for rel_path in file_list:
        abs_path = os.path.join(LOCAL_BASE, rel_path)
        if not os.path.exists(abs_path):
            continue
        current_hash = file_hash(abs_path)
        if cache.get(rel_path) != current_hash:
            changed.append(rel_path)
    return changed

# ─── SFTP (OpenSSH + ControlMaster) ─────────────────────────
class SFTPClient:
    def __init__(self, host: str, port: int, user: str, key_path: str):
        self.host = host
        self.port = port
        self.user = user
        self.key_path = key_path
        self.control_path = os.path.join(
            tempfile.gettempdir(), f"sftp-deploy-{os.getpid()}"
        )
        self.created_dirs = set()

    def _base_opts(self) -> list:
        return [
            "-o", "BatchMode=yes",
            "-o", "ConnectTimeout=15",
            "-o", "StrictHostKeyChecking=accept-new",
            "-o", f"ControlPath={self.control_path}",
            "-P", str(self.port),
            "-i", self.key_path,
        ]

    def connect(self):
        cmd = [
            "sftp",
            "-o", "ControlMaster=auto",
            "-o", "ControlPersist=120",
            *self._base_opts(),
            "-b", "-",
            f"{self.user}@{self.host}",
        ]
        res = subprocess.run(
            cmd, input="pwd\n", text=True, capture_output=True
        )
        if res.returncode != 0:
            raise RuntimeError(res.stderr.strip() or res.stdout.strip() or "Falha ao conectar SFTP")

    def ensure_remote_dir(self, remote_dir: str):
        remote_dir = remote_dir.rstrip("/")
        if not remote_dir or remote_dir in self.created_dirs:
            return
        parts = [p for p in remote_dir.strip("/").split("/") if p]
        cmds = []
        current = ""
        for part in parts:
            current += f"/{part}"
            if current not in self.created_dirs:
                cmds.append(f'-mkdir "{current}"')
                self.created_dirs.add(current)
        if cmds:
            self._run_batch("\n".join(cmds) + "\n")

    def _run_batch(self, batch: str):
        cmd = ["sftp", *self._base_opts(), "-b", "-", f"{self.user}@{self.host}"]
        res = subprocess.run(cmd, input=batch, text=True, capture_output=True)
        if res.returncode != 0:
            raise RuntimeError(res.stderr.strip() or res.stdout.strip() or "Erro SFTP")

    def upload_file(self, local_path: str, remote_path: str):
        remote_dir = os.path.dirname(remote_path).replace("\\", "/")
        self.ensure_remote_dir(remote_dir)
        self._run_batch(f'put "{local_path}" "{remote_path}"\n')

    def quit(self):
        try:
            subprocess.run(
                [
                    "ssh",
                    "-O", "exit",
                    "-o", f"ControlPath={self.control_path}",
                    "-p", str(self.port),
                    f"{self.user}@{self.host}",
                ],
                capture_output=True,
                timeout=5,
            )
        except subprocess.TimeoutExpired:
            pass


# ─── FTP ─────────────────────────────────────────────────────
def connect_ftp() -> ftplib.FTP:
    ftp = ftplib.FTP()
    ftp.connect(FTP_HOST, FTP_PORT, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.encoding = "utf-8"
    try:
        ftp.set_pasv(True)  # Modo passivo — mais compatível com firewalls
    except Exception:
        pass
    return ftp

def ensure_remote_dir(ftp: ftplib.FTP, remote_dir: str):
    """Criar directório remoto recursivamente se não existir."""
    parts = [p for p in remote_dir.strip("/").split("/") if p]
    current = ""
    for part in parts:
        current += f"/{part}"
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            try:
                ftp.mkd(current)
                ftp.cwd(current)
            except ftplib.error_perm:
                pass  # Já existe — ok

def upload_file(ftp: ftplib.FTP, local_path: str, remote_path: str):
    """Fazer upload de um ficheiro."""
    remote_dir  = os.path.dirname(remote_path).replace("\\", "/")
    ensure_remote_dir(ftp, remote_dir)

    with open(local_path, "rb") as f:
        ftp.storbinary(f"STOR {remote_path}", f)

# ─── DEPLOY PRINCIPAL ────────────────────────────────────────
def deploy(file_list: list, force: bool = False, dry_run: bool = False, auto_yes: bool = False):
    cache = load_cache()

    if force:
        to_upload = [
            f for f in file_list
            if os.path.exists(os.path.join(LOCAL_BASE, f))
        ]
    else:
        to_upload = get_changed_files(file_list, cache)

    if not to_upload:
        ok("Nenhum ficheiro alterado. Tudo actualizado!")
        return

    # Ordenar: assets e routes primeiro, views depois
    priority = ["routes/", "app/", "config/", "database/"]
    def sort_key(f):
        for i, p in enumerate(priority):
            if f.startswith(p): return (i, f)
        return (len(priority), f)
    to_upload.sort(key=sort_key)

    # Mostrar lista
    title(f"{'[DRY RUN] ' if dry_run else ''}Ficheiros a enviar ({len(to_upload)})")
    total_size = 0
    for f in to_upload:
        abs_f = os.path.join(LOCAL_BASE, f)
        size  = os.path.getsize(abs_f)
        total_size += size
        print(f"  {C.DIM}{f}{C.RESET}  {C.YELLOW}{format_size(size)}{C.RESET}")
    print(f"\n  Total: {C.BOLD}{format_size(total_size)}{C.RESET}\n")

    if dry_run:
        warn("Dry run — nenhum ficheiro enviado.")
        return

    # Confirmação para deploys grandes
    if len(to_upload) > 20 and not auto_yes:
        resp = input(f"  {C.YELLOW}Enviar {len(to_upload)} ficheiros? (s/n): {C.RESET}")
        if resp.strip().lower() not in ("s", "sim", "y", "yes"):
            warn("Deploy cancelado.")
            return

    # Conectar
    title(f"A conectar ao servidor {DEPLOY_MODE.upper()}...")
    client = None
    try:
        if DEPLOY_MODE == "sftp":
            client = SFTPClient(SFTP_HOST, SFTP_PORT, SFTP_USER, SFTP_KEY)
            client.connect()
            ok(f"Conectado via SFTP a {SFTP_USER}@{SFTP_HOST}:{SFTP_PORT}")
        else:
            client = connect_ftp()
            ok(f"Conectado via FTP a {FTP_HOST}:{FTP_PORT}")
    except Exception as e:
        err(f"Não foi possível conectar: {e}")
        sys.exit(1)
    print()

    success_list = []
    error_list   = []
    start_time   = time.time()

    for rel_path in to_upload:
        abs_path    = os.path.join(LOCAL_BASE, rel_path)
        remote_path = f"{REMOTE_BASE}/{rel_path}".replace("\\", "/")
        remote_path = remote_path.replace("//", "/")

        try:
            if DEPLOY_MODE == "sftp":
                client.upload_file(abs_path, remote_path)
            else:
                upload_file(client, abs_path, remote_path)
            size = os.path.getsize(abs_path)
            cache[rel_path] = file_hash(abs_path)
            ok(f"{rel_path}  {C.DIM}{format_size(size)}{C.RESET}")
            success_list.append(rel_path)
        except Exception as e:
            err(f"{rel_path}  →  {e}")
            error_list.append(rel_path)

    save_cache(cache)
    log_deploy(to_upload, len(success_list), len(error_list))

    try:
        client.quit()
    except Exception:
        pass

    elapsed = time.time() - start_time

    # Resumo
    print(f"\n{'─'*50}")
    ok(f"{len(success_list)} ficheiro(s) enviado(s)")
    if error_list:
        err(f"{len(error_list)} erro(s):")
        for f in error_list: print(f"     {f}")
    print(f"  {C.DIM}Tempo: {elapsed:.1f}s{C.RESET}")
    print(f"{'─'*50}\n")

    # Lembrete pós-deploy
    if success_list:
        print(f"{C.YELLOW}Lembrete:{C.RESET}")
        print(f"  Pasta remota: {C.DIM}{REMOTE_BASE}{C.RESET}")
        print(f"  Se alteraste CSS/JS, garante que corriste {C.BOLD}npm run build{C.RESET} antes.")
        print(f"  No primeiro deploy, configura o {C.BOLD}.env{C.RESET} e o {C.BOLD}vendor/{C.RESET} (ou --vendor) no servidor.\n")

# ─── STATUS — ver o que mudou sem enviar ─────────────────────
def show_status():
    cache = load_cache()
    all_files = get_all_deployable_files()
    changed   = get_changed_files(all_files, cache)
    new_files = [
        f for f in changed
        if f not in cache
    ]
    modified = [f for f in changed if f in cache]

    title("Estado actual do projecto")

    if not changed:
        ok("Nenhuma alteração pendente.")
        return

    if new_files:
        print(f"\n  {C.GREEN}Novos ({len(new_files)}):{C.RESET}")
        for f in sorted(new_files):
            print(f"    + {f}")

    if modified:
        print(f"\n  {C.YELLOW}Modificados ({len(modified)}):{C.RESET}")
        for f in sorted(modified):
            print(f"    ~ {f}")

    print(f"\n  Total: {len(changed)} ficheiro(s) por enviar\n")
    print(f"  Corre {C.BOLD}python3 deploy.py{C.RESET} para enviar.")

# ─── MAIN ─────────────────────────────────────────────────────
def main():
    raw_args = sys.argv[1:]
    auto_yes = "-y" in raw_args or "--yes" in raw_args
    args = [a for a in raw_args if a not in ("-y", "--yes")]

    # Verificar password apenas se usar modo FTP
    global FTP_PASS
    if DEPLOY_MODE == "ftp" and not FTP_PASS:
        creds_file = os.path.join(LOCAL_BASE, ".ftp-credentials")
        if os.path.exists(creds_file):
            with open(creds_file) as f:
                for line in f:
                    if line.startswith("FTP_PASS="):
                        FTP_PASS = line.split("=", 1)[1].strip()
                        break
        if not FTP_PASS:
            import getpass
            FTP_PASS = getpass.getpass("Password FTP: ")

    # ── Comandos ──────────────────────────────────────────────
    if not args:
        title("Deploy: ficheiros alterados")
        deploy(get_all_deployable_files(), auto_yes=auto_yes)

    elif "--status" in args:
        show_status()

    elif "--clear-cache" in args:
        if os.path.exists(CACHE_FILE):
            os.remove(CACHE_FILE)
            ok("Cache de deploy limpo. Próximo deploy enviará tudo.")
        else:
            info("Cache já estava vazio.")

    elif "--full" in args:
        title("Deploy COMPLETO (todos os ficheiros)")
        dry = "--dry" in args
        deploy(get_all_deployable_files(), force=True, dry_run=dry, auto_yes=auto_yes)

    elif "--dry" in args:
        title("Dry run — ver o que seria enviado")
        deploy(get_all_deployable_files(), dry_run=True)

    elif "--file" in args:
        filepaths = []
        i = 0
        while i < len(args):
            if args[i] == "--file" and i + 1 < len(args):
                filepaths.append(args[i + 1])
                i += 2
            else:
                i += 1
        if not filepaths:
            err("Especifique o(s) ficheiro(s): --file path1 --file path2")
            sys.exit(1)
        for filepath in filepaths:
            if os.path.isabs(filepath):
                filepath = os.path.relpath(filepath, LOCAL_BASE)
            filepath = filepath.replace("\\", "/")
        title(f"Deploy: {len(filepaths)} ficheiro(s)")
        deploy(filepaths, force=True)

    else:
        # Grupos específicos
        deployed_any = False
        for arg in args:
            group = arg.lstrip("-")
            if group in DEPLOY_GROUPS or group in OPTIONAL_GROUPS:
                title(f"Deploy: {group}")
                deploy(get_files_for_group(group), force=True, auto_yes=auto_yes)
                deployed_any = True
            elif group not in ("dry", "force"):
                err(f"Argumento desconhecido: {arg}")
                print(f"\n  Uso: python3 deploy.py [opção]")
                all_groups = list(DEPLOY_GROUPS) + list(OPTIONAL_GROUPS)
                print(f"  Grupos: {', '.join(f'--{g}' for g in all_groups)}")
                print(f"  Outros: --full, --status, --dry, --clear-cache, --file path, --yes\n")

        if not deployed_any:
            title("Deploy: ficheiros alterados")
            deploy(get_all_deployable_files(), auto_yes=auto_yes)

if __name__ == "__main__":
    main()
