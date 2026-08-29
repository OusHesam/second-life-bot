<?php
namespace App\Handlers;

use App\Telegram;
use App\Game\{MissionService, AchievementService, PlayerService};
use App\Handler;

final class MissionHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb) {
            return false;
        }

        $data = $cb['data'];
        if (!str_starts_with($data, 'mis:') && !str_starts_with($data, 'ach:')) {
            return false;
        }

        Telegram::answerCallback($cb['id']);
        $chatId = (int)$cb['message']['chat']['id'];
        $msgId = (int)$cb['message']['message_id'];
        $user = PlayerService::findByTg($chatId);

        if (!$user || $user['banned']) {
            return true;
        }

        if ($data === 'ach:list') {
            $this->achievements($msgId, $user, $chatId);
            return true;
        }

        if (str_starts_with($data, 'mis:claim:')) {
            $key = explode(':', $data)[2];
            $msg = MissionService::claim($user, $key);
            Telegram::sendMessage($chatId, $msg ?? "⚠️ جایزه‌ای برای گرفتن نیست.");
            $this->list($msgId, PlayerService::findByTg($chatId), $chatId);
            return true;
        }

        if ($data === 'mis:list') {
            $this->list($msgId, $user, $chatId);
            return true;
        }

        return true;
    }

    private function list(int $msgId, array $user, int $chatId): void
    {
        $rows = [];
        $text = "🎯 <b>مأموریت‌ها</b>\n\n";

        foreach (MissionService::listFor($user) as $key => $m) {
            $meta = $m['meta'];
            $pct = min(100, (int)($m['progress'] / $meta['target'] * 100));
            $status = $m['claimed'] ? '✅' : ($m['done'] ? '🎁' : '⏳');

            $text .= "{$status} {$meta['emoji']} <b>{$meta['name']}</b>\n" .
                "📊 {$m['progress']} / {$meta['target']}  ({$pct}٪)\n" .
                "🎁 جایزه: " . number_format($meta['reward_cash']) .
                ($meta['reward_xp'] ? " + {$meta['reward_xp']} XP" : '') . "\n\n";

            if ($m['done'] && !$m['claimed']) {
                $rows[] = [Telegram::btn("🎁 گرفتن جایزه: {$meta['name']}", "mis:claim:{$key}")];
            }
        }

        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];
        Telegram::editMessage($chatId, $msgId, $text, Telegram::kb($rows));
    }

    private function achievements(int $msgId, array $user, int $chatId): void
    {
        $st = \App\DB::pdo()->prepare("SELECT achievement_key FROM player_achievements WHERE user_id = ?");
        $st->execute([(int)$user['id']]);
        $owned = array_column($st->fetchAll(), 'achievement_key');

        $text = "🏆 <b>دستاوردها</b>\n\n";
        foreach (AchievementService::ALL as $key => $a) {
            $unlocked = in_array($key, $owned, true);
            $text .= ($unlocked ? "{$a['emoji']}" : '🔒') . " {$a['name']}" .
                ($unlocked ? " — +{$a['reward']} دلار" : "") . "\n";
        }

        Telegram::editMessage($chatId, $msgId, $text, Telegram::kb([[Telegram::btn('⬅️ بازگشت', 'menu:home')]]));
    }
}
