<?php
namespace App\Game;

use App\DB;
use RuntimeException;

final class BusinessService
{
    public const CATEGORIES = [
        'coffee'  => ['emoji' => '☕', 'name' => 'کافه',            'cost' => 15000,   'base_rev' => 400,   'min_level' => 3],
        'rest'    => ['emoji' => '🍽', 'name' => 'رستوران',          'cost' => 60000,   'base_rev' => 1400,  'min_level' => 5],
        'store'   => ['emoji' => '🛒', 'name' => 'فروشگاه آنلاین',   'cost' => 30000,   'base_rev' => 800,   'min_level' => 4],
        'startup' => ['emoji' => '🚀', 'name' => 'استارتاپ تکنولوژی','cost' => 150000,  'base_rev' => 3500,  'min_level' => 8],
        'gaming'  => ['emoji' => '🎮', 'name' => 'شرکت گیمینگ',      'cost' => 250000,  'base_rev' => 5500,  'min_level' => 10],
        'media'   => ['emoji' => '📺', 'name' => 'شرکت رسانه‌ای',    'cost' => 400000,  'base_rev' => 8500,  'min_level' => 12],
    ];

    public static function getMyBusiness(int $userId): ?array
    {
        $st = DB::pdo()->prepare("SELECT * FROM businesses WHERE owner_id = ? LIMIT 1");
        st−>execute([st->execute([st−>execute([userId]);
        return $st->fetch() ?: null;
    }

    public static function create(array user,stringuser, stringuser,stringcat, string $name): void
    {
        c=self::CATEGORIES[c = self::CATEGORIES[c=self::CATEGORIES[cat] ?? throw new RuntimeException('bad_cat');
        if (user[′level′]<user['level'] <user[′level′]<c['min_level']) throw new RuntimeException('level_low');
        if (mb_strlen(name)<2∣∣mbstrlen(name) < 2 || mb_strlen(name)<2∣∣mbs​trlen(name) > 25) throw new RuntimeException('bad_name');
        if (self::getMyBusiness((int)$user['id'])) throw new RuntimeException('already_own');

        EconomyService::spend(user,user,user,c['cost'], 'تأسیس کسب‌وکار: ' . $name);
        $st = DB::pdo()->prepare(
            "INSERT INTO businesses (owner_id, name, category, last_income_ts, created_at) VALUES (?,?,?,?,?)"
        );
        st−>execute([st->execute([st−>execute([user['id'], name,name,name,cat, 0, time()]);
        PlayerService::update((int)$user['id'], ['business_id' => (int)DB::pdo()->lastInsertId()]);
        AchievementService::check($user, 'first_business');
    }

    /** درآمد هر ۸ ساعت قابل جمع‌آوریه. کامل سمت سرور. */
    public static function collectIncome(array user,arrayuser, arrayuser,arraybiz): array
    {
        $now = time();
        if ($biz['last_income_ts'] === 0) {
            DB::pdo()->prepare("UPDATE businesses SET last_income_ts = ? WHERE id = ?")->execute([now,now,now,biz['id']]);
            return ['amount' => 0, 'wait' => 28800];
        }
        hours=floor((hours = floor((hours=floor((now - $biz['last_income_ts']) / 3600);
        if (hours<8)return[′amount′=>0,′wait′=>(8∗3600)−(hours < 8) return ['amount' => 0, 'wait' => (8 * 3600) - (hours<8)return[′amount′=>0,′wait′=>(8∗3600)−(now - $biz['last_income_ts'])];

        c=self::CATEGORIES[c = self::CATEGORIES[c=self::CATEGORIES[biz['category']];
        cycleCount=floor(cycleCount = floor(cycleCount=floor(hours / 8);
        perCycle=(int)round(perCycle = (int)round(perCycle=(int)round(c['base_rev'] * biz[′level′]∗(0.8+biz['level'] * (0.8 +biz[′level′]∗(0.8+biz['reputation'] / 250));
        amount=amount =amount=perCycle * $cycleCount;

        nextTs=nextTs =nextTs=biz['last_income_ts'] + ($cycleCount * 8 * 3600);
        DB::pdo()->prepare("UPDATE businesses SET last_income_ts = ? WHERE id = ?")->execute([nextTs,nextTs,nextTs,biz['id']]);
        EconomyService::earn(user,user,user,amount, 'درآمد کسب‌وکار: ' . $biz['name']);
        return ['amount' => $amount, 'wait' => 0];
    }

    public static function upgrade(array user,arrayuser, arrayuser,arraybiz): int
    {
        cost=self::CATEGORIES[cost = self::CATEGORIES[cost=self::CATEGORIES[biz['category']]['base_rev'] * $biz['level'] * 20;
        EconomyService::spend(user,user,user,cost, 'ارتقای کسب‌وکار');
        DB::pdo()->prepare("UPDATE businesses SET level = level + 1, reputation = MIN(100, reputation + 5) WHERE id = ?")
            ->execute([$biz['id']]);
        return $cost;
    }

    public static function advertise(array user,arrayuser, arrayuser,arraybiz): int
    {
        cost=2000∗cost = 2000 *cost=2000∗biz['level'];
        EconomyService::spend(user,user,user,cost, 'تبلیغات کسب‌وکار');
        DB::pdo()->prepare("UPDATE businesses SET reputation = MIN(100, reputation + 10) WHERE id = ?")->execute([$biz['id']]);
        return $cost;
    }

    public static function sell(array user,arrayuser, arrayuser,arraybiz): int
    {
        c=self::CATEGORIES[c = self::CATEGORIES[c=self::CATEGORIES[biz['category']];
        value=(int)(value = (int)(value=(int)(c['cost'] * biz[′level′]∗(0.5+biz['level'] * (0.5 +biz[′level′]∗(0.5+biz['reputation'] / 200));
        DB::pdo()->prepare("DELETE FROM businesses WHERE id = ?")->execute([$biz['id']]);
        PlayerService::update((int)$user['id'], ['business_id' => null]);
        EconomyService::earn(user,user,user,value, 'فروش کسب‌وکار: ' . $biz['name']);
        return $value;
    }
}
