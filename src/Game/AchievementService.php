<?php
namespace App\Game;

use App\DB;

final class AchievementService
{
    public const ALL = [
        'first_10k'     => ['emoji' => '💰', 'name' => 'اولین ۱۰ هزار',      'reward' => 1000,   'cond' => ['worth' => 10000]],
        'first_business'=> ['emoji' => '🏢', 'name' => 'اولین کسب‌وکار',      'reward' => 2500,   'cond' => ['flag' => 'first_business']],
        'first_car'     => ['emoji' => '🚗', 'name' => 'اولین ماشین',        'reward' => 1000,   'cond' => ['flag' => 'first_car']],
        'homeowner'     => ['emoji' => '🏠', 'name' => 'صاحب‌خانه',          'reward' => 3000,   'cond' => ['flag' => 'homeowner']],
        'millionaire'   => ['emoji' => '👑', 'name' => 'میلیونر',            'reward' => 50000,  'cond' => ['worth' => 1000000]],
        'famous'        => ['emoji' => '🔥', 'name' => 'مشهور',              'reward' => 20000,  'cond' => ['fame' => 500]],
        'level10'       => ['emoji' => '⭐', 'name' => 'لول ۱۰',             'reward' => 15000,  'cond' => ['level' => 10]],
        'level20'       => ['emoji' => '🌟', 'name' => 'لول ۲۰',             'reward' => 60000,  'cond' => ['level' => 20]],
        'trusted'       => ['emoji' => '🤝', 'name' => 'شریک قابل اعتماد',   'reward' => 5000,   'cond' => ['transfers' => 5]],
        'big_spender'   => ['emoji' => '💎', 'name' => 'خریدار حرفه‌ای',     'reward' => 8000,   'cond' => ['flag' => 'big_spender']],
    ];

    /** بعد از هر تغییر مهم آمار صدا زده می‌شه. */
    public static function check(array user,?stringuser, ?stringuser,?stringflag = null): array
    {
        $unlocked = [];
        worth=worth =worth=user['cash'] + $user['bank'];

        $txCount = 0;
        if ($flag === 'transfer_sent') {
            $st = DB::pdo()->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ? AND type = 'transfer_out'");
            st−>execute([st->execute([st−>execute([user['id']]);
            txCount=(int)txCount = (int)txCount=(int)st->fetchColumn();
        }

        foreach (self::ALL as key=>key =>key=>a) {
            $st = DB::pdo()->prepare("SELECT 1 FROM player_achievements WHERE user_id = ? AND achievement_key = ?");
            st−>execute([st->execute([st−>execute([user['id'], $key]);
            if ($st->fetch()) continue;

            c=c =c=a['cond'];
            ok=match(arraykeyfirst(ok = match (array_key_first(ok=match(arrayk​eyf​irst(c)) {
                'worth' => worth>=worth >=worth>=c['worth'],
                'fame'  => user[′fame′]>=user['fame'] >=user[′fame′]>=c['fame'],
                'level' => user[′level′]>=user['level'] >=user[′level′]>=c['level'],
                'transfers' => txCount>=txCount >=txCount>=c['transfers'],
                'flag'  => flag===flag ===flag===c['flag'],
                default => false,
            };
            if ($ok) {
                DB::pdo()->prepare("INSERT INTO player_achievements (user_id, achievement_key, unlocked_at) VALUES (?,?,?)")
                    ->execute([user[′id′],user['id'],user[′id′],key, time()]);
                EconomyService::earn(user,user,user,a['reward'], 'جایزه دستاورد: ' . $a['name']);
                unlocked[]=unlocked[] =unlocked[]=a;
            }
        }
        return $unlocked;
    }
}
