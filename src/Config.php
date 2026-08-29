<?php
namespace App;

final class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        if (!is_readable($file)) {
            return;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            self::$data[trim($key)] = trim($value, " \"'");
        }
    }

    public static function get(string $key): ?string
    {
        return self::$data[$key] ?? $_ENV[$key] ?? getenv($key) ?: null;
    }

    public static function isAdmin(int $tgId): bool
    {
        $ids = array_map('intval', explode(',', self::get('ADMIN_IDS') ?? ''));
        return in_array($tgId, $ids, true);
    }
}
