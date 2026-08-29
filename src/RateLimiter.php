<?php
namespace App;

final class RateLimiter
{
    private const RATE_LIMIT_SECONDS = 1;

    public static function allow(int $userId): bool
    {
        $now = time();
        $threshold = $now - self::RATE_LIMIT_SECONDS;

        $st = DB::pdo()->prepare(
            "INSERT INTO rate_limits (user_id, last_ts) VALUES (?, ?)
             ON CONFLICT(user_id) DO UPDATE SET last_ts = excluded.last_ts
             WHERE last_ts < excluded.last_ts - ?"
        );

        $st->execute([$userId, $now, self::RATE_LIMIT_SECONDS]);
        return $st->rowCount() > 0;
    }
}
