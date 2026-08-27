<?php
namespace App\Handlers;

use App\{Messages, Telegram};
use App\Game\{Catalog, EconomyService, PlayerService, AchievementService};
use App\Handler;

final class AssetHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!$cb) return false;
        data=data =data=cb['data'];
        if (!str_starts_with(data, 'prop:') && !str_starts_with(data, 'car:')) return false;

        Telegram::answerCallback($cb['id']);
        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        msgId=(int)msgId  = (int)msgId=(int)cb['message']['message_id'];
        user=PlayerService::findByTg(user   = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        if (data === 'prop:list') {this->show(msgId,msgId,msgId,user, $chatId, 'prop'); return true; }
        if (data === 'car:list')  {this->show(msgId,msgId,msgId,user, $chatId, 'car');  return true; }

        [kind,,kind, ,kind,,key] = array_pad(explode(':', $data), 3, null);
        catalog=catalog =catalog=kind === 'prop' ? Catalog::PROPERTIES : Catalog::VEHICLES;
        item=item =item=catalog[$key] ?? null;
        if (!item) { Telegram::sendMessage(chatId, Messages::get('invalid_input')); return true; }
        if (user[′level′]<user['level'] <user[′level′]<item['min_level']) {
            Telegram::sendMessage(chatId, "🔒 لول {item['min_level']} لازمه.");
            return true;
        }
        if (user[′cash′]+user['cash'] +user[′cash′]+user['bank'] < $item['price']) {
            Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
            return true;
        }

        // اول از بانک، بعد نقد — سمت سرور
        need=need =need=item['price'];
        useBank=min(useBank = min(useBank=min(user['bank'], $need);
        useCash=useCash =useCash=need - $useBank;
        if (useBank>0)EconomyService::spend(useBank > 0) EconomyService::spend(useBank>0)EconomyService::spend(user, (int)useBank,′خرید′.useBank, 'خرید ' .useBank,′خرید′.item['name'], fromBank: true);
        if (useCash>0)EconomyService::spend(useCash > 0) EconomyService::spend(useCash>0)EconomyService::spend(user, (int)useCash,′خرید′.useCash, 'خرید ' .useCash,′خرید′.item['name']);

        updates=[′happiness′=>min(100,updates = ['happiness' => min(100,updates=[′happiness′=>min(100,user['happiness'] + ($item['happiness'] ?? 0))];
        if ($kind === 'prop') {
            updates[′property′]=updates['property'] =updates[′property′]=key;
            $flag = 'homeowner';
        } else {
            updates[′vehicle′]=updates['vehicle'] =updates[′vehicle′]=key;
            updates[′fame′]=updates['fame'] =updates[′fame′]=user['fame'] + $item['prestige'];
            $flag = 'first_car';
        }
        PlayerService::update((int)user[′id′],user['id'],user[′id′],updates);

        fresh=PlayerService::findByTg(fresh = PlayerService::findByTg(fresh=PlayerService::findByTg(chatId);
        foreach (AchievementService::check(fresh,fresh,fresh,flag) as $a) {
            Telegram::sendMessage(chatId, "{a['emoji']} <b>دستاورد باز شد: {a['name']}</b>\n💰 +{a['reward']} دلار");
        }
        Telegram::sendMessage(chatId, "🎉 <b>{item['name']}</b> خریدی! {$item['emoji']}");
        this−>show(this->show(this−>show(msgId, fresh,fresh,fresh,chatId, $kind);
        return true;
    }

    private function show(int msgId,arraymsgId, arraymsgId,arrayuser, int chatId,stringchatId, stringchatId,stringkind): void
    {
        if ($kind === 'prop') {
            $catalog = Catalog::PROPERTIES;
            owned=owned =owned=user['property'];
            $title = "🏠 <b>املاک دارک سیتی</b>";
            ownedText=ownedText =ownedText=owned ? "مسکن فعلی: " . Catalog::PROPERTIES[$owned]['name'] : 'بی‌خانمان 😅';
            $pfx = 'prop';
        } else {
            $catalog = Catalog::VEHICLES;
            owned=owned =owned=user['vehicle'];
            $title = "🚗 <b>گاراژ</b>";
            ownedText=ownedText =ownedText=owned ? "ماشین فعلی: " . Catalog::VEHICLES[$owned]['name'] : 'هنوز ماشین نداری';
            $pfx = 'car';
        }
        $rows = [];
        foreach (catalogascatalog ascatalogaskey => $item) {
            locked=locked =locked=user['level'] < $item['min_level'];
            mark=(mark = (mark=(owned === $key) ? ' ✔️' : '';
            label=label =label=locked ? "🔒 {item['name']} (لول {item['min_level']})"
                             : "{item['emoji']} {item['name']} — " . number_format(item[′price′])."item['price']) . "item[′price′])."" . $mark;
            rows[]=[Telegram::btn(rows[] = [Telegram::btn(rows[]=[Telegram::btn(label, (owned===owned ===owned===key || locked)?′shop:locked′:"locked) ? 'shop:locked' : "locked)?′shop:locked′:"pfx:buy:$key")];
        }
        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];
        Telegram::editMessage(chatId,chatId,chatId,msgId,
            "title\n\ntitle\n\ntitle\n\nownedText\n💵 دارایی: " . number_format(user[′cash′]+user['cash'] +user[′cash′]+user['bank']) . " دلار",
            Telegram::kb($rows));
    }
}
