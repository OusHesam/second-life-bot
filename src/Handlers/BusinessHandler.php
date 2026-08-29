<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{BusinessService, PlayerService, MissionService, AchievementService};
use App\Handler;

final class BusinessHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb) {
            return false;
        }

        $data = $cb['data'];
        if (!str_starts_with($data, 'biz:') && !str_starts_with($data, 'bzname:')) {
            return false;
        }

        Telegram::answerCallback($cb['id']);
        $chatId = (int)$cb['message']['chat']['id'];
        $msgId = (int)$cb['message']['message_id'];
        $user = PlayerService::findByTg($chatId);

        if (!$user || $user['banned']) {
            return true;
        }

        if ($data === 'biz:menu') {
            $this->menu($msgId, $user, $chatId);
            return true;
        }

        $parts = explode(':', $data);

        if (str_starts_with($data, 'biz:create:')) {
            $cat = $parts[2];
            $c = BusinessService::CATEGORIES[$cat] ?? null;

            if (!$c || $user['level'] < $c['min_level']) {
                Telegram::sendMessage($chatId, "🔒 سطح دسترسی کافی نیست.");
                return true;
            }

            DB::pdo()->prepare("UPDATE users SET state = ? WHERE id = ?")->execute([
                json_encode(['step' => 'biz_name', 'cat' => $cat], JSON_UNESCAPED_UNICODE),
                $user['id']
            ]);

            Telegram::sendMessage($chatId, "🏷 اسم کسب‌وکارت رو بنویس (حداکثر ۲۵ حرف):");
            return true;
        }

        $biz = BusinessService::getMyBusiness((int)$user['id']);
        if (!$biz && in_array($parts[1] ?? '', ['collect', 'upgrade', 'ad', 'sell'])) {
            Telegram::sendMessage($chatId, "❌ کسب‌وکاری نداری.");
            return true;
        }

        try {
            match ($parts[1] ?? '') {
                'collect' => $this->collectIncome($user, $biz, $chatId),
                'upgrade' => $this->upgrade($user, $biz, $chatId),
                'ad' => $this->advertise($user, $biz, $chatId),
                'sell' => $this->sellBusiness($user, $biz, $chatId),
                default => null,
            };
        } catch (\Throwable $e) {
            Telegram::sendMessage($chatId, $e->getMessage() === 'not_enough_money'
                ? Messages::get('not_enough_money')
                : "⚠️ " . $e->getMessage());
        }

        $fresh = PlayerService::findByTg($chatId);
        foreach (AchievementService::check($fresh, null) as $a) {
            Telegram::sendMessage($chatId, "{$a['emoji']} <b>دستاورد باز شد: {$a['name']}</b>\n💰 +{$a['reward']} دلار");
        }

        $this->menu($msgId, $fresh, $chatId);
        return true;
    }

    private function collectIncome(array $user, array $biz, int $chatId): void
    {
        $r = BusinessService::collectIncome($user, $biz);
        if ($r['amount'] > 0) {
            Telegram::sendMessage($chatId, "📈 <b>درآمد جمع شد!</b>\n💰 +" . number_format($r['amount']) . " دلار");
            foreach (MissionService::progress($user, 'earned_total', $r['amount']) as $m) {
                Telegram::sendMessage($chatId, $m);
            }
        } else {
            $h = (int)ceil($r['wait'] / 3600);
            Telegram::sendMessage($chatId, "⏳ هنوز دیر رسیده. حدود {$h} ساعت دیگه!");
        }
    }

    private function upgrade(array $user, array $biz, int $chatId): void
    {
        $cost = BusinessService::upgrade($user, $biz);
        Telegram::sendMessage($chatId, "⬆️ ارتقا خورد!\n💸 " . number_format($cost) . " دلار");
        foreach (MissionService::progress($user, 'biz_upgrades', 1) as $m) {
            Telegram::sendMessage($chatId, $m);
        }
    }

    private function advertise(array $user, array $biz, int $chatId): void
    {
        $cost = BusinessService::advertise($user, $biz);
        Telegram::sendMessage($chatId, "📣 تبلیغ شد!\n💸 " . number_format($cost) . " دلار");
    }

    private function sellBusiness(array $user, array $biz, int $chatId): void
    {
        $value = BusinessService::sell($user, $biz);
        Telegram::sendMessage($chatId, "🤝 فروشت! " . number_format($value) . " دلار گرم دست گرفتی! 💵");
    }

    public function menu(int $msgId, array $user, int $chatId): void
    {
        $biz = BusinessService::getMyBusiness((int)$user['id']);

        if (!$biz) {
            $rows = [];
            foreach (BusinessService::CATEGORIES as $key => $c) {
                $locked = $user['level'] < $c['min_level'];
                $label = $locked ? "🔒 {$c['name']}" : "{$c['emoji']} {$c['name']} — " . number_format($c['cost']) . "$";
                $rows[] = [Telegram::btn($label, $locked ? 'shop:locked' : "biz:create:{$key}")];
            }
            $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];

            Telegram::editMessage($chatId, $msgId,
                "🏢 <b>کسب‌وکار</b>\n\nیکی رو انتخاب کن:\n(درآمد هر ۸ ساعت قابل جمع‌آوریه)",
                Telegram::kb($rows));
            return;
        }

        $c = BusinessService::CATEGORIES[$biz['category']];
        $rows = [
            [Telegram::btn('💰 جمع‌آوری درآمد', 'biz:collect')],
            [Telegram::btn('⬆️ ارتقا', 'biz:upgrade'), Telegram::btn('📣 تبلیغات', 'biz:ad')],
            [Telegram::btn('💸 فروش کسب‌وکار', 'biz:sell')],
            [Telegram::btn('⬅️ بازگشت', 'menu:home')],
        ];

        Telegram::editMessage($chatId, $msgId,
            "{$c['emoji']} <b>{$biz['name']}</b>\n\n" .
            "📈 لول: {$biz['level']}\n" .
            "🔥 شهرت: {$biz['reputation']}/100\n" .
            "👷 کارکنان: {$biz['employees']}\n" .
            "💵 درآمد هر ۸ ساعت: ~" . number_format((int)($c['base_rev'] * $biz['level'])) . " دلار",
            Telegram::kb($rows));
    }

    public function menuPublic(int $msgId, array $user, int $chatId): void
    {
        $this->menu($msgId, $user, $chatId);
    }
}
