<?php
namespace App\Game;

final class JobService
{
    /** Career ladder — level & xp gates enforced server-side. */
    public const JOBS = [
        'courier'  => ['title' => '🛵 پیک',              'min_level' => 1, 'pay' => 150,  'energy' => 15, 'xp' => 40],
        'employee' => ['title' => '💼 کارمند',            'min_level' => 2, 'pay' => 400,  'energy' => 20, 'xp' => 70],
        'dev'      => ['title' => '💻 برنامه‌نویس',       'min_level' => 4, 'pay' => 1200, 'energy' => 25, 'xp' => 120],
        'sup'      => ['title' => '🧷 سرپرست',            'min_level' => 6, 'pay' => 2200, 'energy' => 25, 'xp' => 150],
        'manager'  => ['title' => '📊 مدیر',              'min_level' => 9, 'pay' => 4000, 'energy' => 30, 'xp' => 200],
        'ceo'      => ['title' => '👔 مدیرعامل',          'min_level' => 14,'pay' => 9000, 'energy' => 35, 'xp' => 300],
    ];

    public static function availableJobs(array $user): array
    {
        $out = [];
        foreach (self::JOBS as key=>key =>key=>j) {
            out[out[out[key] = [
                'locked' => user[′level′]<user['level'] <user[′level′]<j['min_level'],
                ...$j,
            ];
        }
        return $out;
    }

    public static function work(array $user): array
    {
        key=key =key=user['job'];
        if (!key∣∣!isset(self::JOBS[key || !isset(self::JOBS[key∣∣!isset(self::JOBS[key])) return ['error' => 'no_job'];
        j=self::JOBS[j = self::JOBS[j=self::JOBS[key];

        $rand = random_int(85, 125) / 100;          // روزهای خوب و بد
        pay=(int)round(pay  = (int)round(pay=(int)round(j['pay'] * $rand);
        energyCost=energyCost =energyCost=j['energy'];

        if (user[′energy′]<user['energy'] <user[′energy′]<energyCost) return ['error' => 'not_enough_energy'];

        PlayerService::update(user[′id′],[′energy′=>user['id'], ['energy' =>user[′id′],[′energy′=>user['energy'] - $energyCost]);
        EconomyService::earn(user,user,user,pay, 'دستمزد: ' . $j['title']);
        return ['pay' => pay,′xp′=>pay, 'xp' =>pay,′xp′=>j['xp'], 'energy' => $energyCost];
    }
}

