<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('ROOT', dirname(__DIR__));

echo "<pre>";
echo "1. PHP: " . PHP_VERSION . "\n\n";

echo "2. Extensions:\n";
foreach (['pdo_sqlite', 'curl', 'json', 'mbstring'] as $ext) {
    echo "   ext:".(extensionloaded(ext: " . (extension_loaded(ext:".(extensionl​oaded(ext) ? "OK" : "MISSING") . "\n";
}
echo "\n";

echo "3. ROOT: " . ROOT . "\n";
echo "   .env: " . (file_exists(ROOT . '/.env') ? "YES" : "NO") . "\n\n";

$token = '';
$admin = '';
if (is_readable(ROOT . '/.env')) {
    foreach (file(ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        line=trim(line = trim(line=trim(line);
        if (line===′′∣∣line === '' ||line===′′∣∣line[0] === '#') continue;
        p=explode(′=′,p = explode('=',p=explode(′=′,line, 2);
        if (trim(p[0])===′BOTTOKEN′)p[0]) === 'BOT_TOKEN')p[0])===′BOTT​OKEN′)token = trim($p[1]);
        if (trim(p[0])===′ADMINIDS′)p[0]) === 'ADMIN_IDS')p[0])===′ADMINI​DS′)admin = trim($p[1]);
    }
}
echo "4. BOT_TOKEN: " . (token!==′′?"OKlen=".strlen(token !== '' ? "OK len=" . strlen(token!==′′?"OKlen=".strlen(token) : "EMPTY") . "\n";
echo "   ADMIN_IDS: " . (admin!==′′?admin !== '' ?admin!==′′?admin : "NOT SET") . "\n\n";

if (!is_dir(ROOT . '/data')) { @mkdir(ROOT . '/data', 0775, true); }
echo "5. data writable: " . (is_writable(ROOT . '/data') ? "YES" : "NO") . "\n\n";

echo "6. SQLite: ";
try {
    $pdo = new PDO('sqlite:' . ROOT . '/data/test.sqlite');
    $pdo->exec('CREATE TABLE IF NOT EXISTS t (id INTEGER)');
    echo "OK\n";
    @unlink(ROOT . '/data/test.sqlite');
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
echo "\n";

echo "7. Telegram API: ";
ch=curlinit("https://api.telegram.org/bot".ch = curl_init("https://api.telegram.org/bot" .ch=curli​nit("https://api.telegram.org/bot".token . "/getMe");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
res=curlexec(res = curl_exec(res=curle​xec(ch);
err=curlerror(err = curl_error(err=curle​rror(ch);
curl_close($ch);
if ($res !== false) {
    j=jsondecode((string)j = json_decode((string)j=jsond​ecode((string)res, true);
    echo (j[′ok′]??false)?"OK@".j['ok'] ?? false) ? "OK @" .j[′ok′]??false)?"OK@".j['result']['username'] : "API ERROR";
} else {
    echo "CURL FAIL: " . $err;
}
echo "\n</pre>";
