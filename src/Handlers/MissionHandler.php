<?php
namespace App\Handlers;

use App\Telegram;
use App\Game\{MissionService, AchievementService, PlayerService};
use App\Handler;

final class MissionHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!$cb) return false;
        data=data =data=cb['data'];
        if (!str_starts_with(data, 'mis:') && !str_starts_with(data, 'ach:')) return false;

        Telegram::answerCallback($cb['id']);
        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        msgId=(int)msgId  = (int)msgId=(int)cb['message']['message_id'];
        user=PlayerService::findByTg(user   = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        if (data === 'ach:list') {this->achievements(msgId,msgId,msgId,user, $chatId); return true; }

        // claim جایزه مأموریت
        if (str_starts_with($data, 'mis:claim:')) {
            key=explode(′:′,key = explode(':',key=explode(′:′,data)[2];
            msg=MissionService::claim(msg = MissionService::claim(msg=MissionService::claim(user, $key);
            Telegram::sendMessage(chatId,chatId,chatId,msg ?? "⚠️ جایزه‌ای برای گرفتن نیست.");
            this−>list(this->list(this−>list(msgId, PlayerService::findByTg(chatId),chatId),chatId),chatId);
            return true;
        }
        if (data === 'mis:list') {this->list(msgId,msgId,msgId,user, $chatId); return true; }
        return true;
    }

    private function list(int msgId,arraymsgId, arraymsgId,arrayuser, int $chatId): void
    {
        $rows = [];
        $text = "🎯 <b>مأموریت‌ها</b>\n\n";
        foreach (MissionService::listFor(user)asuser) asuser)askey => $m) {
            meta=meta =meta=m['meta'];
            pct=min(100,(int)(pct = min(100, (int)(pct=min(100,(int)(m['progress'] / $meta['target'] * 100));
            status=status =status=m['claimed'] ? '✅' : ($m['done'] ? '🎁' : '⏳');
            text.="text .= "text.="status {meta['emoji']} <b>{meta['name']}</b>\n" .
                     "📊 {m['progress']} / {meta['target']}  ($pct٪)\n" .
                     "🎁 جایزه: " . number_format(meta[′rewardcash′])."meta['reward_cash']) . "meta[′rewardc​ash′])."" .
                     (meta['reward_xp'] ? " + {meta['reward_xp']} XP" : '') . "\n\n";
            if (m['done'] && !m['claimed']) {
                rows[] = [Telegram::btn("🎁 گرفتن جایزه: {meta['name']}", "mis:claim:$key")];
            }
        }
        $rows[] = [Telegram::btn('⬅️ بازگشت', 'menu:home')];
        Telegram::editMessage(chatId,chatId,chatId,msgId, text,Telegram::kb(text, Telegram::kb(text,Telegram::kb(rows));
    }

    private function achievements(int msgId,arraymsgId, arraymsgId,arrayuser, int $chatId): void
    {
        $st = \App\DB::pdo()->prepare("SELECT achievement_key FROM player_achievements WHERE user_id = ?");
        st−>execute([st->execute([st−>execute([user['id']]);
        owned=arraycolumn(owned = array_column(owned=arrayc​olumn(st->fetchAll(), 'achievement_key');

        $text = "🏆 <b>دستاوردها</b>\n\n";
        foreach (AchievementService::ALL as key=>key =>key=>a) {
            unlocked=inarray(unlocked = in_array(unlocked=ina​rray(key, $owned, true);
            text.=(text .= (text.=(unlocked ? "{a['emoji']}" : '🔒') . " {a['name']}" .
                     (unlocked ? " — +{a['reward']} دلار" : "\n") . "\n";
        }
        Telegram::editMessage(chatId,chatId,chatId,msgId, $text,
            Telegram::kb([[Telegram::btn('⬅️ بازگشت', 'menu:home')]]));
    }
}
