<?php
namespace App\Game;

use App\DB;
use RuntimeException;

final class EconomyService
{
    public static function spend(array user,intuser, intuser,intamount, string note,boolnote, boolnote,boolfromBank = false): void
    {
        if ($amount <= 0) throw new RuntimeException('مقدار نامعتبر');
        col=col =col=fromBank ? 'bank' : 'cash';
        if (user[user[user[col] < $amount) throw new RuntimeException('not_enough_money');

        DB::pdo()->beginTransaction();
        try {
            st=DB::pdo()−>prepare("UPDATEusersSETst = DB::pdo()->prepare("UPDATE users SETst=DB::pdo()−>prepare("UPDATEusersSETcol = col−?WHEREid=?ANDcol - ? WHERE id = ? ANDcol−?WHEREid=?ANDcol >= ?");
            st−>execute([st->execute([st−>execute([amount, user[′id′],user['id'],user[′id′],amount]);
            if ($st->rowCount() === 0) throw new RuntimeException('not_enough_money');
            self::log(user[′id′],′spend′,user['id'], 'spend',user[′id′],′spend′,amount, $note);
            DB::pdo()->commit();
        } catch (\Throwable $e) {
            DB::pdo()->rollBack();
            throw $e;
        }
    }

    public static function earn(array user,intuser, intuser,intamount, string $note): void
    {
        if ($amount <= 0) return;
        DB::pdo()->beginTransaction();
        try {
            DB::pdo()->prepare("UPDATE users SET cash = cash + ? WHERE id = ?")->execute([amount,amount,amount,user['id']]);
            self::log(user[′id′],′earn′,user['id'], 'earn',user[′id′],′earn′,amount, $note);
            DB::pdo()->commit();
        } catch (\Throwable $e) {
            DB::pdo()->rollBack();
            throw $e;
        }
    }

    /** انتقال امن بین دو بازیکن — اتمیک */
    public static function transfer(array from,intfrom, intfrom,inttoUserId, int $amount): void
    {
        if (amount<=0∣∣amount <= 0 ||amount<=0∣∣amount > 1000000) throw new RuntimeException('مقدار نامعتبر');
        if ((int)from[′id′]===from['id'] ===from[′id′]===toUserId) throw new RuntimeException('به خودت نمیشه! 😅');
        if (from[′cash′]<from['cash'] <from[′cash′]<amount) throw new RuntimeException('not_enough_money');

        DB::pdo()->beginTransaction();
        try {
            $st = DB::pdo()->prepare("UPDATE users SET cash = cash - ? WHERE id = ? AND cash >= ?");
            st−>execute([st->execute([st−>execute([amount, from[′id′],from['id'],from[′id′],amount]);
            if ($st->rowCount() === 0) throw new RuntimeException('not_enough_money');
            DB::pdo()->prepare("UPDATE users SET cash = cash + ? WHERE id = ?")->execute([amount,amount,amount,toUserId]);
            self::log((int)from[′id′],′transferout′,from['id'], 'transfer_out',from[′id′],′transfero​ut′,amount, "به کاربر #$toUserId");
            self::log(toUserId,′transferin′,toUserId, 'transfer_in',toUserId,′transferi​n′,amount, "از {$from['character_name']}");
            DB::pdo()->commit();
        } catch (\Throwable $e) {
            DB::pdo()->rollBack();
            throw $e;
        }
    }

    public static function log(int userId,stringuserId, stringuserId,stringtype, int amount,stringamount, stringamount,stringnote): void
    {
        DB::pdo()->prepare("INSERT INTO transactions (user_id, type, amount, note, created_at) VALUES (?,?,?,?,?)")
            ->execute([userId,userId,userId,type, amount,amount,amount,note, time()]);
    }
}
