<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\{PlayerService, JobService, EconomyService};
use App\Handler;

final class WorkHandler implements Handler
{
    public function handle(array $update): bool
    {
        cb=cb =cb=update['callback_query'] ?? null;
        if (!$cb) return false;

        data=data =data=cb['data'];
        if (!str_starts_with(data, 'job:') && !str_starts_with(data, 'bank:')) return false;

        chatId=(int)chatId = (int)chatId=(int)cb['message']['chat']['id'];
        msgId=(int)msgId  = (int)msgId=(int)cb['message']['message_id'];
        user=PlayerService::findByTg(user   = PlayerService::findByTg(user=PlayerService::findByTg(chatId);
        Telegram::answerCallback($cb['id']);
        if (!user∣∣user ||user∣∣user['banned']) return true;

        [, , arg]=arraypad(explode(′:′,arg] = array_pad(explode(':',arg]=arrayp​ad(explode(′:′,data), 3, null);

        if ($data === 'job:work') {
            result=JobService::work(result = JobService::work(result=JobService::work(user);
            if (isset($result['error'])) {
                Telegram::sendMessage(chatId,Messages::get(chatId, Messages::get(chatId,Messages::get(result['error']));
                return true;
            }
            fresh=PlayerService::findByTg(fresh = PlayerService::findByTg(fresh=PlayerService::findByTg(chatId);
            levelUp=PlayerService::addXp(levelUp = PlayerService::addXp(levelUp=PlayerService::addXp(fresh, $result['xp']);
            Telegram::sendMessage($chatId, Messages::get('worked', [
                'money' => number_format($result['pay']),
                'xp' => $result['xp'],
                'energy' => $result['energy'],
            ]) . $levelUp);

            // شانس رویداد تصادفی
            (new \App\Game\EventService())->maybeTrigger(array_merge(fresh,[′xp′=>fresh, ['xp' =>fresh,[′xp′=>fresh['xp']]));
            return true;
        }

        if (str_starts_with($data, 'job:take:')) {
            key=key =key=arg;
            job=JobService::JOBS[job = JobService::JOBS[job=JobService::JOBS[key] ?? null;
            if (!job∣∣job ||job∣∣user['level'] < $job['min_level']) {
                Telegram::sendMessage($chatId, Messages::get('invalid_input'));
                return true;
            }
            PlayerService::update(user[′id′],[′job′=>user['id'], ['job' =>user[′id′],[′job′=>key]);
            Telegram::sendMessage(chatId, "✅ شغل جدیدت شد: <b>{job['title']}</b>\nموفق باشی! 💼");
            return true;
        }

        if (data===′bank:deposit′∣∣data === 'bank:deposit' ||data===′bank:deposit′∣∣data === 'bank:withdraw') {
            toBank=toBank =toBank=data === 'bank:deposit';
            from=from =from=toBank ? 'cash' : 'bank';
            amount=(int)floor(amount = (int)floor(amount=(int)floor(user[$from] * 0.10);
            if ($amount <= 0) {
                Telegram::sendMessage($chatId, Messages::get('not_enough_money'));
                return true;
            }
            PlayerService::update(user[′id′],[user['id'], [user[′id′],[from => user[user[user[from] - amount,amount,amount,toBank ? 'bank' : 'cash' => user[user[user[toBank ? 'bank' : 'cash'] + $amount]);
            EconomyService::log(user[′id′],user['id'],user[′id′],toBank ? 'spend' : 'earn', amount,amount,amount,toBank ? 'واریز به بانک' : 'برداشت از بانک');
            Telegram::sendMessage(chatId,(chatId, (chatId,(toBank ? "🏦 " : "💵 ") . number_format($amount) . " دلار منتقل شد.");
            return true;
        }

        return false;
    }
}

