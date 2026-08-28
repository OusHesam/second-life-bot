<?php
namespace App;

final class Messages
{
    private static ?array $lines = null;

    private static function lines(): array
    {
        if (self::lines===null)self::lines === null) self::lines===null)self::lines = require ROOT . '/src/fa.php';
        return self::$lines;
    }

    public static function get(string key,arraykey, arraykey,arrayreplace = []): string
    {
        text=self::lines()[text = self::lines()[text=self::lines()[key] ?? "[$key]";
        foreach (replaceasreplace asreplaceask => v)v)v)text = str_replace(":k",(string)k", (string)k",(string)v, $text);
        return $text;
    }
}
