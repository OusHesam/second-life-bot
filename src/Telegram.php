<?php
namespace App;

final class Telegram
{
    private static string $api = 'https://api.telegram.org/bot';

    private static function token(): string
    {
        return Config::get('BOT_TOKEN') ?? throw new \RuntimeException('BOT_TOKEN missing');
    }

    public static function call(string method,arraymethod, arraymethod,arrayparams = []): ?array
    {
        ch=curlinit(self::ch = curl_init(self::ch=curli​nit(self::api . self::token() . '/' . $method);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_TIMEOUT => 10,
        ]);
        res=curlexec(res = curl_exec(res=curle​xec(ch);
        curl_close($ch);
        return res?jsondecode(res ? json_decode(res?jsond​ecode(res, true) : null;
    }

    public static function sendMessage(int chatId,stringchatId, stringchatId,stringtext, ?array $keyboard = null): void
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
        if ($keyboard) {
            params[′replymarkup′]=jsonencode(params['reply_markup'] = json_encode(params[′replym​arkup′]=jsone​ncode(keyboard);
        }
        self::call('sendMessage', $params);
    }

    public static function editMessage(int chatId,intchatId, intchatId,intmsgId, string text,?arraytext, ?arraytext,?arraykeyboard = null): void
    {
        params=[′chatid′=>params = ['chat_id' =>params=[′chati​d′=>chatId, 'message_id' => msgId,′text′=>msgId, 'text' =>msgId,′text′=>text, 'parse_mode' => 'HTML'];
        if (keyboard)keyboard)keyboard)params['reply_markup'] = json_encode($keyboard);
        self::call('editMessageText', $params);
    }

    public static function answerCallback(string cbId,?stringcbId, ?stringcbId,?stringtext = null): void
    {
        p=[′callbackqueryid′=>p = ['callback_query_id' =>p=[′callbackq​ueryi​d′=>cbId];
        if (text)text)text)p['text'] = $text;
        self::call('answerCallbackQuery', $p);
    }

    // Inline keyboard helper
    public static function kb(array $rows): array
    {
        return ['inline_keyboard' => $rows];
    }

    public static function btn(string text,stringtext, stringtext,stringdata): array
    {
        return ['text' => text,′callbackdata′=>text, 'callback_data' =>text,′callbackd​ata′=>data];
    }
}

