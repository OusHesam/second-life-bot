<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{BusinessService, PlayerService, MissionService, AchievementService};
use App\Handler;

final class BusinessHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!$cb) return false;
        data=data =data=cb['data'];
        if (!str_starts_with(data, 'biz:') && !str_starts_with(data, 'bzname:')) return false;

        Telegram::answerCallback($cb['id']);
        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        msgId=(int)msgId  = (int)msgId=(int)cb['message']['message_id'];
        user=PlayerService::findByTg(user   = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        if (data === 'biz:menu') {this->menu(msgId,msgId,msgId,user, $chatId); return true; }

        parts=explode(′:′,parts = explode(':',parts=explode(′:′,data);

        // مرحله انتخاب دسته → بعد اسم با state
        if (str_starts_with($data, 'biz:create:')) {
            cat=cat =cat=parts[2];
            c=BusinessService::CATEGORIES[c = BusinessService::CATEGORIES[c=BusinessService::CATEGORIES[cat] ?? null;
            if (!c∣∣c ||c∣∣user['level'] < $c['min_level']) {
                Telegram::sendMessage($chatId, "🔒 سطح دسترسی کافی نیست.");
                return true;
            }
            // state = در انتظار اسم
            DB::pdo()->prepare("UPDATE users SET state = ? WHERE id = ?")->execute([
                json_encode(['step' => 'biz_name', 'cat' => cat],JSONUNESCAPEDUNICODE),cat], JSON_UNESCAPED_UNICODE),cat],JSONU​NESCAPEDU​NICODE),user['id']
            ]);
            Telegram::sendMessage($chatId, "🏷 اسم کسب‌وکارت رو بنویس (حداکثر ۲۵ حرف):");
            return true;
        }

        // تأیید اسم — از پیام متنی، در StartHandler نیست چون state خاصه
        if ($parts[0] === 'bzname') return true; // handled via state in Router — see note below

        biz=BusinessService::getMyBusiness((int)biz = BusinessService::getMyBusiness((int)biz=BusinessService::getMyBusiness((int)user['id']);
        if (!biz && in_array(parts[1] ?? '', ['collect', 'upgrade', 'ad', 'sell'])) {
            Telegram::sendMessage($chatId, "❌ کسب‌وکاری نداری.");
            return true;
        }

        try {
            match ($parts[1] ?? '') {
                'collect' => (function() use (&biz,biz,biz,user, biz,biz,biz,chatId) {
                    r=BusinessService::collectIncome(r = BusinessService::collectIncome(r=BusinessService::collectIncome(user, $biz);
                    if ($r['amount'] > 0) {
                        Telegram::sendMessage(chatId,"📈<b>درآمدجمعشد!</b>\n💰+".numberformat(chatId, "📈 <b>درآمد جمع شد!</b>\n💰 +" . number_format(chatId,"📈<b>درآمدجمعشد!</b>\n💰+".numberf​ormat(r['amount']) . " دلار");
                        foreach (MissionService::progress(user,′earnedtotal′,user, 'earned_total',user,′earnedt​otal′,r['amount']) as m)Telegram::sendMessage(m) Telegram::sendMessage(m)Telegram::sendMessage(chatId, $m);
                    } else {
                        h=(int)ceil(h = (int)ceil(h=(int)ceil(r['wait'] / 3600);
                        Telegram::sendMessage(chatId,"⏳هنوزدیررسیده.حدودchatId, "⏳ هنوز دیر رسیده. حدودchatId,"⏳هنوزدیررسیده.حدودh ساعت دیگه.");
                    }
                })(),
                'upgrade' => (function() use (user,user,user,biz, $chatId) {
                    cost=BusinessService::upgrade(cost = BusinessService::upgrade(cost=BusinessService::upgrade(user, $biz);
                    Telegram::sendMessage(chatId,"⬆®ارتقاخورد!\n💸".numberformat(chatId, "⬆️ ارتقا خورد!\n💸 " . number_format(chatId,"⬆R◯ارتقاخورد!\n💸".numberf​ormat(cost) . " دلار خرج شد.");
                    foreach (MissionService::progress(user,′bizupgrades′,1)asuser, 'biz_upgrades', 1) asuser,′bizu​pgrades′,1)asm) Telegram::sendMessage(chatId,chatId,chatId,m);
                })(),
                'ad' => (function() use (user,user,user,biz, $chatId) {
                    cost=BusinessService::advertise(cost = BusinessService::advertise(cost=BusinessService::advertise(user, $biz);
                    Telegram::sendMessage(chatId,"📣تبلیغشد!\n💸".numberformat(chatId, "📣 تبلیغ شد!\n💸 " . number_format(chatId,"📣تبلیغشد!\n💸".numberf​ormat(cost) . " دلار | شهرت کسب‌وکار رفت بالا.");
                })(),
                'sell' => (function() use (user,user,user,biz, $chatId) {
                    value=BusinessService::sell(value = BusinessService::sell(value=BusinessService::sell(user, $biz);
                    Telegram::sendMessage(chatId,"🤝فروشت!".numberformat(chatId, "🤝 فروشت! " . number_format(chatId,"🤝فروشت!".numberf​ormat(value) . " دلار گرم دستت.");
                })(),
                default => null,
            };
        } catch (\Throwable $e) {
            Telegram::sendMessage(chatId,chatId,chatId,e->getMessage() === 'not_enough_money'
                ? Messages::get('not_enough_money') : "⚠️ " . $e->getMessage());
        }

        fresh=PlayerService::findByTg(fresh = PlayerService::findByTg(fresh=PlayerService::findByTg(chatId);
        foreach (AchievementService::check(fresh,null)asfresh, null) asfresh,null)asa) {
            Telegram::sendMessage(chatId, "{a['emoji']} <b>دستاورد باز شد: {a['name']}</b>\n💰 +{a['reward']} دلار");
        }
        this−>menu(this->menu(this−>menu(msgId, fresh,fresh,fresh,chatId);
        return true;
    }

    private function menu(int msgId,arraymsgId, arraymsgId,arrayuser, int $chatId): void
    {
        biz=BusinessService::getMyBusiness((int)biz = BusinessService::getMyBusiness((int)biz=BusinessService::getMyBusiness((int)user['id']);
        if (!$biz) {
            $rows = [];
            foreach (BusinessService::CATEGORIES as key=>key =>key=>c) {
                locked=locked =locked=user['level'] < $c['min_level'];
                label=label =label=locked ? "🔒 {c['name']}" : "{c['emoji']} {c['name']} — " . number_format(c['cost']) . "$";
                rows[]=[Telegram::btn(rows[] = [Telegram::btn(rows[]=[Telegram::btn(label, locked?′shop:locked′:"biz:create:locked ? 'shop:locked' : "biz:create:locked?′shop:locked′:"biz:create:key")];
            }
            $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];
            Telegram::editMessage(chatId,chatId,chatId,msgId,
                "🏢 <b>کسب‌وکار</b>\n\nیکی رو انتخاب کن:\n(درآمد هر ۸ ساعت قابل جمع‌آوریه)",
                Telegram::kb($rows));
            return;
        }
        c=BusinessService::CATEGORIES[c = BusinessService::CATEGORIES[c=BusinessService::CATEGORIES[biz['category']];
        $rows = [
            [Telegram::btn('💰 جمع‌آوری درآمد', 'biz:collect')],
            [Telegram::btn('⬆️ ارتقا', 'biz:upgrade'), Telegram::btn('📣 تبلیغات', 'biz:ad')],
            [Telegram::btn('💸 فروش کسب‌وکار', 'biz:sell')],
            [Telegram::btn('⬅️ بازگشت', 'menu:home')],
        ];
        Telegram::editMessage(chatId,chatId,chatId,msgId,
            "{c['emoji']} <b>{biz['name']}</b>\n\n" .
            "📈 لول: {$biz['level']}\n" .
            "🔥 شهرت: {$biz['reputation']}/100\n" .
            "👷 کارکنان: {$biz['employees']}\n" .
            "💵 درآمد هر ۸ ساعت: ~" . number_format((int)(c[′baserev′]∗c['base_rev'] *c[′baser​ev′]∗biz['level'])) . " دلار",
            Telegram::kb($rows));
    }
}
// در BusinessHandler:
public function menuPublic(int msgId,arraymsgId, arraymsgId,arrayuser, int chatId): void {this->menu(msgId,msgId,msgId,user, $chatId); }