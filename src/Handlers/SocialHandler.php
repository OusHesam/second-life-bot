<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{EconomyService, PlayerService, MissionService, AchievementService};
use App\Handler;

final class SocialHandler implements Handler
{
    public function handle(array $update): bool
    {
        $msg = $update['message'] ?? null;
        if (!$msg) {
            return false;
        }

        $text = trim($msg['text'] ?? '');

        if (!str_starts_with($text, '/pay ')) {
            return false;
        }

        $parts = preg_split('/\s+/', $text);
        if (count($parts) < 3) {
            Telegram::sendMessage((int)$msg['from']['id'], "فرمت: /pay SL-849271 مقدار\nمثال: /pay SL-849271 500");
            return true;
        }

        $user = PlayerService::findByTg((int)$msg['from']['id']);
        if (!$user || $user['banned']) {
            return true;
        }

        [, $code, $amount] = $parts;
        $amount = (int)$amount;
        $code = str_starts_with($code, 'SL-') ? $code : 'SL-' . $code;

        $st = DB::pdo()->prepare("SELECT * FROM users WHERE player_code = ? AND player_code != 'PENDING'");
        $st->execute([$code]);
        $target = $st->fetch();

        if (!$target) {
            Telegram::sendMessage((int)$msg['from']['id'], "❌ بازیکنی با این شناسه پیدا نشد.");
            return true;
        }

        try {
            EconomyService::transfer($user, (int)$target['id'], $amount);
        } catch (\Throwable $e) {
            Telegram::sendMessage((int)$msg['from']['id'],
                $e->getMessage() === 'not_enough_money'
                    ? Messages::get('not_enough_money')
                    : "⚠️ " . $e->getMessage());
            return true;
        }

        Telegram::sendMessage((int)$msg['from']['id'],
            "💸 " . number_format($amount) . " دلار به <b>{$target['character_name']}</b> ({$code}) فرستاده شد.");
        Telegram::sendMessage((int)$target['telegram_id'],
            "💵 <b>پول دریافت کردی!</b>\n\nاز <b>{$user['character_name']}</b>: +" . number_format($amount) . " دلار");

        MissionService::progress($user, 'transfers', 1);
        foreach (AchievementService::check(PlayerService::findByTg((int)$msg['from']['id']), 'transfer_sent') as $a) {
            Telegram::sendMessage((int)$msg['from']['id'], "{$a['emoji']} <b>دستاورد باز شد: {$a['name']}</b>");
        }

        return true;
    }
}
