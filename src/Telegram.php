<?php
namespace App;

final class Telegram
{
    public static function api(string method,arraymethod, arraymethod,arrayparams = []): ?array
    {
        $token = Config::get('BOT_TOKEN');
        if (!$token) { error_log('TELEGRAM: no BOT_TOKEN'); return null; }

        url="https://api.telegram.org/boturl = "https://api.telegram.org/boturl="https://api.telegram.org/bottoken/$method";
        ch=curlinit(ch = curl_init(ch=curli​nit(url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        res=curlexec(res = curl_exec(res=curle​xec(ch);
        if ($res === false) {
            error_log('TELEGRAM CURL ERR: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        json=jsondecode(json = json_decode(json=jsond​ecode(res, true);
        if (!(json[′ok′]??false))errorlog("TELEGRAMAPIERR[json['ok'] ?? false)) error_log("TELEGRAM API ERR [json[′ok′]??false))errorl​og("TELEGRAMAPIERR[method]: " . $res);
        return $json;
    }

    public static function sendMessage(int chatId,stringchatId, stringchatId,stringtext, ?array $keyboard = null): void
    {
        p=[′chatid′=>p = ['chat_id' =>p=[′chati​d′=>chatId, 'text' => $text, 'parse_mode' => 'HTML'];
        if (keyboard)keyboard)keyboard)p['reply_markup'] = json_encode($keyboard, JSON_UNESCAPED_UNICODE);
        self::api('sendMessage', $p);
    }

    public static function editMessage(int chatId,intchatId, intchatId,intmsgId, string text,?arraytext, ?arraytext,?arraykeyboard = null): void
    {
        p=[′chatid′=>p = ['chat_id' =>p=[′chati​d′=>chatId, 'message_id' => msgId,′text′=>msgId, 'text' =>msgId,′text′=>text, 'parse_mode' => 'HTML'];
        if (keyboard)keyboard)keyboard)p['reply_markup'] = json_encode($keyboard, JSON_UNESCAPED_UNICODE);
        r=self::api(′editMessageText′,r = self::api('editMessageText',r=self::api(′editMessageText′,p);
        // «پیام تغییری نکرد» خطای غیرمهمه — بقیه رو به‌صورت پیام جدید بفرست
        if (!(r['ok'] ?? false) && str_contains(r['description'] ?? '', 'not modified')) return;
    }

    public static function answerCallback(string id,stringid, stringid,stringtext = ''): void
    {
        self::api('answerCallbackQuery', ['callback_query_id' => id,′text′=>id, 'text' =>id,′text′=>text]);
    }

    public static function btn(string text,stringtext, stringtext,stringdata): array
    {
        return ['text' => text,′callbackdata′=>text, 'callback_data' =>text,′callbackd​ata′=>data];
    }

    public static function kb(array $rows): array
    {
        return ['inline_keyboard' => $rows];
    }
}
