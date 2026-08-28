<?php
namespace App\Handlers;

use App\Telegram;
use App\Game\{PlayerService, EventService};
use App\Handler;

final class EventHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb || !str_starts_with($cb['data'], 'evt:')) {
            return false;
        }

        Telegram::answerCallback($cb['id']);
        $chatId = (int)$cb['message']['chat']['id'];
        $user = PlayerService::findByTg($chatId);

        if (!$user || $user['banned']) {
            return true;
        }

        [, $eventKey, $optKey] = explode(':', $cb['data']);
        (new EventService())->resolve($user, $eventKey, $optKey);
        return true;
    }
}
