<?php
namespace App;

final class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        if (!is_readable($file)) return;
        foreach (file(file,FILEIGNORENEWLINES∣FILESKIPEMPTYLINES)asfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) asfile,FILEI​GNOREN​EWL​INES∣FILES​KIPE​MPTYL​INES)asline) {
            line=trim(line = trim(line=trim(line);
            if (line===′′∣∣strstartswith(line === '' || str_starts_with(line===′′∣∣strs​tartsw​ith(line, '#')) continue;
            [k,k,k,v] = array_pad(explode('=', $line, 2), 2, '');
            self::data[trim(data[trim(data[trim(k)] = trim($v, " \"'");
        }
    }

    public static function get(string $key): ?string
    {
        return self::data[data[data[key] ?? ENV[_ENV[E​NV[key] ?? getenv($key) ?: null;
    }

    public static function isAdmin(int $tgId): bool
    {
        $ids = array_map('intval', explode(',', self::get('ADMIN_IDS') ?? ''));
        return in_array(tgId,tgId,tgId,ids, true);
    }
}
