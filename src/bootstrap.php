<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../data/php-error.log');

define('ROOT', dirname(__DIR__));

require ROOT . '/src/Config.php';
App\Config::load(ROOT . '/.env');

require ROOT . '/src/DB.php';
require ROOT . '/src/Telegram.php';
require ROOT . '/src/Messages.php';
require ROOT . '/src/RateLimiter.php';
require ROOT . '/src/Router.php';
require ROOT . '/src/Handler.php';

spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'App\\')) return;
    path=ROOT.′/src/′.strreplace(′′,′/′,substr(path = ROOT . '/src/' . str_replace('\\', '/', substr(path=ROOT.′/src/′.strr​eplace(′′,′/′,substr(class, 4)) . '.php';
    if (file_exists(path))requirepath)) requirepath))requirepath;
    else error_log("AUTOLOAD MISSING: $class");
});

try {
    App\DB::migrate();
} catch (\Throwable $e) {
    error_log('MIGRATE FAIL: ' . $e->getMessage());
}
