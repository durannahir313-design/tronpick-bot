#!/data/data/com.termux/files/usr/bin/bash
pkg install -y php git > /dev/null 2>&1
rm -rf $HOME/tronpick
git clone https://github.com/durannahir313-design/tronpick-bot.git $HOME/tronpick
echo '#!/data/data/com.termux/files/usr/bin/sh' > $PREFIX/bin/tronpick
echo 'php $HOME/tronpick/tronpick_bot.php "$@"' >> $PREFIX/bin/tronpick
chmod +x $PREFIX/bin/tronpick
echo "✓ Instalado. Ejecuta: tronpick"
