<?php
namespace App\Handlers;

use App\{Messages, Telegram};
use App\Game\{Catalog, EconomyService, PlayerService, AchievementService};
use App\Handler;

final class AssetHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb) {
            return false;
        }

        $data = $cb['data'];
        if (!str_starts_with($data, 'prop:') && !str_starts_with($data, 'car:')) {
            return false;
        }

        Telegram::answerCallback($cb['id']);
        $chatId = (int)$cb['message']['chat']['id'];
        $msgId = (int)$cb['message']['message_id'];
        $user = PlayerService::findByTg($chatId);

        if (!$user || $user['banned']) {
            return true;
        }

        if ($data === 'prop:list') {
            $this->show($msgId, $user, $chatId, 'prop');
            return true;
        }

        if ($data === 'car:list') {
            $this->show($msgId, $user, $chatId, 'car');
            return true;
        }

        [, , , $key] = array_pad(explode(':', $data), 4, null);
        $catalog = str_starts_with($data, 'prop:') ? Catalog::PROPERTIES : Catalog::VEHICLES;
        $item = $catalog[$key] ?? null;

        if (!$item) {
            Telegram::sendMessage($chatId, Messages::get('invalid_input'));
            return true;
        }

        if ($user['level'] < $item['min_level']) {
            Telegram::sendMessage($chatId, "🔒 لول {$item['min_level']} لازمه.");
            return true;
        }

        if ($user['cash'] + $user['bank'] < $item['price']) {
            Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
            return true;
        }

        // اول از بانک، بعد نقد
        $need = $item['price'];
        $useBank = min($user['bank'], $need);
        $useCash = $need - $useBank;

        if ($useBank > 0) {
            EconomyService::spend($user, $useBank, 'خرید ' . $item['name'], true);
        }
        if ($useCash > 0) {
            EconomyService::spend($user, $useCash, 'خرید ' . $item['name']);
        }

        $updates = ['happiness' => min(100, $user['happiness'] + ($item['happiness'] ?? 0))];
        $kind = str_starts_with($data, 'prop:') ? 'prop' : 'car';

        if ($kind === 'prop') {
            $updates['property'] = $key;
            $flag = 'homeowner';
        } else {
            $updates['vehicle'] = $key;
            $updates['fame'] = $user['fame'] + $item['prestige'];
            $flag = 'first_car';
        }

        PlayerService::update((int)$user['id'], $updates);
        $fresh = PlayerService::findByTg($chatId);

        foreach (AchievementService::check($fresh, $flag) as $a) {
            Telegram::sendMessage($chatId, "{$a['emoji']} <b>دستاورد باز شد: {$a['name']}</b>\n💰 +{$a['reward']} دلار");
        }

        Telegram::sendMessage($chatId, "🎉 <b>{$item['name']}</b> خریدی! {$item['emoji']}");
        $this->show($msgId, $fresh, $chatId, $kind);
        return true;
    }

    public function show(int $msgId, array $user, int $chatId, string $kind): void
    {
        if ($kind === 'prop') {
            $catalog = Catalog::PROPERTIES;
            $owned = $user['property'];
            $title = "🏠 <b>املاک دارک سیتی</b>";
            $ownedText = $owned ? "مسکن فعلی: " . Catalog::PROPERTIES[$owned]['name'] : 'بی‌خانمان 😅';
            $pfx = 'prop';
        } else {
            $catalog = Catalog::VEHICLES;
            $owned = $user['vehicle'];
            $title = "🚗 <b>گاراژ</b>";
            $ownedText = $owned ? "ماشین فعلی: " . Catalog::VEHICLES[$owned]['name'] : 'هنوز ماشین نداری';
            $pfx = 'car';
        }

        $rows = [];
        foreach ($catalog as $key => $item) {
            $locked = $user['level'] < $item['min_level'];
            $mark = ($owned === $key) ? ' ✔️' : '';
            $label = $locked ? "🔒 {$item['name']} (لول {$item['min_level']})"
                : "{$item['emoji']} {$item['name']} — " . number_format($item['price']) . " دلار" . $mark;
            $rows[] = [Telegram::btn($label, ($owned === $key || $locked) ? 'shop:locked' : "{$pfx}:buy:{$key}")];
        }

        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];

        Telegram::editMessage($chatId, $msgId,
            "{$title}\n\n{$ownedText}\n💵 دارایی: " . number_format($user['cash'] + $user['bank']) . " دلار",
            Telegram::kb($rows));
    }

    public function showPublic(int $msgId, array $user, int $chatId, string $kind): void
    {
        $this->show($msgId, $user, $chatId, $kind);
    }
}
