<?php
namespace App;

use PDO;

final class DB
{
    private static ?PDO $pdo = null;

    public static function init(): void
    {
        $path = ROOT . '/' . Config::get('DB_PATH', 'data/secondlife.sqlite');
        @mkdir(dirname($path), 0775, true);
        self::pdo=newPDO(′sqlite:′.pdo = new PDO('sqlite:' .pdo=newPDO(′sqlite:′.path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        self::$pdo->exec('PRAGMA journal_mode = WAL');
        self::$pdo->exec('PRAGMA foreign_keys = ON');
    }

    public static function pdo(): PDO { return self::$pdo ?? throw new \RuntimeException('DB not init'); }
    public static function close(): void { self::$pdo = null; }

    public static function migrate(): void
{
    $pdo = self::pdo();
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        telegram_id INTEGER UNIQUE NOT NULL,
        username TEXT,
        player_code TEXT UNIQUE NOT NULL,
        character_name TEXT NOT NULL,
        nickname TEXT,
        background TEXT NOT NULL,
        level INTEGER NOT NULL DEFAULT 1,
        xp INTEGER NOT NULL DEFAULT 0,
        cash INTEGER NOT NULL DEFAULT 0,
        bank INTEGER NOT NULL DEFAULT 0,
        energy INTEGER NOT NULL DEFAULT 100,
        health INTEGER NOT NULL DEFAULT 100,
        happiness INTEGER NOT NULL DEFAULT 70,
        fame INTEGER NOT NULL DEFAULT 0,
        power INTEGER NOT NULL DEFAULT 0,
        influence INTEGER NOT NULL DEFAULT 0,
        city TEXT NOT NULL DEFAULT 'dark_city',
        job TEXT,
        business_id INTEGER,
        property TEXT,
        vehicle TEXT,
        banned INTEGER NOT NULL DEFAULT 0,
        notif_enabled INTEGER NOT NULL DEFAULT 1,
        state TEXT,
        last_energy_ts INTEGER NOT NULL DEFAULT 0,
        created_at INTEGER NOT NULL,
        updated_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id),
        type TEXT NOT NULL,
        amount INTEGER NOT NULL,
        note TEXT,
        created_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS businesses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        owner_id INTEGER NOT NULL REFERENCES users(id),
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        level INTEGER NOT NULL DEFAULT 1,
        reputation INTEGER NOT NULL DEFAULT 50,
        employees INTEGER NOT NULL DEFAULT 0,
        last_income_ts INTEGER NOT NULL DEFAULT 0,
        created_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS player_achievements (
        user_id INTEGER NOT NULL REFERENCES users(id),
        achievement_key TEXT NOT NULL,
        unlocked_at INTEGER NOT NULL,
        PRIMARY KEY (user_id, achievement_key)
    );
    CREATE TABLE IF NOT EXISTS pending_events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id),
        event_key TEXT NOT NULL,
        data TEXT NOT NULL,
        expires_at INTEGER NOT NULL,
        created_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS admin_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        admin_id INTEGER NOT NULL,
        action TEXT NOT NULL,
        target INTEGER,
        detail TEXT,
        created_at INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS rate_limits (
        user_id INTEGER PRIMARY KEY,
        last_ts INTEGER NOT NULL
    );
    CREATE TABLE IF NOT EXISTS player_items (
        user_id INTEGER NOT NULL REFERENCES users(id),
        item_key TEXT NOT NULL,
        qty INTEGER NOT NULL DEFAULT 1,
        PRIMARY KEY (user_id, item_key)
    );
    CREATE TABLE IF NOT EXISTS player_missions (
        user_id INTEGER NOT NULL REFERENCES users(id),
        mission_key TEXT NOT NULL,
        progress INTEGER NOT NULL DEFAULT 0,
        done INTEGER NOT NULL DEFAULT 0,
        claimed INTEGER NOT NULL DEFAULT 0,
        assigned_at INTEGER NOT NULL,
        PRIMARY KEY (user_id, mission_key)
    );
    CREATE INDEX IF NOT EXISTS idx_tx_user ON transactions(user_id);
    CREATE INDEX IF NOT EXISTS idx_events_user ON pending_events(user_id);
    ");
}


