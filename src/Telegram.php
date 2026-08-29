<?php
namespace App;

final class Telegram
{
    public static function api(string $method, array $params = []): ?array
    {
        $token = Config::get('BOT_TOKEN');
        if (!$token) {
            error_log('TELEGRAM: no BOT_TOKEN');
            return null;
        }

        $url = "https://api.telegram.org/bot{$token}/{$method}";
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $res = curl_exec($ch);
        if ($res === false) {
            error_log('TELEGRAM CURL ERR: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        $json = json_decode($res, true);

        if (!($json['ok'] ?? false)) {
            error_log("TELEGRAM API ERR [{$method}]: {$res}");
        }

        return $json;
    }

    public static function sendMessage(int $chatId, string $text, ?array $keyboard = null): void
    {
        $p = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $p['reply_markup'] = json_encode($keyboard, JSON_UNESCAPED_UNICODE);
        }

        self::api('sendMessage', $p);
    }

    public static function editMessage(int $chatId, int $msgId, string $text, ?array $keyboard = null): void
    {
        $p = [
            'chat_id' => $chatId,
            'message_id' => $msgId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $p['reply_markup'] = json_encode($keyboard, JSON_UNESCAPED_UNICODE);
        }

        $r = self::api('editMessageText', $p);

        // "پیام تغییری نکرد" خطای غیرمهمه — بقیه رو به‌صورت پیام جدید بفرست
        if (!(($r['ok'] ?? false)) && str_contains($r['description'] ?? '', 'not modified')) {
            return;
        }
    }

    public static function answerCallback(string $id, string $text = ''): void
    {
        self::api('answerCallbackQuery', [
            'callback_query_id' => $id,
            'text' => $text,
        ]);
    }

    public static function btn(string $text, string $data): array
    {
        return [
            'text' => $text,
            'callback_data' => $data,
        ];
    }

    public static function kb(array $rows): array
    {
        return ['inline_keyboard' => $rows];
    }
}
