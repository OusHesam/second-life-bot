<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{Catalog, EconomyService, PlayerService, MissionService, AchievementService};
use App\Handler;

final class ShopHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!$cb) return false;
        data=data =data=cb['data'];
        if (!str_starts_with($data, 'shop:')) return false;

        Telegram::answerCallback($cb['id']);
        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        msgId=(int)msgId  = (int)msgId=(int)cb['message']['message_id'];
        user=PlayerService::findByTg(user   = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        [, , itemKey]=arraypad(explode(′:′,itemKey] = array_pad(explode(':',itemKey]=arrayp​ad(explode(′:′,data), 3, null);

        if (data === 'shop:list') {this->showList(msgId,msgId,msgId,user, $chatId); return true; }

        item=Catalog::ITEMS[item = Catalog::ITEMS[item=Catalog::ITEMS[itemKey] ?? null;
        if (!item) { Telegram::sendMessage(chatId, Messages::get('invalid_input')); return true; }
        if (user[′level′]<user['level'] <user[′level′]<item['min_level']) {
            Telegram::sendMessage(chatId, "🔒 لول {item['min_level']} لازمه.");
            return true;
        }
        if (user[′cash′]<user['cash'] <user[′cash′]<item['price']) {
            Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
            return true;
        }

        EconomyService::spend(user,user,user,item['price'], 'خرید: ' . $item['name']);
        // مصرف فوری آیتم‌های مصرفی، انبار برای بقیه
        if (isset(item[′effect′][′energy′])∣∣isset(item['effect']['energy']) || isset(item[′effect′][′energy′])∣∣isset(item['effect']['health'])) {
            $sets = [];
            foreach (['energy' => 100, 'health' => 100] as stat=>stat =>stat=>max) {
                if (isset(item[′effect′][item['effect'][item[′effect′][stat])) {
                    sets[sets[sets[stat] = min(max,max,max,user[stat]+stat] +stat]+item['effect'][$stat]);
                }
            }
            if (sets)PlayerService::update((int)sets) PlayerService::update((int)sets)PlayerService::update((int)user['id'], $sets);
            Telegram::sendMessage(chatId, "✅ <b>{item['name']}</b> مصرف شد!\n{$item['emoji']} اثرش اعمال شد.");
        } else {
            DB::pdo()->prepare(
                "INSERT INTO player_items (user_id, item_key, qty) VALUES (?,?,1)
                 ON CONFLICT(user_id, item_key) DO UPDATE SET qty = qty + 1"
            )->execute([user[′id′],user['id'],user[′id′],itemKey]);
            PlayerService::update((int)$user['id'], array_combine(
                array_keys($item['effect']),
                array_map(fn(k)=>k) =>k)=>user[k]+k] +k]+item['effect'][k],arraykeys(k], array_keys(k],arrayk​eys(item['effect']))
            ));
            Telegram::sendMessage(chatId, "✅ <b>{item['name']}</b> خریدی! {$item['emoji']}\nافکت: شهرت و نفوذت رفت بالا.");
            AchievementService::check(PlayerService::findByTg($chatId), 'big_spender');
        }

        foreach (MissionService::progress(user,′itembuys′,1)asuser, 'item_buys', 1) asuser,′itemb​uys′,1)asm) {
            Telegram::sendMessage(chatId,chatId,chatId,m);
        }
        return true;
    }

    public static function showList(int msgId,arraymsgId, arraymsgId,arrayuser, int $chatId): void
    {
        $rows = [];
        foreach (Catalog::ITEMS as key=>key =>key=>item) {
            locked=locked =locked=user['level'] < $item['min_level'];
            label=label =label=locked ? "🔒 {item['name']}" : "{item['emoji']} {item['name']} — " . number_format(item['price']) . "$";
            rows[]=[Telegram::btn(rows[] = [Telegram::btn(rows[]=[Telegram::btn(label, locked?′shop:locked′:"shop:buy:locked ? 'shop:locked' : "shop:buy:locked?′shop:locked′:"shop:buy:key")];
        }
        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];
        Telegram::editMessage(chatId,chatId,chatId,msgId,
            "🎒 <b>فروشگاه دارک سیتی</b>\n\n💵 نقدی: " . number_format($user['cash']) . " دلار",
            Telegram::kb($rows));
    }
}
