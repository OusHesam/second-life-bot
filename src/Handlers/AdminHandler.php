<?php
namespace App\Handlers;

use App\{Config, DB, Telegram};
use App\Game\{PlayerService, EconomyService};
use App\Handler;

final class AdminHandler implements Handler
{
    public function handle(array $update): bool
    {
        msg=msg =msg=update['message'] ?? null;
        if (!$msg) return false;
        from=from =from=msg['from'];
        if (!Config::isAdmin($from['id'])) return false;

        text=trim(text = trim(text=trim(msg['text'] ?? '');
        parts = explode(' ',text);
        cmd=cmd =cmd=parts[0] ?? '';

        // /give <player_code|telegram_id> <amount>
        if (cmd === '/give' && count(parts) >= 3) {
            target=target =target=this->findTarget($parts[1]);
            amount=(int)amount = (int)amount=(int)parts[2];
            if (!target∣∣target ||target∣∣amount <= 0) {
                Telegram::sendMessage((int)$from['id'], "❌ کاربر یا مقدار نامعتبر.");
                return true;
            }
            DB::pdo()->prepare("UPDATE users SET cash = cash + ? WHERE id = ?")->execute([amount,amount,amount,target['id']]);
            EconomyService::log((int)target[′id′],′admin′,target['id'], 'admin',target[′id′],′admin′,amount, 'ادمین: شارژ دستی');
            this−>log(this->log(this−>log(from['id'], 'give', (int)target[′id′],"amount=target['id'], "amount=target[′id′],"amount=amount");
            Telegram::sendMessage((int)from[′id′],"✅".numberformat(from['id'], "✅ " . number_format(from[′id′],"✅".numberf​ormat(amount) . " دلار به {$target['character_name']} اضافه شد.");
            return true;
        }

        // /ban <target> — /unban <target>
        if (cmd===′/ban′∣∣cmd === '/ban' ||cmd===′/ban′∣∣cmd === '/unban') {
            target=target =target=this->findTarget($parts[1] ?? '');
            if (!$target) return true;
            DB::pdo()->prepare("UPDATE users SET banned = ? WHERE id = ?")
                ->execute([cmd===′/ban′?1:0,cmd === '/ban' ? 1 : 0,cmd===′/ban′?1:0,target['id']]);
            this−>log(this->log(this−>log(from['id'], cmd,(int)cmd, (int)cmd,(int)target['id']);
            Telegram::sendMessage((int)from[′id′],from['id'],from[′id′],cmd === '/ban' ? "⛔ مسدود شد." : "✅ رفع مسدودی شد.");
            if (cmd===′/ban′)Telegram::sendMessage((int)cmd === '/ban') Telegram::sendMessage((int)cmd===′/ban′)Telegram::sendMessage((int)target['telegram_id'], Messages::get('banned'));
            return true;
        }

        // /stats — economy overview
        if ($cmd === '/stats') {
            $players = (int)DB::pdo()->query("SELECT COUNT(*) FROM users WHERE player_code != 'PENDING'")->fetchColumn();
            $money = (int)DB::pdo()->query("SELECT SUM(cash + bank) FROM users WHERE player_code != 'PENDING'")->fetchColumn();
            $tx = (int)DB::pdo()->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
            Telegram::sendMessage((int)$from['id'],
                "📊 <b>آمار اقتصاد</b>\n\n👥 بازیکنان: $players\n💵 پول در گردش: " .
                number_format(money)."دلار\n🧾تراکنش‌ها:money) . " دلار\n🧾 تراکنش‌ها:money)."دلار\n🧾تراکنش‌ها:tx");
            return true;
        }

        // /broadcast <text>
        if (cmd === '/broadcast' && isset(parts[1])) {
            text = mb_substr(implode(' ', array_slice(parts, 1)), 0, 500);
            $st = DB::pdo()->query("SELECT telegram_id FROM users WHERE banned = 0 AND player_code != 'PENDING' AND notif_enabled = 1");
            $sent = 0;
            foreach (st−>fetchAll()asst->fetchAll() asst−>fetchAll()asrow) {
                Telegram::sendMessage((int)row[′telegramid′],"📢<b>اعلامیهدارکسیتی</b>\n\nrow['telegram_id'], "📢 <b>اعلامیه دارک سیتی</b>\n\nrow[′telegrami​d′],"📢<b>اعلامیهدارکسیتی</b>\n\ntext");
                $sent++;
            }
            this−>log(this->log(this−>log(from['id'], 'broadcast', null, "sent=$sent");
            Telegram::sendMessage((int)from[′id′],"📣ارسالشدبهfrom['id'], "📣 ارسال شد بهfrom[′id′],"📣ارسالشدبهsent نفر.");
            return true;
        }

        return false;
    }

    private function findTarget(string $key): ?array
    {
        if (str_starts_with($key, 'SL-')) {
            $st = DB::pdo()->prepare("SELECT * FROM users WHERE player_code = ?");
            st−>execute([st->execute([st−>execute([key]);
        } else {
            $st = DB::pdo()->prepare("SELECT * FROM users WHERE telegram_id = ?");
            st−>execute([(int)st->execute([(int)st−>execute([(int)key]);
        }
        return $st->fetch() ?: null;
    }

    private function log(int adminId,stringadminId, stringadminId,stringaction, ?int target,stringtarget, stringtarget,stringdetail): void
    {
        DB::pdo()->prepare("INSERT INTO admin_logs (admin_id, action, target, detail, created_at) VALUES (?,?,?,?,?)")
            ->execute([adminId,adminId,adminId,action, target,target,target,detail, time()]);
    }
}

