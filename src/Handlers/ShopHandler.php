<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{Catalog, EconomyService, PlayerService, MissionService, AchievementService};
use App\Handler;

final class ShopHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb) {
            return false;
        }

        $data = $cb['data'];
        if (!str_starts_with($data, 'shop:')) {
            return false;
        }

        Telegram::answerCallback($cb['id']);
        $chatId = (int)$cb['message']['chat']['id'];
        $msgId = (int)$cb['message']['message_id'];
        $user = PlayerService::findByTg($chatId);

        if (!$user || $user['banned']) {
            return true;
        }

        [, , $itemKey] = array_pad(explode(':', $data), 3, null);

        if ($data === 'shop:list') {
            self::showList($msgId, $user, $chatId);
            return true;
        }

        $item = Catalog::ITEMS[$itemKey] ?? null;
        if (!$item) {
            Telegram::sendMessage($chatId, Messages::get('invalid_input'));
            return true;
        }

        if ($user['level'] < $item['min_level']) {
            Telegram::sendMessage($chatId, "🔒 لول {$item['min_level']} لازمه.");
            return true;
        }

        if ($user['cash'] < $item['price']) {
            Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
            return true;
        }

        EconomyService::spend($user, $item['price'], 'خرید: ' . $item['name']);

        // مصرف فوری آیتم‌های مصرفی، انبار برای بقیه
        if (isset($item['effect']['energy']) || isset($item['effect']['health'])) {
            $sets = [];
            foreach (['energy' => 100, 'health' => 100] as $stat => $max) {
                if (isset($item['effect'][$stat])) {
                    $sets[$stat] = min($max, $user[$stat] + $item['effect'][$stat]);
                }
            }
            if ($sets) {
                PlayerService::update((int)$user['id'], $sets);
            }
            Telegram::sendMessage($chatId, "✅ <b>{$item['name']}</b> مصرف شد!\n{$item['emoji']} اثرش اعمال شد.");
        } else {
            DB::pdo()->prepare(
                "INSERT INTO player_items (user_id, item_key, qty) VALUES (?, ?, 1) 
                ON CONFLICT(user_id, item_key) DO UPDATE SET qty = qty + 1"
            )->execute([(int)$user['id'], $itemKey]);

            PlayerService::update((int)$user['id'], array_combine(
                array_keys($item['effect']),
                array_map(fn($k) => $user[$k] + $item['effect'][$k], array_keys($item['effect']))
            ));

            Telegram::sendMessage($chatId, "✅ <b>{$item['name']}</b> خریدی! {$item['emoji']}");
            AchievementService::check(PlayerService::findByTg($chatId), 'big_spender');
        }

        foreach (MissionService::progress($user, 'item_buys', 1) as $m) {
            Telegram::sendMessage($chatId, $m);
        }

        return true;
    }

    public static function showList(int $msgId, array $user, int $chatId): void
    {
        $rows = [];
        foreach (Catalog::ITEMS as $key => $item) {
            $locked = $user['level'] < $item['min_level'];
            $label = $locked ? "🔒 {$item['name']}" : "{$item['emoji']} {$item['name']} — " . number_format($item['price']) . "$";
            $rows[] = [Telegram::btn($label, $locked ? 'shop:locked' : "shop:buy:{$key}")];
        }
        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];

        Telegram::editMessage($chatId, $msgId,
            "🎒 <b>فروشگاه دارک سیتی</b>\n\n💵 نقدی: " . number_format($user['cash']) . " دلار",
            Telegram::kb($rows));
    }
}
