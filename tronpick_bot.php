<?php
// === TRONPICK AUTO-UPDATE + LICENCIA ===
const FP_REPO = 'https://raw.githubusercontent.com/durannahir313-design/tronpick-bot/main';
const FP_DIR = '/data/data/com.termux/files/home/tronpick';
if (php_sapi_name() !== 'cli') { http_response_code(404); exit; }

// licencia auto-descarga
$__lic = __DIR__.'/license.dat';
if (!file_exists($__lic) || filesize($__lic) < 64 || (time() - @filemtime($__lic) > 3600)) {
    $r = @file_get_contents(FP_REPO.'/license.dat');
    if ($r && strlen($r) > 64) @file_put_contents($__lic, $r);
}

// update por version.txt
$__local = @trim(@file_get_contents(__DIR__.'/version.txt'));
$__remote = @trim(@file_get_contents(FP_REPO.'/version.txt'));
if ($__remote && $__remote !== $__local) {
    fwrite(STDERR, "[*] Actualizando TronPick a $__remote...\n");
    $dir = (stripos(PHP_OS,'WIN')===0) ? __DIR__ : FP_DIR;
    @chdir($dir);
    passthru('git fetch --quiet && git reset --hard origin/main 2>&1');
    $cmd = PHP_BINARY.' '.escapeshellarg(__FILE__);
    foreach (array_slice($argv ?? [],1) as $a) $cmd .= ' '.escapeshellarg($a);
    passthru($cmd);
    exit;
}
error_reporting(0);

error_reporting(0);
/**
* ==========================================================
*  TronPick BOT v3.0 — Individual
*  IconCaptcha + Xevil Fallback
*  Licencia SHA-256 + Hardware ID Binding
* ==========================================================
*  Host: tronpick.io | Crypto: TRX | Pair: TRXUSDT
* ==========================================================
*  v3.0: Sistema de licencia dual (SHA-256 + HWID).
*  v2.1: Corregido claim POST, multi-JSON parse, LitePick.
* ==========================================================
*/
// ======================== COLORES ========================
define("C_RESET",  "\033[0m");
define("C_RED",    "\033[1;31m");
define("C_GREEN",  "\033[1;32m");
define("C_YELLOW", "\033[1;33m");
define("C_BLUE",   "\033[1;34m");
define("C_PURPLE", "\033[1;35m");
define("C_CYAN",   "\033[1;36m");
define("C_WHITE",  "\033[1;37m");
define("C_DIM",    "\033[2;37m");
define("C_BOLD_R", "\033[1;31;40m");
define("C_BOLD_G", "\033[1;32;40m");
define("NL", "\n");
// ======================== SITIO ========================
$SITE = ['name'=>'TronPick', 'host'=>'tronpick.io', 'crypto'=>'TRX', 'pair'=>'TRXUSDT', 'color'=>C_RED, 'icon'=>'T'];
$API_TYPE = 0;
$API_HOST = '';
$API_KEY  = '';
$CURRENT_SITE = null;
$CLAIM_COUNT = 0;
// =================== COMPAT WINDOWS ===================
if(stripos(PHP_OS,'WIN')===0){shell_exec('reg add HKCU\Console /v VirtualTerminalLevel /t REG_DWORD /d 1 /f 2>nul');system('cmd /c color 0');}
$GLOBALS['_STDIN_HANDLE']=STDIN;
if(!function_exists('win_readline')){
    function win_readline($p=''){echo $p;flush();$l=fgets($GLOBALS['_STDIN_HANDLE']);return $l===false?'':trim($l);}
}
// =================== TERMINAL WIDTH ===================
function getTermWidth(){
    if(function_exists('shell_exec')){$tw=(int)@shell_exec('tput cols 2>/dev/null');if($tw>30)return $tw;}
    return 56;
}
function vis_len($t){
    $c=preg_replace('/\x1B\[[0-9;]*[A-Za-z]/','',$t);$w=0;$l=strlen($c);
    for($i=0;$i<$l;){$b=ord($c[$i]);
        if($b<0x80){$cp=$b;$i+=1;}elseif($b<0xE0){$cp=(($b&0x1F)<<6)|(ord($c[$i+1])&0x3F);$i+=2;}
        elseif($b<0xF0){$cp=(($b&0x0F)<<12)|((ord($c[$i+1])&0x3F)<<6)|(ord($c[$i+2])&0x3F);$i+=3;}
        else{$cp=(($b&0x07)<<18)|((ord($c[$i+1])&0x3F)<<12)|((ord($c[$i+2])&0x3F)<<6)|(ord($c[$i+3])&0x3F);$i+=4;}
        if(($cp>=0x1100&&$cp<=0x115F)||($cp>=0x2E80&&$cp<=0x303E)||($cp>=0x3040&&$cp<=0xA4CF)||($cp>=0xAC00&&$cp<=0xD7AF)||($cp>=0xF900&&$cp<=0xFAFF)||($cp>=0xFF00&&$cp<=0xFF60)||($cp>=0x1F300&&$cp<=0x1FAFF)||($cp>=0x20000&&$cp<=0x3FFFD))$w+=2;else $w+=1;}
    return $w;
}
function ui_pad($t,$w){$v=vis_len($t);$p=$w-$v;return $p<=0?$t:$t.str_repeat(" ",$p);}
function ui_center($t,$w){$v=vis_len($t);$p=$w-$v;$l=str_repeat(" ",max(0,(int)floor($p/2)));$r=str_repeat(" ",max(0,(int)ceil($p/2)));return $l.$t.$r;}
// =================== HTTP FUNCTIONS ===================
function Run($url,$head=0,$post=0,$proxy=0){
    $host=parse_url($url,PHP_URL_HOST);$folder="configs/{$host}-config";
    if(!is_dir($folder))mkdir($folder,0777,true);$cookieFile="$folder/cookie.txt";
    $ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieFile);curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieFile);
    if($proxy){curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);curl_setopt($ch,CURLOPT_PROXY,$proxy);}
    if($post){curl_setopt($ch,CURLOPT_POST,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$post);}
    if($head&&is_array($head)){curl_setopt($ch,CURLOPT_HTTPHEADER,$head);}
    curl_setopt($ch,CURLOPT_HEADER,true);$r=curl_exec($ch);
    if(!$r)return false;$hs=curl_getinfo($ch,CURLINFO_HEADER_SIZE);
    $body=substr($r,$hs);curl_close($ch);return $body;
}
function Run1($url,$head=0,$post=0,$proxy=0){
    $host=parse_url($url,PHP_URL_HOST);$folder="configs/{$host}-config";
    if(!is_dir($folder))mkdir($folder,0777,true);$cookieFile="$folder/cookie.txt";
    $ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieFile);curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieFile);
    if($proxy){curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);curl_setopt($ch,CURLOPT_PROXY,$proxy);}
    if($post){curl_setopt($ch,CURLOPT_POST,true);curl_setopt($ch,CURLOPT_POSTFIELDS,$post);}
    if($head&&is_array($head)){curl_setopt($ch,CURLOPT_HTTPHEADER,$head);}
    curl_setopt($ch,CURLOPT_HEADER,true);$r=curl_exec($ch);
    if(!$r)return false;$info=curl_getinfo($ch);$hs=curl_getinfo($ch,CURLINFO_HEADER_SIZE);
    $header=substr($r,0,$hs);$body=substr($r,$hs);curl_close($ch);
    return['header'=>$header,'body'=>$body,'info'=>$info];
}
// =================== CONFIG STORAGE ===================
define("DEFAULT_UA","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36");
// =================== LICENSE CONSTANTS ===================
define('LIC_SALT','LP2025#');
define('HWID_SALT','MPICK_DEVBIND_2025!');
define('LIC_FILE',__DIR__.'/license.dat');
define('LIC_CACHE_FILE',__DIR__.'/configs/.lic_cache');
function saveData($host,$key){
    $base=__DIR__."/configs/{$host}-config";if(!is_dir($base))mkdir($base,0777,true);$path="$base/$key";
    if(file_exists($path)){$v=file_get_contents($path);if(trim($v)!=='')return $v;}
    if($key==='user_Agent'){echo C_CYAN."  Input user_Agent [Enter = default]: ".C_RESET;$d=win_readline();if(trim($d)===''){$d=DEFAULT_UA;echo C_GREEN."  + Usando User-Agent por defecto.\n".C_RESET;}file_put_contents($path,$d);return $d;}
    echo C_CYAN."  Input $key: ".C_RESET;$d=win_readline();
    if(trim($d)===''){echo C_RED."  Campo vacio no permitido.\n".C_RESET;return saveData($host,$key);}
    file_put_contents($path,$d);return $d;
}
function unlinkData($host,$key){
    $f="configs/{$host}-config/$key";if(file_exists($f))unlink($f);
}
function removeData($host,$key=null){
    $f="configs/{$host}-config";
    if(!is_dir($f))return false;
    if($key===null){$files=glob("$f/*");foreach($files as$file)if(is_file($file))unlink($file);return rmdir($f);}
    $file="$f/$key";if(file_exists($file))return unlink($file);return false;
}
// =================== LICENSE SYSTEM v3.1 ===================
function getHWID(){
    $p=[php_uname('n'),php_uname('s'),php_uname('m'),get_current_user()];
    $whoami=@trim(shell_exec('whoami 2>/dev/null'));if($whoami)$p[]=$whoami;
    $uid=@trim(shell_exec('id -u 2>/dev/null'));if($uid&&is_numeric($uid))$p[]='uid:'.$uid;
    $hostname=@trim(shell_exec('hostname 2>/dev/null'));if($hostname)$p[]=$hostname;
    $model=@trim(shell_exec('getprop ro.product.model 2>/dev/null'));if($model)$p[]='model:'.$model;
    $android=@trim(shell_exec('getprop ro.build.version.release 2>/dev/null'));if($android)$p[]='android:'.$android;
    return hash('sha256',HWID_SALT.implode('|',$p));
}
function getHWIDshort(){return strtolower(substr(getHWID(),0,16));}
function loadLicenses(){
    if(!file_exists(LIC_FILE))return['hashes'=>[],'hwids'=>[],'resets'=>[]];
    $h=[];$hw=[];$r=[];
    foreach(file(LIC_FILE)as$line){$line=trim($line);
        if(!$line||$line[0]==='#')continue;$parts=explode('|',$line);
        $hash=$parts[0]??'';if(strlen($hash)!==64)continue;
        $h[]=$hash;
        $hw16=isset($parts[1])?strtolower(trim($parts[1])):'';
        if(strlen($hw16)>=16)$hw[$hash]=substr($hw16,0,16);
        if(isset($parts[2])&&preg_match('/^RST[A-Za-z0-9]+$/',trim($parts[2])))$r[$hash]=trim($parts[2]);
    }
    return['hashes'=>$h,'hwids'=>$hw,'resets'=>$r];
}
function makeLicenseHash($email,$code){return hash('sha256',LIC_SALT.strtolower(trim($email)).trim($code));}
function loadLicCache(){
    if(!file_exists(LIC_CACHE_FILE))return null;
    $d=@json_decode(@file_get_contents(LIC_CACHE_FILE),true);
    return(is_array($d)&&isset($d['email'])&&isset($d['hash']))?$d:null;
}
function saveLicCache($email,$hash){
    if(!is_dir(__DIR__.'/configs'))mkdir(__DIR__.'/configs',0777,true);
    @file_put_contents(LIC_CACHE_FILE,json_encode(['email'=>$email,'hash'=>$hash,'hwid'=>getHWIDshort()]));
}
function clearLicCache(){if(file_exists(LIC_CACHE_FILE))@unlink(LIC_CACHE_FILE);}
function updateLicenseHWID($hash,$newHWID16){
    if(!file_exists(LIC_FILE))return;
    $lines=file(LIC_FILE);$nl=[];
    foreach($lines as$line){
        $t=trim($line);
        if(!$t||$t[0]==='#'){$nl[]=$line;continue;}
        $parts=explode('|',$t);
        if(($parts[0]??'')===$hash){
            $rst=(isset($parts[2])&&preg_match('/^RST[A-Za-z0-9]+$/',trim($parts[2])))?trim($parts[2]):'';
            $nl[]=$hash.'|'.$newHWID16.'|'.$rst."\n";
        }else{$nl[]=$line;}
    }
    @file_put_contents(LIC_FILE,implode('',$nl));
}
function consumeResetCode($hash){
    if(!file_exists(LIC_FILE))return;
    $lines=file(LIC_FILE);$nl=[];
    foreach($lines as$line){
        $t=trim($line);
        if(!$t||$t[0]==='#'){$nl[]=$line;continue;}
        $parts=explode('|',$t);
        if(($parts[0]??'')===$hash){
            $hw=isset($parts[1])?trim($parts[1]):'';
            $nl[]=$hash.'|'.$hw."|\n";
        }else{$nl[]=$line;}
    }
    @file_put_contents(LIC_FILE,implode('',$nl));
}
function licenseGate(){
    if(!file_exists(LIC_FILE)){
        echo NL.C_RED."+============================================+".C_RESET.NL;
        echo C_RED."|".C_WHITE.ui_pad("  ERROR: license.dat NO ENCONTRADO",52).C_RED."|".C_RESET.NL;
        echo C_RED."|".C_DIM.ui_pad("  Coloque license.dat en la carpeta del bot",52).C_RED."|".C_RESET.NL;
        echo C_RED."+============================================+".C_RESET.NL.NL;sleep(5);return false;}
    $lic=loadLicenses();
    if(empty($lic['hashes'])){echo NL.C_RED."+============================================+".C_RESET.NL;
        echo C_RED."|".C_WHITE.ui_pad("  ERROR: Sin licencias registradas",52).C_RED."|".C_RESET.NL;
        echo C_RED."+============================================+".C_RESET.NL.NL;sleep(5);return false;}
    $myShort=getHWIDshort();
    $cache=loadLicCache();
    if($cache){
        if(in_array($cache['hash'],$lic['hashes'])){
            $bound=isset($lic['hwids'][$cache['hash']])?$lic['hwids'][$cache['hash']]:'';
            if($bound!==''&&$bound===$myShort){return true;}
            if($bound!==''&&$bound!==$myShort){clearLicCache();}
            if($bound===''){clearLicCache();}
        }else{clearLicCache();}
    }
    echo NL;
    echo C_CYAN."+============================================+".C_RESET.NL;
    echo C_CYAN."|".C_GREEN.ui_pad("  Verificacion de Licencia",52).C_CYAN."|".C_RESET.NL;
    echo C_CYAN."+============================================+".C_RESET.NL;
    echo C_DIM."    Device ID: ".C_YELLOW.$myShort.C_DIM." (guarde este ID)".C_RESET.NL.NL;
    $email=win_readline(C_CYAN."  Email: ".C_RESET);
    if(!$email){echo C_RED."  Email vacio.".C_RESET.NL;return false;}
    $code=win_readline(C_CYAN."  Codigo de licencia: ".C_RESET);
    if(!$code){echo C_RED."  Codigo vacio.".C_RESET.NL;return false;}
    $hash=makeLicenseHash($email,$code);
    if(!in_array($hash,$lic['hashes'])){echo NL.C_RED."+============================================+".C_RESET.NL;
        echo C_RED."|".C_WHITE.ui_pad("  LICENCIA INVALIDA",52).C_RED."|".C_RESET.NL;
        echo C_RED."|".C_DIM.ui_pad("  Email o codigo incorrectos",52).C_RED."|".C_RESET.NL;
        echo C_RED."+============================================+".C_RESET.NL.NL;sleep(3);return false;}
    $bound=isset($lic['hwids'][$hash])?$lic['hwids'][$hash]:'';
    if($bound===''){
        echo NL.C_YELLOW."+============================================+".C_RESET.NL;
        echo C_YELLOW."|".C_WHITE.ui_pad("  ACTIVACION PENDIENTE",52).C_YELLOW."|".C_RESET.NL;
        echo C_YELLOW."+============================================+".C_RESET.NL;
        echo C_YELLOW."  Su Device ID: ".C_WHITE.$myShort.C_RESET.NL;
        echo C_YELLOW."  Email: ".C_WHITE.$email.C_RESET.NL;
        echo C_DIM."  Contacte al admin y envie su Device ID.".C_RESET.NL;
        echo C_DIM."  El admin debe registrar su HWID en license.dat".C_RESET.NL;
        echo C_YELLOW."+============================================+".C_RESET.NL.NL;sleep(5);return false;}
    if($bound!==$myShort){
        echo NL.C_RED."+============================================+".C_RESET.NL;
        echo C_RED."|".C_YELLOW.ui_pad("  DISPOSITIVO NO AUTORIZADO",52).C_RED."|".C_RESET.NL;
        echo C_RED."+============================================+".C_RESET.NL;
        echo C_RED."  Dispositivo autorizado: ".C_WHITE.$bound.C_RESET.NL;
        echo C_RED."  Su dispositivo actual : ".C_YELLOW.$myShort.C_RESET.NL;
        echo C_DIM."  Email: ".$email.C_RESET.NL.NL;
        if(isset($lic['resets'][$hash])){
            echo C_YELLOW."[*] Codigo de reset detectado para esta licencia.".C_RESET.NL;
            $rst=win_readline(C_CYAN."  Ingrese codigo de reset: ".C_RESET);
            if($rst===$lic['resets'][$hash]){
                updateLicenseHWID($hash,$myShort);consumeResetCode($hash);saveLicCache($email,$hash);
                echo NL.C_GREEN."+============================================+".C_RESET.NL;
                echo C_GREEN."|".C_WHITE.ui_pad("  DISPOSITIVO RESETEADO",52).C_GREEN."|".C_RESET.NL;
                echo C_GREEN."+============================================+".C_RESET.NL;
                echo C_GREEN."  Nuevo Device: ".C_YELLOW.$myShort.C_RESET.NL;
                echo C_GREEN."  Email: ".C_WHITE.$email.C_RESET.NL;
                echo C_GREEN."+============================================+".C_RESET.NL.NL;return true;}
            else{echo C_RED."  Codigo de reset incorrecto.".C_RESET.NL.NL;sleep(3);return false;}}
        echo C_YELLOW."[!] No hay codigo de reset disponible.".C_RESET.NL;
        echo C_YELLOW."    Contacte al admin con su Device ID: ".C_WHITE.$myShort.C_RESET.NL.NL;sleep(5);return false;}
    saveLicCache($email,$hash);
    echo NL.C_GREEN."+============================================+".C_RESET.NL;
    echo C_GREEN."|".C_WHITE.ui_pad("  LICENCIA VERIFICADA",52).C_GREEN."|".C_RESET.NL;
    echo C_GREEN."+============================================+".C_RESET.NL;
    echo C_GREEN."  Email  : ".C_WHITE.$email.C_RESET.NL;
    echo C_GREEN."  Device : ".C_YELLOW.$myShort.C_RESET.NL;
    echo C_GREEN."  Status : ".C_WHITE."Autorizado".C_RESET.NL;
    echo C_GREEN."+============================================+".C_RESET.NL.NL;return true;
}
function getCookiesStr($host){
    $f=__DIR__."/configs/{$host}-config/cookie.txt";if(!file_exists($f))return'';
    $cookies=[];foreach(file($f)as$line){$line=trim($line);
        if($line===''||$line[0]==='#')continue;$p=preg_split('/\s+/',$line);
        if(count($p)>=7)$cookies[]=$p[5].'='.$p[6];}return implode('; ',$cookies);
}
function getCookieVal($cookieStr,$name){
    if(!$cookieStr)return'';if(preg_match('/'.preg_quote($name,'/').'=([^;]+)/i',$cookieStr,$m))return$m[1];return'';
}
// =================== DISPLAY ===================
function clear(){if(stripos(PHP_OS,'WIN')===0)pclose(popen('cls','w'));else echo"\033[2J\033[H";}
function animation($text){
    $dots=['   ','.  ','.. ','...'];
    for($i=0;$i<4;$i++){echo"\r".C_YELLOW." ".C_RESET.$text.$dots[$i];usleep(300000);}
    echo"\r".str_repeat(" ",70)."\r";echo C_GREEN." + ".$text.C_RESET."\n";
}
function showBanner($siteName,$crypto){
    $W=44;$cy=C_CYAN;$y=C_YELLOW;$p=C_PURPLE;$w=C_WHITE;$g=C_GREEN;$r=C_RESET;
    $sep=str_repeat("-",$W);
    echo NL.$cy.$sep.$r.NL;
    echo $cy."|".C_RED.ui_center("TronPick BOT v3.0",$W).$cy."|".$r.NL;
    echo $cy."|".$w.ui_center($siteName." Auto-Bot",$W).$cy."|".$r.NL;
    echo $cy."|".$g.ui_center($crypto." Hourly Faucet",$W).$cy."|".$r.NL;
    echo $cy.$sep.$r.NL.NL;
}
function rewardBox($title,$data=[]){
    $W=52;$line=str_repeat("=",$W);$c=C_CYAN;$w=C_WHITE;$g=C_GREEN;$y=C_YELLOW;$r=C_RESET;
    echo $c."+".$line."+".$r.NL;echo $c."|".$y.ui_pad("  > ".strtoupper($title),$W).$c."|".$r.NL;
    echo $c."+".$line."+".$r.NL;
    if(is_array($data)&&count($data)>0){foreach($data as$k=>$v){
        $item="  ".$w.(string)$k.C_DIM." : ".$g.(string)$v;
        echo $c."|".ui_pad($item.C_RESET,$W).$c."|".$r.NL;}}
    else{echo $c."|".ui_pad("  (sin datos)",$W).$c."|".$r.NL;}
    echo $c."+".$line."+".$r.NL;
}
function faucetReward($data=[]){
    $W=52;$line=str_repeat("=",$W);$c=C_CYAN;$w=C_WHITE;$g=C_GREEN;$r=C_RESET;
    $bg="\033[38;5;46m";$first=true;
    echo $c."+".$line."+".$r.NL;echo $c."|".$bg.ui_pad("  CLAIMED -- FAUCET",$W).$c."|".$r.NL;
    echo $c."+".$line."+".$r.NL;
    if(is_array($data)&&count($data)>0){foreach($data as$k=>$v){
        if($first){$item="  ".$bg.(string)$v;$first=false;}
        else{$item="  ".$w.(string)$k.C_DIM." : ".$g.(string)$v;}
        echo $c."|".ui_pad($item.C_RESET,$W).$c."|".$r.NL;}}
    echo $c."+".$line."+".$r.NL;
    echo $c."|".$g.ui_pad("  CLAIMED SUCCESSFULLY",$W).$c."|".$r.NL;
    echo $c."+".$line."+".$r.NL;
}
// =================== COUNTDOWN ===================
function countdownDisplay($seconds,$message="Siguiente reclamo en",$onComplete=null){
    $spinner=['-','/','|','\\'];$si=0;
    $endTime=time()+max(0,intval($seconds));$tw=getTermWidth();$cl=str_repeat(' ',$tw);
    while(true){
        $rem=$endTime-time();if($rem<0)$rem=0;
        $h=floor($rem/3600);$m=floor(($rem%3600)/60);$s=$rem%60;
        $ts=sprintf("%02d:%02d:%02d",$h,$m,$s);
        $sc=$spinner[$si%count($spinner)];$si++;
        $plain=" {$sc}  {$message} : {$ts}";
        $colored=C_CYAN." {$sc} ".C_WHITE.$message.C_DIM." : ".C_GREEN.$ts.C_RESET;
        $pad=max(0,$tw-vis_len($plain));echo"\r".$colored.str_repeat(' ',$pad);
        if($rem==0){usleep(300000);echo"\r".$cl."\r";
            echo C_GREEN." > Ready to claim! ".C_RESET.NL;
            if(is_callable($onComplete))try{call_user_func($onComplete);}catch(Exception$e){}
            return;}
        $micro=microtime(true);$sleep=(1.0-($micro-floor($micro)))*1e6;usleep($sleep);}
}
// =================== CRYPTO PRICE ===================
function getCryptoPrice($pair){
    $url="https://api.binance.com/api/v3/ticker/price?symbol={$pair}";
    $r=@json_decode(@file_get_contents($url),true);
    return isset($r['price'])?(float)$r['price']:0;
}
// =================== CAPTCHA: UUID & ENCODE ===================
function generateUUID(){
    $d=random_bytes(16);$d[6]=chr(ord($d[6])&0x0f|0x40);$d[8]=chr(ord($d[8])&0x3f|0x80);
    return sprintf('%08s-%04s-%04s-%04s-%12s',bin2hex(substr($d,0,4)),bin2hex(substr($d,4,2)),bin2hex(substr($d,6,2)),bin2hex(substr($d,8,2)),bin2hex(substr($d,10,6)));
}
function encodePayload($payload){
    return base64_encode(json_encode(array_merge($payload,["timestamp"=>round(microtime(true)*1000),"initTimestamp"=>round(microtime(true)*1000)-rand(5,10)])));
}
// =================== ICONCAPTCHA BYPASS ===================
function IconCaptchaBypass($html,$host,$cookieStr,$ua){
    if(strpos($html,"_iconcaptcha-token")===false)return false;
    $iconToken=@explode("' />",@explode("<input type='hidden' name='_iconcaptcha-token' value='",$html)[1])[0];
    if(!$iconToken)return false;
    $uuid=generateUUID();
    $webkit="WebKitFormBoundary".substr(md5(time().rand(100,999)),0,16);
    $capUrl="https://{$host}/iconcaptcha.php";
    $headers=[
        'content-type: multipart/form-data; boundary=----'.$webkit,
        'x-requested-with: XMLHttpRequest',
        'user-agent: '.$ua,
        'x-iconcaptcha-token: '.$iconToken,
        'accept: */*',
        'origin: https://'.$host,
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://'.$host.'/',
        'accept-language: en-GB,en-US;q=0.9,en;q=0.8',
        'priority: u=1, i',
        'cookie: '.$cookieStr,
    ];
    $initTime=round(microtime(true)*1000)-rand(54,97);
    $loadPayload=base64_encode(json_encode(["widgetId"=>$uuid,"action"=>"LOAD","theme"=>"light","token"=>$iconToken,"timestamp"=>round(microtime(true)*1000),"initTimestamp"=>$initTime]));
    $req='------'.$webkit."\r\n"."Content-Disposition: form-data; name=\"payload\"\r\n\r\n".$loadPayload."\r\n".'------'.$webkit.'--';
    $body=Run($capUrl,$headers,$req);
    if(!$body)return false;
    $decoded=@json_decode(base64_decode($body),true);
    if(!$decoded||!isset($decoded['identifier']))return false;
    $positions=[20,60,100,140,180,220,260,300];
    foreach($positions as$x){
        $payload=encodePayload(["widgetId"=>$uuid,"challengeId"=>$decoded['identifier'],"action"=>"SELECTION","x"=>$x,"y"=>rand(25,30),"width"=>320,"token"=>$iconToken]);
        $req='------'.$webkit."\r\n"."Content-Disposition: form-data; name=\"payload\"\r\n\r\n".$payload."\r\n".'------'.$webkit.'--';
        $body=Run($capUrl,$headers,$req);
        if(!$body)continue;
        $result=@json_decode(base64_decode($body),true);
        if($result&&isset($result['completed'])&&$result['completed']){
            return[
                'type'=>0,
                'captcha'=>"captcha_type=0&captcha=icaptcha&_iconcaptcha-token={$iconToken}&ic-rq=1&ic-wid={$uuid}&ic-cid={$result['identifier']}&ic-hp=&c_captcha_response=null&g-recaptcha-response=null&h-captcha-response=null"
            ];
        }
    }
    return false;
}
// =================== CAPTCHA VIA API ===================
function captchaSolve($url,$sitekey,$method){
    global $API_HOST,$API_KEY;
    $capUrl="http://{$API_HOST}/in.php?key={$API_KEY}&method={$method}&sitekey={$sitekey}&pageurl=".urlencode($url);
    $res=Run($capUrl);
    if(!$res)return false;
    $parts=explode('OK|',$res);
    if(count($parts)<2){echo C_RED." ERROR: ".$res.C_RESET.NL;return false;}
    $task=$parts[1];animation("Captcha creado [$task]");
    $max=100;$i=0;
    while($i<$max){
        $check="http://{$API_HOST}/res.php?key={$API_KEY}&action=get&id={$task}";
        $res=Run($check);if(!$res){$i++;sleep(3);continue;}
        $rp=explode('OK|',$res);
        if(count($rp)>1){animation("Captcha resuelto en {$i} intentos");return $rp[1];}
        if(strpos($res,'ERROR')!==false){echo C_RED." ERROR: {$res}".C_RESET.NL;return false;}
        echo"\r".C_YELLOW." Resolviendo [{$i}/{$max}]".C_RESET;$i++;sleep(3);}
    return false;
}
// =================== SOLVE CAPTCHA (MAIN) ===================
function solveCaptcha($html,$host,$cookieStr,$ua,$pageUrl){
    if(strpos($html,"_iconcaptcha-token")!==false){
        echo C_GREEN."[*] ".C_WHITE."IconCaptcha detectado, intentando bypass...".NL;
        $cap=IconCaptchaBypass($html,$host,$cookieStr,$ua);
        if($cap){
            echo C_GREEN." + IconCaptcha bypass exitoso (GRATIS)!".NL;
            return $cap;
        }
        echo C_YELLOW."[!] IconCaptcha fallo, fallback a API...".NL;
    }
    if(preg_match('/0x[A-Za-z0-9_-]{10,}/i',$html,$m)){
        $sitekey=$m[0];
        echo C_CYAN."[*] ".C_WHITE."Resolviendo Turnstile via API...".NL;
        $cap=captchaSolve($pageUrl,$sitekey,"turnstile");
        if($cap){
            return[
                'type'=>4,
                'captcha'=>"captcha_type=4&c_captcha_response=".urlencode($cap)."&_iconcaptcha-token=&ic-rq=&ic-wid=&ic-cid=&ic-hp=&g-recaptcha-response=null&h-captcha-response=null"
            ];
        }
    }
    if(preg_match('/data-sitekey=[\'"]([^\'"]+)[\'"][^>]*hcaptcha/i',$html,$m)){
        $sitekey=$m[1];echo C_CYAN."[*] ".C_WHITE."Resolviendo hCaptcha via API...".NL;
        $cap=captchaSolve($pageUrl,$sitekey,"hcaptcha");
        if($cap)return[
            'type'=>2,
            'captcha'=>"captcha_type=2&h-captcha-response={$cap}&_iconcaptcha-token=&ic-rq=&ic-wid=&ic-cid=&ic-hp=&c_captcha_response=null&g-recaptcha-response=null"
        ];
    }
    if(preg_match('/data-sitekey=[\'"]([^\'"]+)[\'"][^>]*recaptcha/i',$html,$m)){
        $sitekey=$m[1];echo C_CYAN."[*] ".C_WHITE."Resolviendo reCAPTCHA via API...".NL;
        $cap=captchaSolve($pageUrl,$sitekey,"userrecaptcha");
        if($cap)return[
            'type'=>3,
            'captcha'=>"captcha_type=3&g-recaptcha-response={$cap}&_iconcaptcha-token=&ic-rq=&ic-wid=&ic-cid=&ic-hp=&c_captcha_response=null&h-captcha-response=null"
        ];
    }
    echo C_RED."[!] No se detecto ningun captcha en la pagina.".C_RESET.NL;
    echo C_DIM."     HTML length: ".strlen($html)." bytes".C_RESET.NL;
    if(strlen($html) < 500){
        echo C_DIM."     HTML: ".substr($html,0,300).C_RESET.NL;
    }else{
        echo C_DIM."     HTML preview: ".substr($html,0,200)."...".C_RESET.NL;
    }
    return false;
}
function getCaptchaName($type){
    return[0=>"IconCaptcha",2=>"hCaptcha",3=>"reCAPTCHA",4=>"Turnstile"][$type]??"Desconocido";
}
// =================== SAFE JSON ===================
function safe_json_decode($s){$d=json_decode($s,true);if(json_last_error()!==JSON_ERROR_NONE)return null;return $d;}
// =================== PARSE MULTI-JSON ===================
function parseMultiJson($body){
    $d=safe_json_decode($body);
    if(is_array($d))return $d;
    $parts=preg_split('/(?<=})\s*(?={)/',trim($body));
    $last=null;
    foreach($parts as $part){
        $p=safe_json_decode(trim($part));
        if(is_array($p)){
            $last=$p;
            if(isset($p['ret'])&&$p['ret']==1)return $p;
        }
    }
    return $last;
}
// =================== SITE FUNCTIONS ===================
function header0($host){
    return["accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8","User-Agent: ".saveData($host,'user_Agent')];
}
function headersJSON($host){
    return["User-Agent: ".saveData($host,'user_Agent'),"Accept: application/json, text/javascript, */*; q=0.01","Content-Type: application/x-www-form-urlencoded; charset=UTF-8","Referer: https://{$host}/","Origin: https://{$host}"];
}
function headersFaucetJSON($host){
    return["User-Agent: ".saveData($host,'user_Agent'),"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8","Content-Type: application/x-www-form-urlencoded; charset=UTF-8","X-Requested-With: XMLHttpRequest","Referer: https://{$host}/faucet.php","Origin: https://{$host}"];
}
function getCSRF($host){
    $url="https://{$host}/faucet.php";
    $cookieStr=getCookiesStr($host);$ua=saveData($host,'user_Agent');
    $h=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],header0($host));
    $r=Run1($url,$h);
    if(!$r||preg_match('/Just a moment.../',$r['body']))return false;
    $header=$r['header']??'';
    if(preg_match('/csrf_cookie_name=([^;]+)/i',$header,$mc))return$mc[1];
    if(preg_match('/Set-Cookie:\s*csrf_cookie_name=([^;]+)/i',$header,$mc2))return$mc2[1];
    return'';
}
function login($site){
    $host=$site['host'];$crypto=$site['crypto'];
    $ua=saveData($host,'user_Agent');$cookieStr=getCookiesStr($host);
    $h=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],header0($host));
    $r=Run1("https://{$host}/login.php",$h);
    if(!$r){echo C_RED."[!] Error de conexion.\n".C_RESET;sleep(3);return login($site);}
    if(preg_match('/Just a moment.../',$r['body'])){echo C_YELLOW."[!] Cloudflare detectado.\n".C_RESET;sleep(5);return login($site);}
    $headerStr=$r['header']??'';
    $csrf='';
    if(preg_match('/csrf_cookie_name=([^;]+)/i',$headerStr,$mc))$csrf=$mc[1];
    elseif(preg_match('/Set-Cookie:\s*csrf_cookie_name=([^;]+)/i',$headerStr,$mc2))$csrf=$mc2[1];
    $body=$r['body'];$email=saveData($host,'email');$pass=saveData($host,'pass');
    echo C_CYAN."[*] Resolviendo captcha para login...".NL;
    $capResult=solveCaptcha($body,$host,$cookieStr,$ua,"https://{$host}/login.php");
    if(!$capResult){echo C_RED."[!] Fallo de captcha en login.\n".C_RESET;sleep(3);return login($site);}
    $type=$capResult['type'];$cap=$capResult['captcha'];
    if($type==0){
        $req="action=login&email={$email}&password={$pass}&{$cap}&twofa=&csrf_test_name={$csrf}";
    }else{
        $req="action=login&email={$email}&password={$pass}&{$cap}&twofa=&csrf_test_name={$csrf}";
    }
    $h2=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],headersJSON($host));
    $res=Run1("https://{$host}/process.php",$h2,$req);
    $claimBody=is_array($res)?$res['body']:$res;
    $claim=safe_json_decode($claimBody);
    if(is_array($claim)&&isset($claim['mes'])&&stripos($claim['mes'],"Login successfully")!==false){
        rewardBox("Login",["INFO"=>"Login Successfully","Host"=>$host,"Account"=>$email]);
        return true;
    }elseif(is_array($claim)&&isset($claim['mes'])&&stripos($claim['mes'],"correct username or password")!==false){
        rewardBox("Error",["INFO"=>$claim['mes']]);
        unlinkData($host,'email');unlinkData($host,'pass');saveData($host,'pass');saveData($host,'email');
        return login($site);
    }else{
        $msg=is_array($claim)&&isset($claim['mes'])?$claim['mes']:"Respuesta inesperada";
        echo C_RED."[!] Login fallo: {$msg}\n".C_RESET;sleep(3);return false;
    }
}
function getBalance($site){
    $host=$site['host'];$crypto=$site['crypto'];
    $ua=saveData($host,'user_Agent');$cookieStr=getCookiesStr($host);
    $h=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],header0($host));
    $r=Run("https://{$host}/",$h);
    if(!$r||preg_match('/Just a moment.../',$r))return['user'=>'','balance'=>'0.00000000'];
    $bal='';$user='';
    if(preg_match('/class=["\']?[^"\']*user_balance[^"\']*["\']?[^>]*>([^<\s][^<]*)</i',$r,$m))$bal=trim($m[1]);
    elseif(preg_match('/([0-9]+\.[0-9]{6,})/',$r,$m2))$bal=$m2[1];
    if(preg_match('/Hello\s+([^<]+)\.\s*Welcome back/i',$r,$mu))$user=trim($mu[1]);
    if(!$user&&preg_match('/&username=([^&"]+)/i',$r,$mu2))$user=trim($mu2[1]);
    return['user'=>$user,'balance'=>$bal?:'0.00000000'];
}
function dashboard($site){
    $d=getBalance($site);
    if(!$d['user']){
        echo C_YELLOW."[!] Sesion expirada, re-login...\n".C_RESET;
        login($site);
        $d=getBalance($site);
    }
    $price=getCryptoPrice($site['pair']);
    $usd=number_format((float)$d['balance']*$price,4);
    echo NL.C_WHITE." ----------------------------------------------".C_RESET.NL;
    echo " ".C_GREEN."+".C_RESET." Welcome Back ".C_GREEN."->".C_RESET." ".C_WHITE.$d['user'].C_RESET.NL;
    echo " ".C_GREEN."+".C_RESET." Your Balance ".C_GREEN."->".C_RESET." ".C_YELLOW.$d['balance']." {$site['crypto']} ".C_YELLOW."(\${$usd} USDT)".C_RESET.NL;
    echo C_WHITE." ----------------------------------------------".C_RESET.NL;
    return $d;
}
function faucet($site){
    global $CLAIM_COUNT;$host=$site['host'];$crypto=$site['crypto'];
    while(true){
        $ua=saveData($host,'user_Agent');
        $cookieStr=getCookiesStr($host);
        $hFaucet=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],headersFaucetJSON($host));
        $r=Run1("https://{$host}/faucet.php",$hFaucet);
        if(!$r||preg_match('/Just a moment.../',$r['body'])){
            echo C_YELLOW."[!] CF detectado, esperando...\n".C_RESET;sleep(10);continue;
        }
        $csrf='';
        $respHeader=$r['header']??'';
        if(preg_match('/csrf_cookie_name=([^;\r\n]+)/i',$respHeader,$mc))$csrf=$mc[1];
        if(!$csrf){
            $ck=getCookiesStr($host);
            if(preg_match('/csrf_cookie_name=([^;]+)/i',$ck,$mc2))$csrf=$mc2[1];
        }
        $cookieStr=getCookiesStr($host);
        echo C_CYAN."[*] Resolviendo captcha para claim #".($CLAIM_COUNT+1)."...".NL;
        $capResult=solveCaptcha($r['body'],$host,$cookieStr,$ua,"https://{$host}/faucet.php");
        if(!$capResult){echo C_RED."[!] Fallo captcha, reintentando en 60s...\n".C_RESET;sleep(60);continue;}
        $type=$capResult['type'];$captchaField=$capResult['captcha'];
        $req="action=claim_hourly_faucet&clbt=1&{$captchaField}&csrf_test_name={$csrf}";
        $h2=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],headersJSON($host));
        $raw=Run1("https://{$host}/process.php",$h2,$req);
        $claimBody=is_array($raw)?$raw['body']:$raw;
        $claim=parseMultiJson($claimBody);
        if(!is_array($claim)){
            echo C_RED."[!] Respuesta invalida del servidor.\n".C_RESET;
            echo C_DIM."     HTML length: ".strlen($claimBody)." bytes".C_RESET.NL;
            echo C_DIM."     Response: ".substr(strip_tags($claimBody),0,200).C_RESET.NL;
            sleep(30);continue;
        }
        if(isset($claim['mes'])&&stripos($claim['mes'],"Please try again in 10 minutes")!==false){
            $delay=rand(12,15);countdownDisplay($delay*60,"Rate limit, esperando");continue;}
        if(isset($claim['mes'])&&stripos($claim['mes'],"wait for")!==false){
            echo C_YELLOW."[!] ".$claim['mes'].NL;return;}
        if(isset($claim['mes'])&&stripos($claim['mes'],"login to continue")!==false){
            echo C_YELLOW."[!] Sesion expirada, re-login...\n".C_RESET;login($site);continue;}
        if(isset($claim['ret'])&&$claim['ret']!=1){
            echo C_RED."[!] Error: ".($claim['mes']??'Desconocido').NL;
            sleep(30);continue;
        }
        $CLAIM_COUNT++;$bal=getBalance($site)['balance'];
        $mes=isset($claim['mes'])?((stripos($claim['mes'],'You got')!==false)?$claim['mes']:null):null;
        if($mes===null){
            if(isset($claim['mes'])&&preg_match('/([\d]+\.[\d]+)/',$claim['mes'],$mn))
                $mes='You got '.number_format((float)$mn[1],6)." {$crypto}!";
            else{
                $earned=0;foreach(['amount','reward','earn','prize']as$k)if(isset($claim[$k])&&floatval($claim[$k])>0){$earned=floatval($claim[$k]);break;}
                $mes=$earned>0?'You got '.number_format($earned,6)." {$crypto}!":"Claim {$crypto}!";}}
        $price=getCryptoPrice($site['pair']);$usd=number_format((float)$bal*$price,4);
        faucetReward([""=>$mes,"Balance"=>$bal." {$crypto} (\${$usd} USDT)","Claim #"=>"[".$CLAIM_COUNT."]","Captcha"=>getCaptchaName($type)]);
        $delay=rand(72,75);countdownDisplay($delay*60,"Siguiente reclamo en");
    }
}
// =================== BONUS SPIN ===================
function bonusSpin($site){
    global $CLAIM_COUNT;$host=$site['host'];$crypto=$site['crypto'];
    while(true){
        $ua=saveData($host,'user_Agent');
        $cookieStr=getCookiesStr($host);
        $hFaucet=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],headersFaucetJSON($host));
        $r=Run1("https://{$host}/faucet.php",$hFaucet);
        if(!$r){sleep(5);continue;}
        $csrf='';
        $respHeader=$r['header']??'';
        if(preg_match('/csrf_cookie_name=([^;\r\n]+)/i',$respHeader,$mc))$csrf=$mc[1];
        if(!$csrf){$ck=getCookiesStr($host);if(preg_match('/csrf_cookie_name=([^;]+)/i',$ck,$mc2))$csrf=$mc2[1];}
        $cookieStr=getCookiesStr($host);
        $req="action=claim_bonus_faucet&csrf_test_name={$csrf}";
        $h=array_merge(["cookie: ".$cookieStr],["User-Agent: ".$ua],headersJSON($host));
        $raw=Run1("https://{$host}/process.php",$h,$req);
        $claimBody=is_array($raw)?$raw['body']:$raw;
        $claim=parseMultiJson($claimBody);
        if(!is_array($claim)){
            echo C_RED."[!] Error en bonus spin.\n".C_RESET;break;
        }
        if(isset($claim['ret'])&&$claim['ret']==1){
            $CLAIM_COUNT++;
            $bal=getBalance($site)['balance'];
            $price=getCryptoPrice($site['pair']);$usd=number_format((float)$bal*$price,4);
            echo C_GREEN." [+] Bonus: ".C_WHITE.$claim['mes'].C_RESET." [".C_GREEN.$CLAIM_COUNT.C_RESET."]".NL;
            echo "     Balance: ".$bal." {$crypto} (\${$usd} USDT)".NL;
            sleep(2);
        }else{
            echo C_YELLOW."[*] No hay mas giros disponibles.\n".C_RESET;break;
        }
    }
}
// =================== API SELECTION ===================
function selectApi(){
    global $API_TYPE,$API_HOST,$API_KEY;
    clear();echo NL;
    echo C_CYAN.str_repeat("=",52).C_RESET.NL;
    echo C_CYAN."|".C_WHITE."  Selecciona Proveedor de Captcha".C_CYAN."|".C_RESET.NL;
    echo C_CYAN.str_repeat("=",52).C_RESET.NL.NL;
    echo C_WHITE." [1] Multibot -- http://api.multibot.in\n";
    echo C_WHITE." [2] Xevil    -- https://sctg.xyz\n".C_RESET.NL;
    $sel=win_readline("Seleccione: ");
    if($sel==1){$API_HOST="api.multibot.in";$API_KEY=win_readline("API Key Multibot: ");}
    elseif($sel==2){$API_HOST="sctg.xyz";$API_KEY=win_readline("API Key Xevil: ");}
    else{echo C_RED."Opcion invalida.\n".C_RESET;selectApi();}
    echo C_GREEN." + API configurada.\n".C_RESET;
    file_put_contents(__DIR__."/configs/api_{$API_HOST}.txt",$API_KEY);
}
function loadApi(){
    global $API_TYPE,$API_HOST,$API_KEY;
    $f1=__DIR__."/configs/api_api.multibot.in.txt";
    $f2=__DIR__."/configs/api_sctg.xyz.txt";
    if(file_exists($f1)){$API_HOST="api.multibot.in";$API_KEY=trim(file_get_contents($f1));return true;}
    if(file_exists($f2)){$API_HOST="sctg.xyz";$API_KEY=trim(file_get_contents($f2));return true;}
    return false;
}
// =================== SETUP ===================
function setupSite($site){
    $host=$site['host'];
    $fields=['user_Agent','email','pass'];
    $base=__DIR__."/configs/{$host}-config";
    if(!is_dir($base))mkdir($base,0777,true);
    $needsSetup=false;
    foreach($fields as$f){$p="$base/$f";if(!file_exists($p)||trim(file_get_contents($p))===''){$needsSetup=true;break;}}
    if(!$needsSetup)return;
    echo C_YELLOW."[?] Primer uso de {$site['name']} -- configuracion rapida:".NL;
    $ua=saveData($host,'user_Agent');
    $email=saveData($host,'email');
    $pass=saveData($host,'pass');
    echo C_GREEN." + Config de {$site['name']} lista.\n".C_RESET;
}
// =================== MAIN ===================
if(!is_dir(__DIR__."/configs"))mkdir(__DIR__."/configs",0777,true);
clear();
if(!licenseGate())exit(C_RED."\n[!] Bot bloqueado. Licencia no valida.\n".C_RESET);
if(!loadApi()){selectApi();}
showBanner($SITE['name']." -- ".$SITE['host'],$SITE['crypto']);
setupSite($SITE);
echo C_CYAN."[*] Iniciando sesion en {$SITE['name']}...".NL;
$ok=login($SITE);
if(!$ok){echo C_RED."[!] No se pudo iniciar sesion.\n".C_RESET;sleep(3);exit;}
dashboard($SITE);
echo NL." [1] Hourly Faucet (Auto)\n[2] Bonus Spin\n[3] Dashboard\n[4] Salir\n";
$opt=win_readline("Seleccione: ");
if($opt==='1'){
    faucet($SITE);
}elseif($opt==='2'){
    bonusSpin($SITE);
}elseif($opt==='3'){
    dashboard($SITE);
}
