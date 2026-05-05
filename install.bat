@echo off
git clone https://github.com/durannahir313-design/tronpick-bot.git "%USERPROFILE%\tronpick"
echo @php "%%USERPROFILE%%\tronpick\tronpick_bot.php" %%* > "%USERPROFILE%\tronpick\tronpick.bat"
setx PATH "%%PATH%%;%USERPROFILE%\tronpick" >nul
echo ✓ Instalado. Abre nueva CMD y escribe: tronpick
