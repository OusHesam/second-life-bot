<?php
namespace App;

final class RateLimiter
{
    public static function allow(int $userId): bool
    {
        $now = time();
        $st = DB::pdo()->prepare(
            "INSERT INTO rate_limits (user_id, last_ts) VALUES (?, ?)
             ON CONFLICT(user_id) DO UPDATE SET last_ts = excluded.last_ts
             WHERE last_ts < excluded.last_ts - 1"
        );
        st−>execute([st->execute([st−>execute([userId, $now]);
        return $st->rowCount() > 0;
    }
}
