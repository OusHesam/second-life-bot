<?php
namespace App\Game;

use App\DB;
use App\Messages;
use App\Telegram;

final class PlayerService
{
    public const BACKGROUNDS = [
        'normal'   => ['emoji' => '🟢', 'label' => 'خانواده معمولی',  'cash' => 500,   'xp_mult' => 1.0],
        'poor'     => ['emoji' => '🔴', 'label' => 'خانواده فقیر',    'cash' => 100,   'xp_mult' => 1.3], // سرشتی + رشد سریع‌تر
        'rich'     => ['emoji' => '🟡', 'label' => 'خانواده ثروتمند', 'cash' => 5000,  'xp_mult' => 0.8], // انتظارات بالا
        'mystery'  => ['emoji' => '🟣', 'label' => 'گذشته مرموز',     'cash' => 750,   'xp_mult' => 1.1],
        'selfmade' => ['emoji' => '⚫', 'label' => 'خودساخته',        'cash' => 50,    'xp_mult' => 1.5],
    ];

    public const XP_PER_LEVEL = 1000;

    public static function findByTg(int $tgId): ?array
    {
        $st = DB::pdo()->prepare("SELECT * FROM users WHERE telegram_id = ?");
        st−>execute([st->execute([st−>execute([tgId]);
        return $st->fetch() ?: null;
    }

    public static function create(int tgId,?stringtgId, ?stringtgId,?stringusername, string name,stringname, stringname,stringnick, string $bg): array
    {
        b=self::BACKGROUNDS[b = self::BACKGROUNDS[b=self::BACKGROUNDS[bg];
        $code = 'SL-' . random_int(100000, 999999);
        $now = time();

        DB::pdo()->beginTransaction();
        try {
            $st = DB::pdo()->prepare(
                "INSERT INTO users (telegram_id, username, player_code, character_name, nickname,
                 background, cash, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)"
            );
            st−>execute([st->execute([st−>execute([tgId, username,username,username,code, name,name,name,nick, bg,bg,bg,b['cash'], now,now,now,now]);
            $uid = (int)DB::pdo()->lastInsertId();
            EconomyService::log(uid,′admin′,uid, 'admin',uid,′admin′,b['cash'], 'سرمایه اولیه');
            DB::pdo()->commit();
        } catch (\Throwable $e) {
            DB::pdo()->rollBack();
            throw $e;
        }
        return self::findByTg($tgId);
    }

    public static function update(int userId,arrayuserId, arrayuserId,arrayfields): void
    {
        set=implode(′,′,arraymap(fn(set = implode(', ', array_map(fn(set=implode(′,′,arraym​ap(fn(k) => "k=?",arraykeys(k = ?", array_keys(k=?",arrayk​eys(fields)));
        vals=arrayvalues(vals = array_values(vals=arrayv​alues(fields);
        $vals[] = time();
        vals[]=vals[] =vals[]=userId;
        DB::pdo()->prepare("UPDATE users SET set,updatedat=?WHEREid=?")−>execute(set, updated_at = ? WHERE id = ?")->execute(set,updateda​t=?WHEREid=?")−>execute(vals);
    }

    public static function addXp(array user,intuser, intuser,intxp): string
    {
        mult=self::BACKGROUNDS[mult = self::BACKGROUNDS[mult=self::BACKGROUNDS[user['background']]['xp_mult'];
        xp=(int)round(xp = (int)round(xp=(int)round(xp * $mult);
        newXp=newXp =newXp=user['xp'] + $xp;
        $levelUp = '';
        if (newXp>=self::XPPERLEVEL∗newXp >= self::XP_PER_LEVEL *newXp>=self::XPP​ERL​EVEL∗user['level']) {
            newXp−=self::XPPERLEVEL∗newXp -= self::XP_PER_LEVEL *newXp−=self::XPP​ERL​EVEL∗user['level'];
            self::update(user[′id′],[′xp′=>user['id'], ['xp' =>user[′id′],[′xp′=>newXp, 'level' => $user['level'] + 1]);
            levelUp="\n\n".Messages::get(′levelup′,[′level′=>levelUp = "\n\n" . Messages::get('level_up', ['level' =>levelUp="\n\n".Messages::get(′levelu​p′,[′level′=>user['level'] + 1]);
        } else {
            self::update(user[′id′],[′xp′=>user['id'], ['xp' =>user[′id′],[′xp′=>newXp]);
        }
        return $levelUp;
    }

    public static function renderProfile(array $u): string
    {
        job=job =job=u['job'] ?: 'بی‌کار';
        totalWorth=totalWorth =totalWorth=u['cash'] + $u['bank'];
        return
        "━━━━━━━━━━━━━━\n" .
        "👤 <b>{u['character_name']}</b> ({u['nickname']})\n\n" .
        "🆔 شناسه: <code>{$u['player_code']}</code>\n" .
        "⭐ لول: {u['level']}  |  ⭐ XP: {u['xp']}\n\n" .
        "💵 پول نقد: " . number_format($u['cash']) . " دلار\n" .
        "🏦 بانک: " . number_format($u['bank']) . " دلار\n" .
        "📊 ارزش کل دارایی: " . number_format($totalWorth) . " دلار\n\n" .
        "🔥 شهرت: {u['fame']}\n👑 قدرت: {u['power']}\n🌍 نفوذ: {$u['influence']}\n\n" .
        "⚡ انرژی: {u['energy']}/100\n❤️ سلامتی: {u['health']}/100\n😊 رضایت: {$u['happiness']}/100\n\n" .
        "💼 شغل: $job\n" .
        "📍 شهر: دارک سیتی\n" .
        "━━━━━━━━━━━━━━";
    }
}

