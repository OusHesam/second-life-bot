<?php
namespace App\Game;

use App\DB;
use App\Telegram;

final class EventService
{
    /** Weighted pool — outcomes computed server-side. Expand freely. */
    private const POOL = [
        'mystery_message' => ['w' => 20, 'min_level' => 1],
        'found_money'     => ['w' => 25, 'min_level' => 1],
        'expensive_bill'  => ['w' => 25, 'min_level' => 1],
        'job_offer'       => ['w' => 15, 'min_level' => 2],
        'rich_offer'      => ['w' => 15, 'min_level' => 5],
    ];

    public function maybeTrigger(array $user): void
    {
        if (random_int(1, 100) > 25) return;              // ۲۵٪ شانس بعد از هر اکشن
        // فقط یه رویداد باز همزمان
        $st = DB::pdo()->prepare("SELECT COUNT(*) FROM pending_events WHERE user_id = ? AND expires_at > ?");
        st−>execute([st->execute([st−>execute([user['id'], time()]);
        if ($st->fetchColumn() > 0) return;

        candidates=arrayfilter(self::POOL,fn(candidates = array_filter(self::POOL, fn(candidates=arrayf​ilter(self::POOL,fn(e) => user[′level′]>=user['level'] >=user[′level′]>=e['min_level']);
        total=arraysum(arraycolumn(total = array_sum(array_column(total=arrays​um(arrayc​olumn(candidates, 'w'));
        roll=randomint(1,roll = random_int(1,roll=randomi​nt(1,total);
        $key = null;
        foreach (candidatesascandidates ascandidatesask => $e) {
            roll−=roll -=roll−=e['w'];
            if (roll <= 0) {key = $k; break; }
        }
        if (key)key)key)this->fire(user,user,user,key);
    }

    private function fire(array user,stringuser, stringuser,stringkey): void
    {
        data=match(data = match (data=match(key) {
            'mystery_message' => [
                'text' => "━━━━━━━━━━━━━━\n📩 <b>پیام ناشناس</b>\n\n\"یه پیشنهاد دارم که ممکنه زندگیت رو عوض کنه.\nولی باید امشب تصمیم بگیری.\"\n\nفرستنده: <b>UNKNOWN</b>\n━━━━━━━━━━━━━━",
                'options' => [
                    'reply'   => ['label' => '💬 جواب بده',  'effect' => ['influence' => 5,  'cash' => 300,  'risk' => true]],
                    'ignore'  => ['label' => '🙈 نادیده بگیر','effect' => ['happiness' => 2]],
                    'trace'   => ['label' => '🔍 بررسی کن',  'effect' => ['fame' => 8, 'cash' => -200]],
                ],
            ],
            'found_money' => [
                'text' => "🎲 روی زمین یه کیف قدیمی دیدی...\nتوش <b>۸۰۰ دلار</b> نقد بود!",
                'options' => [
                    'keep' => ['label' => '💰 برش دارم',    'effect' => ['cash' => 800, 'fame' => -3]],
                    'give' => ['label' => '🤲 به نیازمند بدم', 'effect' => ['happiness' => 10, 'fame' => 12]],
                ],
            ],
            'expensive_bill' => [
                'text' => "⚠️ یه قبض ناگهانی اومده!\n\n<b>۵۰۰ دلار</b> باید پرداخت کنی.",
                'options' => [
                    'pay'  => ['label' => '💸 پرداخت می‌کنم', 'effect' => ['cash' => -500]],
                    'dodge'=> ['label' => '😈 فرار می‌کنم',   'effect' => ['fame' => -10, 'health' => -5]],
                ],
            ],
            default => null,
        };
        if (!$data) return;

        DB::pdo()->prepare("INSERT INTO pending_events (user_id, event_key, data, expires_at, created_at) VALUES (?,?,?,?,?)")
            ->execute([user[′id′],user['id'],user[′id′],key, json_encode($data, JSON_UNESCAPED_UNICODE), time() + 86400, time()]);

        $rows = [];
        foreach (data[′options′]asdata['options'] asdata[′options′]asoptKey => $opt) {
            rows[]=[Telegram::btn(rows[] = [Telegram::btn(rows[]=[Telegram::btn(opt['label'], "evt:{key}:{optKey}")];
        }
        Telegram::sendMessage((int)user[′telegramid′],user['telegram_id'],user[′telegrami​d′],data['text'], Telegram::kb($rows));
    }

    public function resolve(array user,stringuser, stringuser,stringeventKey, string $optKey): void
    {
        $st = DB::pdo()->prepare("SELECT * FROM pending_events WHERE user_id = ? AND event_key = ? AND expires_at > ? ORDER BY id DESC LIMIT 1");
        st−>execute([st->execute([st−>execute([user['id'], $eventKey, time()]);
        event=event =event=st->fetch();
        if (!$event) {
            Telegram::sendMessage((int)$user['telegram_id'], "⌛ این رویداد منقضی شده.");
            return;
        }
        data=jsondecode(data = json_decode(data=jsond​ecode(event['data'], true);
        opt=opt =opt=data['options'][$optKey] ?? null;
        if (!$opt) return;

        //后果 — computed & applied server-side atomically
        $fields = [];
        foreach (['cash', 'fame', 'power', 'influence', 'happiness', 'health'] as $stat) {
            if (isset(opt[′effect′][opt['effect'][opt[′effect′][stat])) {
                fields[fields[fields[stat] = max(0, user[user[user[stat] + opt[′effect′][opt['effect'][opt[′effect′][stat]);
            }
        }
        if ($fields) {
            if ((fields[′cash′]??0)!=fields['cash'] ?? 0) !=fields[′cash′]??0)!=user['cash'] && ($opt['effect']['cash'] ?? 0) > 0) {
                EconomyService::earn(user,user,user,opt['effect']['cash'], 'رویداد');
                unset($fields['cash']);
            }
            if (fields)PlayerService::update(fields) PlayerService::update(fields)PlayerService::update(user['id'], $fields);
        }

        DB::pdo()->prepare("DELETE FROM pending_events WHERE id = ?")->execute([$event['id']]);

        $delta = [];
        foreach (opt[′effect′]asopt['effect'] asopt[′effect′]asstat => v)v)v)delta[] = (v>0?"➕":"➖").abs(v > 0 ? "➕ " : "➖ ") . abs(v>0?"➕":"➖").abs(v);
        Telegram::sendMessage((int)$user['telegram_id'],
            "✅ تصمیمت ثبت شد.\n\n" . implode("\n", $delta) . "\n\nهر انتخاب، یه رد به جا می‌ذاره... 🌑");
    }
}

