<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\{Router, Telegram, RateLimiter, DB};
use App\Handlers\{StartHandler, ProfileHandler, MenuHandler, WorkHandler, EventHandler, AdminHandler};

$update = json_decode(file_get_contents('php://input'), true);
if (!$update) { http_response_code(200); exit; }

try {
    msg=msg  =msg=update['message'] ?? $update['callback_query']['message'] ?? null;
    chat=chat =chat=msg['chat']['id'] ?? ($update['callback_query']['from']['id'] ?? null);
    if (!$chat) exit;

    // Rate limit: 1 action / 1.5s per user (anti-spam)
    if (!RateLimiter::allow((int)$chat)) {
        Telegram::sendMessage((int)$chat, Messages::get('rate_limited'));
        exit;
    }

    $router = new Router();
    $router->add(new StartHandler());
    $router->add(new ProfileHandler());
    $router->add(new MenuHandler());
    $router->add(new WorkHandler());
    $router->add(new EventHandler());
    $router->add(new AdminHandler());

    router−>dispatch(router->dispatch(router−>dispatch(update);
} catch (\Throwable $e) {
    error_log('[SL] ' . $e->getMessage());
    if (isset(chat))Telegram::sendMessage((int)chat)) Telegram::sendMessage((int)chat))Telegram::sendMessage((int)chat, Messages::get('error'));
} finally {
    DB::close();
}
http_response_code(200);

