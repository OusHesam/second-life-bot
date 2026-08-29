<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{PlayerService, EconomyService};
use App\Handler;

final class StartHandler implements Handler
{
    public function handle(array $update): bool
    {
        $msg = $update['message'] ?? null;
        if (!$msg) {
            return false;
        }

        $from = $msg['from'];
        $text = trim($msg['text'] ?? '');
        $user = PlayerService::findByTg($from['id']);

        if ($text === '/start') {
            if ($user && $user['player_code'] !== 'PENDING') {
                Telegram::sendMessage((int)$from['id'], Messages::get('main_menu'), self::mainKb());
                return true;
            }

            if (!$user) {
                DB::pdo()->prepare(
                    "INSERT OR IGNORE INTO users (telegram_id, username, player_code, character_name, background, cash, created_at, updated_at, state) 
                    VALUES (?, ?, 'PENDING', '', 'none', 0, ?, ?, ?)"
                )->execute([$from['id'], $from['username'] ?? null, time(), time(), json_encode(['step' => 'name'])]);
            }

            Telegram::sendMessage((int)$from['id'], Messages::get('welcome'));
            Telegram::sendMessage((int)$from['id'], Messages::get('ask_name'));
            return true;
        }

        // مراحل ورودی متنی (ساخت شخصیت و نام کسب‌وکار)
        if (!$user) {
            return false;
        }

        $state = json_decode($user['state'] ?? '{}', true) ?: [];
        $input = mb_substr(trim($text), 0, 25);

        if (($state['step'] ?? '') === 'biz_name') {
            if (mb_strlen($input) < 2) {
                Telegram::sendMessage((int)$from['id'], '❌ اسم کسب‌وکار باید حداقل ۲ حرف باشد.');
                return true;
            }
            try {
                \App\Game\BusinessService::create($user, (string)($state['cat'] ?? ''), $input);
                DB::pdo()->prepare('UPDATE users SET state=NULL, updated_at=? WHERE id=?')->execute([time(), $user['id']]);
                Telegram::sendMessage((int)$from['id'], '🎉 کسب‌وکارت با موفقیت تأسیس شد!');
            } catch (\Throwable $e) {
                $msg = $e->getMessage() === 'not_enough_money' ? Messages::get('not_enough_money') : '⚠️ ' . $e->getMessage();
                Telegram::sendMessage((int)$from['id'], $msg);
            }
            return true;
        }

        if ($user['player_code'] !== 'PENDING') {
            return false;
        }

        if (($state['step'] ?? '') === 'name') {
            if (mb_strlen($input) < 2) {
                Telegram::sendMessage((int)$from['id'], Messages::get('invalid_input') . "\n" . Messages::get('ask_name'));
                return true;
            }

            $this->setState((int)$user['id'], ['step' => 'nickname', 'name' => $input]);
            Telegram::sendMessage((int)$from['id'], Messages::get('ask_nickname'));
            return true;
        }

        if (($state['step'] ?? '') === 'nickname') {
            if (mb_strlen($input) < 1) {
                Telegram::sendMessage((int)$from['id'], Messages::get('invalid_input') . "\n" . Messages::get('ask_nickname'));
                return true;
            }

            $this->setState((int)$user['id'], ['step' => 'background', 'name' => $state['name'], 'nick' => $input]);

            $rows = [];
            foreach (PlayerService::BACKGROUNDS as $key => $b) {
                $rows[] = [Telegram::btn($b['emoji'] . ' ' . $b['label'], "bg:{$key}")];
            }

            Telegram::sendMessage((int)$from['id'], Messages::get('choose_bg'), Telegram::kb($rows));
            return true;
        }

        return false;
    }

    public function finishCreation(array $user, string $bg): void
    {
        if (!isset(PlayerService::BACKGROUNDS[$bg]) || ($user['player_code'] === 'PENDING' && empty(json_decode($user['state'] ?? '{}', true)['name']))) {
            return;
        }

        $state = json_decode($user['state'], true);
        $b = PlayerService::BACKGROUNDS[$bg];
        $code = 'SL-' . random_int(100000, 999999);

        DB::pdo()->prepare(
            "UPDATE users SET player_code = ?, character_name = ?, nickname = ?, background = ?, cash = ?, state = NULL, updated_at = ? WHERE id = ?"
        )->execute([$code, $state['name'], $state['nick'], $bg, $b['cash'], time(), $user['id']]);

        EconomyService::log((int)$user['id'], 'admin', $b['cash'], 'سرمایه اولیه');

        $fresh = PlayerService::findByTg((int)$user['telegram_id']);
        Telegram::sendMessage(
            (int)$user['telegram_id'],
            PlayerService::renderProfile(array_merge($user, [
                'player_code' => $code,
                'character_name' => $state['name'],
                'nickname' => $state['nick'],
                'background' => $bg,
                'cash' => $b['cash'],
            ]))
        );
        Telegram::sendMessage((int)$user['telegram_id'], "🌑 <i>زندگی جدیدت شروع شد. هر انتخاب، یه رد به جا می‌ذاره...</i>", self::mainKb());
    }

    private function setState(int $userId, array $data): void
    {
        DB::pdo()->prepare(
            "UPDATE users SET state = ? WHERE id = ?"
        )->execute([json_encode($data, JSON_UNESCAPED_UNICODE), $userId]);
    }

    public static function mainKb(): array
    {
        $b = fn(string $t, string $d) => Telegram::btn($t, $d);

        return Telegram::kb([
            [$b('👤 زندگی من', 'menu:profile'), $b('💼 شغل و حرفه', 'menu:jobs')],
            [$b('🏢 کسب‌وکار', 'menu:biz'), $b('🏠 املاک', 'menu:property')],
            [$b('🚗 گاراژ', 'menu:garage'), $b('🎒 فروشگاه', 'menu:shop')],
            [$b('💰 پول و بانک', 'menu:bank'), $b('💸 انتقال پول', 'menu:transfer')],
            [$b('🎁 پاداش روزانه', 'daily:claim'), $b('🌃 منطقه خطر', 'city:crime')],
            [$b('🎯 مأموریت‌ها', 'menu:missions'), $b('🏆 دستاوردها', 'menu:achievements')],
            [$b('📊 رتبه‌بندی', 'menu:ranking'), $b('⚙️ تنظیمات', 'menu:settings')],
        ]);
    }
}
