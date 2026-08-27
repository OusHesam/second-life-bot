<?php
namespace App\Game;

use App\DB;

final class MissionService
{
    /** مأموریت‌های همیشگی — با hook در EconomyService پیشرفت می‌کنن. */
    public const MISSIONS = [
        'earn_10k'    => ['emoji' => '🎯', 'name' => 'اولین ۱۰,۰۰۰ دلار',   'target' => 10000,   'metric' => 'earned_total', 'reward_cash' => 2000, 'reward_xp' => 500,  'min_level' => 1],
        'work_5'      => ['emoji' => '🔧', 'name' => '۵ بار سر کار برو',    'target' => 5,       'metric' => 'work_count',   'reward_cash' => 500,  'reward_xp' => 200,  'min_level' => 1],
        'buy_item'    => ['emoji' => '🎒', 'name' => 'اولین خرید از فروشگاه','target' => 1,      'metric' => 'item_buys',    'reward_cash' => 300,  'reward_xp' => 150,  'min_level' => 1],
        'transfer_1'  => ['emoji' => '💸', 'name' => 'اولین انتقال پول',    'target' => 1,       'metric' => 'transfers',    'reward_cash' => 300,  'reward_xp' => 150,  'min_level' => 1],
        'biz_upgrade' => ['emoji' => '📈', 'name' => 'کسب‌وکار رو ارتقا بده','target' => 1,      'metric' => 'biz_upgrades', 'reward_cash' => 4000, 'reward_xp' => 800,  'min_level' => 3],
        'level_5'     => ['emoji' => '⭐', 'name' => 'به لول ۵ برس',        'target' => 5,       'metric' => 'level',        'reward_cash' => 3000, 'reward_xp' => 0,    'min_level' => 1],
    ];

    public static function listFor(array $user): array
    {
        $st = DB::pdo()->prepare("SELECT * FROM player_missions WHERE user_id = ?");
        st−>execute([st->execute([st−>execute([user['id']]);
        $rows = [];
        foreach (st−>fetchAll()asst->fetchAll() asst−>fetchAll()asr) rows[rows[rows[r['mission_key']] = $r;

        $out = [];
        foreach (self::MISSIONS as key=>key =>key=>m) {
            if (user[′level′]<user['level'] <user[′level′]<m['min_level']) continue;
            r=r =r=rows[$key];
            out[out[out[key] = [
                'meta' => $m,
                'progress' => $r['progress'] ?? 0,
                'done' => (int)($r['done'] ?? 0),
                'claimed' => (int)($r['claimed'] ?? 0),
            ];
        }
        return $out;
    }

    /** Hook: بعد از اکشن‌ها صدا زده می‌شه. */
    public static function progress(array user,stringuser, stringuser,stringmetric, int $amount): array
    {
        $msgs = [];
        foreach (self::MISSIONS as key=>key =>key=>m) {
            if (m[′metric′]!==m['metric'] !==m[′metric′]!==metric) continue;
            $st = DB::pdo()->prepare("SELECT * FROM player_missions WHERE user_id = ? AND mission_key = ?");
            st−>execute([st->execute([st−>execute([user['id'], $key]);
            row=row =row=st->fetch();

            if (metric===′level′∣∣metric === 'level' ||metric===′level′∣∣metric === 'earned_total') {
                newProgress=newProgress =newProgress=amount;      // مقدار مطلق
            } else {
                newProgress=(newProgress = (newProgress=(row['progress'] ?? 0) + $amount;
            }
            if (row &&row['done']) continue;

            done=done =done=newProgress >= $m['target'] ? 1 : 0;
            if ($row) {
                DB::pdo()->prepare("UPDATE player_missions SET progress = ?, done = ? WHERE user_id = ? AND mission_key = ?")
                    ->execute([newProgress,newProgress,newProgress,done, user[′id′],user['id'],user[′id′],key]);
            } else {
                DB::pdo()->prepare("INSERT INTO player_missions (user_id, mission_key, progress, done, assigned_at) VALUES (?,?,?,?,?)")
                    ->execute([user[′id′],user['id'],user[′id′],key, newProgress,newProgress,newProgress,done, time()]);
            }
            if (done && !row['done']) {
                msgs[] = "✅ مأموریت انجام شد: <b>{m['name']}</b>\n🎁 جایزه‌ات رو از منوی مأموریت‌ها بردار!";
            }
        }
        return $msgs;
    }

    public static function claim(array user,stringuser, stringuser,stringkey): ?string
    {
        m=self::MISSIONS[m = self::MISSIONS[m=self::MISSIONS[key] ?? null;
        if (!$m) return null;
        $st = DB::pdo()->prepare("SELECT * FROM player_missions WHERE user_id = ? AND mission_key = ?");
        st−>execute([st->execute([st−>execute([user['id'], $key]);
        row=row =row=st->fetch();
        if (!row∣∣!row || !row∣∣!row['done'] || $row['claimed']) return null;

        DB::pdo()->prepare("UPDATE player_missions SET claimed = 1 WHERE user_id = ? AND mission_key = ?")
            ->execute([user[′id′],user['id'],user[′id′],key]);
        if (m[′rewardcash′]>0)EconomyService::earn(m['reward_cash'] > 0) EconomyService::earn(m[′rewardc​ash′]>0)EconomyService::earn(user, $m['reward_cash'], 'جایزه مأموریت');
        $xpMsg = '';
        if (m[′rewardxp′]>0)m['reward_xp'] > 0)m[′rewardx​p′]>0)xpMsg = PlayerService::addXp(user,user,user,m['reward_xp']);
        return "🎁 <b>{m['name']}</b>\n💰 +" . number_format(m['reward_cash']) . " دلار" .
               (m['reward_xp'] > 0 ? "\n⭐ +{m['reward_xp']} XP" : '') . $xpMsg;
    }
}
