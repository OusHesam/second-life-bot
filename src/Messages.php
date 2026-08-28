<?php
namespace App;

final class Messages
{
    private static ?array $lines = null;

    private static function lines(): array
    {
        if (self::$lines === null) {
            self::$lines = require ROOT . '/src/fa.php';
        }

        return self::$lines;
    }

    public static function get(string $key, array $replace = []): string
    {
        $text = self::lines()[$key] ?? "[{$key}]";

        foreach ($replace as $k => $v) {
            $text = str_replace(":{$k}", (string)$v, $text);
        }

        return $text;
    }
}
