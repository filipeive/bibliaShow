const { app, BrowserWindow, Menu, globalShortcut } = require('electron');
const path = require('path');

let mainWindow;

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1440,
    height: 900,
    minWidth: 1024,
    minHeight: 700,
    title: 'Bíblia Show 2.0 — Arena de Conhecimento Bíblico',
    icon: path.join(__dirname, '../public/favicon.ico'),
    backgroundColor: '#020617',
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true
    },
    autoHideMenuBar: false
  });

  const defaultDevUrl = 'http://127.0.0.1:8000';
  const prodUrl = 'http://146.235.224.99/bibliashow';
  const appUrl = process.env.APP_URL || defaultDevUrl;

  mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription, validatedURL) => {
    if (validatedURL.includes('127.0.0.1') || validatedURL.includes('localhost')) {
      console.log(`[Electron] Servidor local indisponível em ${validatedURL}. Alternando para produção: ${prodUrl}`);
      mainWindow.loadURL(prodUrl);
    }
  });

  mainWindow.loadURL(appUrl);

  const menuTemplate = [
    {
      label: 'Navegação',
      submenu: [
        { label: 'Início / Painel', click: () => mainWindow.loadURL(`${appUrl}/`) },
        { label: 'Modo Individual', click: () => mainWindow.loadURL(`${appUrl}/game/play`) },
        { label: 'Salas Multiplayer', click: () => mainWindow.loadURL(`${appUrl}/salas`) },
        { type: 'separator' },
        { label: 'Recarregar', accelerator: 'CmdOrCtrl+R', click: () => mainWindow.reload() },
        { label: 'Sair', accelerator: 'CmdOrCtrl+Q', click: () => app.quit() }
      ]
    },
    {
      label: 'Telão & Exibição',
      submenu: [
        {
          label: 'Alternar Tela Cheia (Telão)',
          accelerator: 'F11',
          click: () => {
            mainWindow.setFullScreen(!mainWindow.isFullScreen());
          }
        },
        {
          label: 'Abrir Telão da Sala Mais Recente',
          accelerator: 'CmdOrCtrl+T',
          click: () => mainWindow.loadURL(`${appUrl}/telao/1`)
        }
      ]
    },
    {
      label: 'Ajuda',
      submenu: [
        {
          label: 'Alternar Ferramentas de Desenvolvedor',
          accelerator: 'F12',
          click: () => mainWindow.webContents.toggleDevTools()
        },
        {
          label: 'Sobre o Bíblia Show 2.0',
          click: () => {
            const { dialog } = require('electron');
            dialog.showMessageBox(mainWindow, {
              type: 'info',
              title: 'Bíblia Show 2.0',
              message: 'Bíblia Show 2.0 — Edição de Igreja e Telão',
              detail: 'Sistema interativo de quiz bíblico multi-tenant para igrejas, cultos e gincanas com suporte a telão HD e participação móvel.'
            });
          }
        }
      ]
    }
  ];

  const menu = Menu.buildFromTemplate(menuTemplate);
  Menu.setApplicationMenu(menu);

  mainWindow.on('closed', function () {
    mainWindow = null;
  });
}

app.whenReady().then(() => {
  createWindow();

  app.on('activate', function () {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit();
});
