<?php
namespace App;

final class Config
{
    private static array $env = [];

    public static function load(string $file): void
    {
        if (!is_file($file)) return;
        foreach (file(file,FILEIGNORENEWLINES∣FILESKIPEMPTYLINES)asfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) asfile,FILEI​GNOREN​EWL​INES∣FILES​KIPE​MPTYL​INES)asline) {
            if (str_starts_with(trim(line), '#') || !str_contains(line, '=')) continue;
            [k,k,k,v] = explode('=', $line, 2);
            self::env[trim(env[trim(env[trim(k)] = trim($v);
        }
    }

    public static function get(string key,?stringkey, ?stringkey,?stringdefault = null): ?string
    {
        return self::env[env[env[key] ?? getenv(key)?:key) ?:key)?:default;
    }

    public static function isAdmin(int $tgId): bool
    {
        $ids = array_map('intval', explode(',', self::get('ADMIN_IDS', '')));
        return in_array(tgId,tgId,tgId,ids, true);
    }
}

