<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{PlayerService, JobService, EconomyService, EventService};
use App\Handler;

final class WorkHandler implements Handler
{
    public function handle(array $update): bool
    {
        $cb = $update['callback_query'] ?? null;
        if (!$cb) {
            return false;
        }

        $data = $cb['data'];
        if (!str_starts_with($data, 'job:') && !str_starts_with($data, 'bank:')) {
            return false;
        }

        $chatId = (int)$cb['message']['chat']['id'];
        $msgId = (int)$cb['message']['message_id'];
        $user = PlayerService::findByTg($chatId);

        Telegram::answerCallback($cb['id']);

        if (!$user || $user['banned']) {
            return true;
        }

        [, , $arg] = array_pad(explode(':', $data), 3, null);

        if ($data === 'job:work') {
            $result = JobService::work($user);
            if (isset($result['error'])) {
                Telegram::sendMessage($chatId, Messages::get($result['error']));
                return true;
            }

            $fresh = PlayerService::findByTg($chatId);
            $levelUp = PlayerService::addXp($fresh, $result['xp']);

            Telegram::sendMessage($chatId, Messages::get('worked', [
                'money' => number_format($result['pay']),
                'xp' => $result['xp'],
                'energy' => $result['energy'],
            ]) . $levelUp);

            // شانس رویداد تصادفی
            (new EventService())->maybeTrigger(array_merge($fresh, ['xp' => $fresh['xp']]));
            return true;
        }

        if (str_starts_with($data, 'job:take:')) {
            $key = $arg;
            $job = JobService::JOBS[$key] ?? null;

            if (!$job || $user['level'] < $job['min_level']) {
                Telegram::sendMessage($chatId, Messages::get('invalid_input'));
                return true;
            }

            PlayerService::update((int)$user['id'], ['job' => $key]);
            Telegram::sendMessage($chatId, "✅ شغل جدیدت شد: <b>{$job['title']}</b>\nموفق باشی! 💼");
            return true;
        }

        if ($data === 'bank:deposit' || $data === 'bank:withdraw') {
            $toBank = $data === 'bank:deposit';
            $from = $toBank ? 'cash' : 'bank';
            $amount = (int)floor($user[$from] * 0.10);

            if ($amount <= 0) {
                Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
                return true;
            }

            PlayerService::update((int)$user['id'], [
                $from => $user[$from] - $amount,
                ($toBank ? 'bank' : 'cash') => $user[$toBank ? 'bank' : 'cash'] + $amount,
            ]);

            EconomyService::log((int)$user['id'], $toBank ? 'spend' : 'earn', $amount, $toBank ? 'واریز به بانک' : 'برداشت از بانک');
            Telegram::sendMessage($chatId, ($toBank ? "🏦 " : "💵 ") . number_format($amount) . " دلار منتقل شد.");
            return true;
        }

        return false;
    }
}
