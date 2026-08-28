<?php
namespace App\Handlers;

use App\{Messages, Telegram, DB};
use App\Game\{PlayerService, JobService};
use App\Handler;

final class MenuHandler implements Handler
{
    public function handle(array $update): bool
    {
        // Callbacks
        $cb = $update['callback_query'] ?? null;
        if ($cb) {
            $data = $cb['data'];
            $chatId = (int)$cb['message']['chat']['id'];
            $msgId = (int)$cb['message']['message_id'];
            $user = PlayerService::findByTg($chatId);

            Telegram::answerCallback($cb['id']);

            if (!$user) {
                return true;
            }

            if ($user['banned']) {
                Telegram::sendMessage($chatId, Messages::get('banned'));
                return true;
            }

            // Finish character creation
            if (str_starts_with($data, 'bg:')) {
                (new StartHandler())->finishCreation($user, substr($data, 3));
                return true;
            }

            if (str_starts_with($data, 'menu:')) {
                $this->route($user, substr($data, 5), $chatId, $msgId);
                return true;
            }

            return false;
        }

        // Reply-keyboard text commands
        $text = $update['message']['text'] ?? null;
        $from = $update['message']['from'] ?? null;
        if (!$text || !$from) {
            return false;
        }

        $user = PlayerService::findByTg($from['id']);
        if (!$user || $user['player_code'] === 'PENDING') {
            return false;
        }

        if ($user['banned']) {
            Telegram::sendMessage((int)$from['id'], Messages::get('banned'));
            return true;
        }

        if ($text === '/profile') {
            Telegram::sendMessage((int)$from['id'], PlayerService::renderProfile($user));
            return true;
        }

        if ($text === '/menu' || $text === '🏠 خانه') {
            Telegram::sendMessage((int)$from['id'], Messages::get('main_menu'), StartHandler::mainKb());
            return true;
        }

        return false;
    }

    private function route(array $user, string $action, int $chatId, int $msgId): void
    {
        switch ($action) {
            case 'profile':
                Telegram::editMessage($chatId, $msgId, PlayerService::renderProfile($user),
                    Telegram::kb([[Telegram::btn('⬅️ بازگشت', 'menu:home')]]));
                break;

            case 'jobs':
                $rows = [];
                foreach (JobService::availableJobs($user) as $key => $j) {
                    $label = $j['locked']
                        ? "🔒 {$j['title']} (لول {$j['min_level']})"
                        : "{$j['title']} — " . number_format($j['pay']) . "$";

                    if (!$j['locked']) {
                        $rows[] = [Telegram::btn($label, "job:take:{$key}")];
                    } else {
                        $rows[] = [Telegram::btn($label, 'job:locked')];
                    }
                }

                $cur = $user['job'] ? JobService::JOBS[$user['job']]['title'] : 'بی‌کار';
                $rows[] = [Telegram::btn('🔧 کار کن', 'job:work')];
                $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];

                Telegram::editMessage($chatId, $msgId, "💼 <b>شغل فعلی:</b> {$cur}\n\n⬇️ مشاغل در دسترس:", Telegram::kb($rows));
                break;

            case 'bank':
                $rows = [
                    [Telegram::btn('📥 واریز ۱۰٪', 'bank:deposit'), Telegram::btn('📤 برداشت ۱۰٪', 'bank:withdraw')],
                    [Telegram::btn('⬅️ بازگشت', 'menu:home')],
                ];

                Telegram::editMessage($chatId, $msgId,
                    "💰 <b>پول و بانک</b>\n\n💵 نقد: " . number_format($user['cash']) .
                    "\n🏦 بانک: " . number_format($user['bank']),
                    Telegram::kb($rows));
                break;

            case 'transfer':
                Telegram::editMessage($chatId, $msgId,
                    "💸 <b>انتقال پول</b>\n\nفرمت:\n<code>/pay SL-849271 500</code>\n\n🆔 شناسه طرف مقابل رو از پروفایلش بگیر.",
                    Telegram::kb([[Telegram::btn('⬅️ بازگشت', 'menu:home')]]));
                break;

            case 'biz':
                (new BusinessHandler())->menuPublic($msgId, $user, $chatId);
                break;

            case 'property':
                (new AssetHandler())->showPublic($msgId, $user, $chatId, 'prop');
                break;

            case 'garage':
                (new AssetHandler())->showPublic($msgId, $user, $chatId, 'car');
                break;

            case 'shop':
                ShopHandler::showList($msgId, $user, $chatId);
                break;

            case 'missions':
                Telegram::editMessage($chatId, $msgId, '⏳ ...', Telegram::kb([[Telegram::btn('رفرش', 'mis:list')]]));
                (new MissionHandler())->handle([
                    'callback_query' => [
                        'id' => 'x',
                        'data' => 'mis:list',
                        'message' => ['chat' => ['id' => $chatId], 'message_id' => $msgId],
                    ]
                ]);
                break;

            case 'achievements':
                Telegram::editMessage($chatId, $msgId, '⏳ ...', Telegram::kb([[Telegram::btn('رفرش', 'ach:list')]]));
                (new MissionHandler())->handle([
                    'callback_query' => [
                        'id' => 'x',
                        'data' => 'ach:list',
                        'message' => ['chat' => ['id' => $chatId], 'message_id' => $msgId],
                    ]
                ]);
                break;

            case 'ranking':
                $st = DB::pdo()->query(
                    "SELECT character_name, level, cash + bank AS worth FROM users WHERE banned = 0 AND player_code != 'PENDING' ORDER BY worth DESC LIMIT 10"
                );
                $text = "📊 <b>ثروتمندترین‌های دارک سیتی</b>\n\n";
                $i = 1;
                foreach ($st->fetchAll() as $row) {
                    $medal = match ($i) {
                        1 => '🥇',
                        2 => '🥈',
                        3 => '🥉',
                        default => '▫️'
                    };
                    $text .= "{$medal} {$row['character_name']} — لول {$row['level']} — " .
                        number_format((int)$row['worth']) . " دلار\n";
                    $i++;
                }
                Telegram::editMessage($chatId, $msgId, $text, Telegram::kb([[Telegram::btn('⬅️ بازگشت', 'menu:home')]]));
                break;

            case 'settings':
                $notif = $user['notif_enabled'] ? 'روشن ✅' : 'خاموش ❌';
                Telegram::editMessage($chatId, $msgId,
                    "⚙️ <b>تنظیمات</b>\n\n🔔 اعلان‌ها: {$notif}",
                    Telegram::kb([
                        [Telegram::btn('🔔 تغییر اعلان‌ها', 'set:notif')],
                        [Telegram::btn('⬅️ بازگشت', 'menu:home')],
                    ]));
                break;

            case 'home':
            default:
                Telegram::editMessage($chatId, $msgId, Messages::get('main_menu'), StartHandler::mainKb());
        }
    }
}
