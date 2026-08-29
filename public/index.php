<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/data/php-error.log');

require dirname(__DIR__) . '/src/bootstrap.php';

// اگر WEBHOOK_SECRET تنظیم شده باشد، فقط درخواست‌های تلگرام پذیرفته می‌شوند.
$secret = App\Config::get('WEBHOOK_SECRET');
if ($secret !== null && $secret !== '') {
    $provided = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
    if (!hash_equals($secret, $provided)) { http_response_code(403); exit; }
}

use App\Router;
use App\Handlers\StartHandler;
use App\Handlers\MenuHandler;
use App\Handlers\WorkHandler;
use App\Handlers\EventHandler;
use App\Handlers\ShopHandler;
use App\Handlers\AssetHandler;
use App\Handlers\BusinessHandler;
use App\Handlers\MissionHandler;
use App\Handlers\SocialHandler;
use App\Handlers\AdminHandler;
use App\Handlers\CityHandler;

try {
    $router = new Router();
    $router->add(new StartHandler());
    $router->add(new MenuHandler());
    $router->add(new WorkHandler());
    $router->add(new CityHandler());
    $router->add(new EventHandler());
    $router->add(new ShopHandler());
    $router->add(new AssetHandler());
    $router->add(new BusinessHandler());
    $router->add(new MissionHandler());
    $router->add(new SocialHandler());
    $router->add(new AdminHandler());

    $update = json_decode((string)file_get_contents('php://input'), true);

    if (!is_array($update)) {
        http_response_code(200);
        exit;
    }

    $router->dispatch($update);
} catch (\Throwable $e) {
    error_log(
        'INDEX FATAL: '
        . $e->getMessage() . ' @ '
        . $e->getFile() . ':'
        . $e->getLine()
    );
}

http_response_code(200);
