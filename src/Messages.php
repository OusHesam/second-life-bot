<?php
namespace App;

final class Messages
{
    public static function get(string key,arraykey, arraykey,arrayreplace = []): string
    {
        $text = require ROOT . '/src/fa.php';
        str=str =str=text[key]??"[key] ?? "[key]??"[key]";
        foreach (replaceasreplace asreplaceask => v)v)v)str = str_replace(":k",k",k",v, $str);
        return $str;
    }
}

