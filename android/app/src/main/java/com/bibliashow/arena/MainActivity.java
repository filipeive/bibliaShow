package com.bibliashow.arena;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.webkit.CookieManager;
import android.webkit.DownloadListener;
import android.webkit.URLUtil;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.widget.Toast;
import androidx.activity.OnBackPressedCallback;
import com.getcapacitor.BridgeActivity;
import com.getcapacitor.BridgeWebViewClient;

public class MainActivity extends BridgeActivity {

    private BroadcastReceiver downloadCompleteReceiver;
    private long lastBackPressTime = 0;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Handle Hardware & Gesture Back Button: navigate back in WebView history first
        this.getOnBackPressedDispatcher().addCallback(this, new OnBackPressedCallback(true) {
            @Override
            public void handleOnBackPressed() {
                WebView webView = (getBridge() != null) ? getBridge().getWebView() : null;
                if (webView != null && webView.canGoBack()) {
                    String currentUrl = webView.getUrl();
                    if (currentUrl != null && (currentUrl.endsWith("/login") || currentUrl.contains("/login?"))) {
                        handleExitOnDoubleBack();
                    } else {
                        webView.goBack();
                    }
                } else {
                    handleExitOnDoubleBack();
                }
            }
        });
        
        if (this.getBridge() != null && this.getBridge().getWebView() != null) {
            WebView webView = this.getBridge().getWebView();

            // 1. Offline Error Handling
            webView.setWebViewClient(new BridgeWebViewClient(this.getBridge()) {
                @Override
                public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                    super.onReceivedError(view, request, error);
                    if (request != null && request.isForMainFrame()) {
                        view.loadUrl("file:///android_asset/public/offline.html");
                    }
                }
            });

            // 2. Native Download Handling
            webView.setDownloadListener(new DownloadListener() {
                @Override
                public void onDownloadStart(String url, String userAgent, String contentDisposition, String mimeType, long contentLength) {
                    try {
                        DownloadManager.Request request = new DownloadManager.Request(Uri.parse(url));
                        
                        String finalMimeType = mimeType;
                        if (finalMimeType == null || finalMimeType.isEmpty() || finalMimeType.equals("application/octet-stream")) {
                            if (url.contains(".pdf") || url.contains("export-pdf") || url.contains("pdf")) {
                                finalMimeType = "application/pdf";
                            } else if (url.contains(".csv") || url.contains("export")) {
                                finalMimeType = "text/csv";
                            }
                        }
                        
                        request.setMimeType(finalMimeType);
                        
                        String cookies = CookieManager.getInstance().getCookie(url);
                        if (cookies != null) {
                            request.addRequestHeader("cookie", cookies);
                        }
                        request.addRequestHeader("User-Agent", userAgent);
                        
                        String fileName = URLUtil.guessFileName(url, contentDisposition, finalMimeType);
                        if (fileName == null || fileName.isEmpty() || fileName.equals("downloadfile")) {
                            if (url.contains("export-pdf") || url.contains("pdf")) {
                                fileName = "relatorio_" + System.currentTimeMillis() + ".pdf";
                            } else {
                                fileName = "exportacao_" + System.currentTimeMillis() + ".csv";
                            }
                        }

                        request.setTitle(fileName);
                        request.setDescription("A baixar ficheiro do Life App...");
                        request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
                        request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName);
                        request.setAllowedOverMetered(true);
                        request.setAllowedOverRoaming(true);

                        DownloadManager dm = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
                        if (dm != null) {
                            dm.enqueue(request);
                            Toast.makeText(getApplicationContext(), "A baixar ficheiro: " + fileName, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        Toast.makeText(getApplicationContext(), "Erro ao iniciar download: " + e.getMessage(), Toast.LENGTH_LONG).show();
                    }
                }
            });
        }

        // 3. Register Receiver to Automatically Prompt User with Compatible Apps (PDF Viewer / Excel / etc.)
        downloadCompleteReceiver = new BroadcastReceiver() {
            @Override
            @SuppressWarnings("deprecation")
            public void onReceive(Context context, Intent intent) {
                long downloadId = intent.getLongExtra(DownloadManager.EXTRA_DOWNLOAD_ID, -1);
                if (downloadId != -1) {
                    DownloadManager dm = (DownloadManager) context.getSystemService(Context.DOWNLOAD_SERVICE);
                    if (dm != null) {
                        Uri fileUri = dm.getUriForDownloadedFile(downloadId);
                        String mimeType = dm.getMimeTypeForDownloadedFile(downloadId);
                        
                        if (fileUri != null) {
                            Intent openIntent = new Intent(Intent.ACTION_VIEW);
                            openIntent.setDataAndType(fileUri, mimeType != null ? mimeType : "*/*");
                            openIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_GRANT_READ_URI_PERMISSION);
                            try {
                                Intent chooser = Intent.createChooser(openIntent, "Abrir ficheiro com...");
                                chooser.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                                context.startActivity(chooser);
                            } catch (Exception e) {
                                Toast.makeText(context, "Ficheiro guardado em Transferências (Downloads).", Toast.LENGTH_LONG).show();
                            }
                        }
                    }
                }
            }
        };

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            registerReceiver(downloadCompleteReceiver, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE), Context.RECEIVER_EXPORTED);
        } else {
            registerReceiver(downloadCompleteReceiver, new IntentFilter(DownloadManager.ACTION_DOWNLOAD_COMPLETE));
        }
    }

    private void handleExitOnDoubleBack() {
        long now = System.currentTimeMillis();
        if (now - lastBackPressTime < 2000) {
            finish();
        } else {
            lastBackPressTime = now;
            Toast.makeText(getApplicationContext(), "Pressione voltar novamente para sair", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        if (downloadCompleteReceiver != null) {
            try {
                unregisterReceiver(downloadCompleteReceiver);
            } catch (Exception ignored) {}
        }
    }
}
