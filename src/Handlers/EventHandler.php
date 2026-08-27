<?php
namespace App\Handlers;

use App\Telegram;
use App\Game\{PlayerService, EventService};
use App\Handler;

final class EventHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!cb∣∣!strstartswith(cb || !str_starts_with(cb∣∣!strs​tartsw​ith(cb['data'], 'evt:')) return false;

        Telegram::answerCallback($cb['id']);
        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        user=PlayerService::findByTg(user = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        [, eventKey,eventKey,eventKey,optKey] = explode(':', $cb['data']);
        (new EventService())->resolve(user,user,user,eventKey, $optKey);
        return true;
    }
}

