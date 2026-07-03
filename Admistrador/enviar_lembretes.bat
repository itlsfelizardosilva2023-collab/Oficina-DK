@echo off
:: ============================================================
:: Oficina D.K - Envio Automático de Lembretes de Manutenção
:: Coloca este ficheiro em: C:\xampp\htdocs\Oficina_D.K\Admistrador\
:: ============================================================

set PHP="C:\xampp\php\php.exe"
set SCRIPT="C:\xampp\htdocs\Oficina_D.K\Admistrador\enviar_lembretes_manutencao.php"
set LOGDIR="C:\xampp\htdocs\Oficina_D.K\Admistrador\logs"
set LOG="C:\xampp\htdocs\Oficina_D.K\Admistrador\logs\lembretes.log"

:: Criar pasta de logs se não existir
if not exist %LOGDIR% mkdir %LOGDIR%

:: Registar data e hora no log
echo. >> %LOG%
echo ============================================ >> %LOG%
echo Execucao: %date% %time% >> %LOG%
echo ============================================ >> %LOG%

:: Executar o script PHP e guardar resultado no log
%PHP% %SCRIPT% >> %LOG% 2>&1
