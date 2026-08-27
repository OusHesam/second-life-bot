<?php
namespace App\Handlers;

use App\{DB, Messages, Telegram};
use App\Game\PlayerService;
use App\Handler;

final class StartHandler implements Handler
{
    public function handle(array $update): bool
    {
        msg=msg =msg=update['message'] ?? null;
        if (!$msg) return false;
        from=from =from=msg['from'];
        text=trim(text = trim(text=trim(msg['text'] ?? '');
        user=PlayerService::findByTg(user = PlayerService::findByTg(user=PlayerService::findByTg(from['id']);

        if ($text === '/start') {
            if (user &&user['player_code'] !== 'PENDING') {
                Telegram::sendMessage((int)$from['id'], Messages::get('main_menu'), self::mainKb());
                return true;
            }
            if (!$user) {
                // رکورد موقت — پول در finishCreation بسته به background ست می‌شه (فیکس باگ ۲)
                DB::pdo()->prepare(
                    "INSERT OR IGNORE INTO users (telegram_id, username, player_code, character_name, background,
                     cash, created_at, updated_at, state) VALUES (?,?,PENDING', '', 'none', 0, ?, ?, ?)"
                )->execute([from[′id′],from['id'],from[′id′],from['username'] ?? null, time(), time(), json_encode(['step' => 'name'])]);
            }
            Telegram::sendMessage((int)$from['id'], Messages::get('welcome'));
            Telegram::sendMessage((int)$from['id'], Messages::get('ask_name'));
            return true;
        }

        // مراحل ساخت شخصیت
        if (!user∣∣user ||user∣∣user['player_code'] !== 'PENDING') return false;
        state=jsondecode(state = json_decode(state=jsond​ecode(user['state'] ?? '{}', true);
        input=mbsubstr(trim(input = mb_substr(trim(input=mbs​ubstr(trim(text), 0, 25);

        if (($state['step'] ?? '') === 'name') {
            if (mb_strlen($input) < 2) {
                Telegram::sendMessage((int)$from['id'], Messages::get('invalid_input') . "\n" . Messages::get('ask_name'));
                return true;
            }
            this−>setState((int)this->setState((int)this−>setState((int)user['id'], ['step' => 'nickname', 'name' => $input]);
            Telegram::sendMessage((int)$from['id'], Messages::get('ask_nickname'));
            return true;
        }

        if (($state['step'] ?? '') === 'nickname') {
            if (mb_strlen($input) < 1) {
                Telegram::sendMessage((int)$from['id'], Messages::get('invalid_input') . "\n" . Messages::get('ask_nickname'));
                return true;
            }
            this−>setState((int)this->setState((int)this−>setState((int)user['id'], ['step' => 'background', 'name' => state[′name′],′nick′=>state['name'], 'nick' =>state[′name′],′nick′=>input]);
            $rows = [];
            foreach (PlayerService::BACKGROUNDS as key=>key =>key=>b) {
                rows[]=[Telegram::btn(rows[] = [Telegram::btn(rows[]=[Telegram::btn(b['emoji'] . ' ' . b[′label′],"bg:b['label'], "bg:b[′label′],"bg:key")];
            }
            Telegram::sendMessage((int)from[′id′],Messages::get(′choosebg′),Telegram::kb(from['id'], Messages::get('choose_bg'), Telegram::kb(from[′id′],Messages::get(′chooseb​g′),Telegram::kb(rows));
            return true;
        }
        return false;
    }

    public function finishCreation(array user,stringuser, stringuser,stringbg): void
    {
        if (!isset(PlayerService::BACKGROUNDS[bg])∣∣bg]) ||bg])∣∣user['player_code'] === 'PENDING' && empty(json_decode($user['state'] ?? '{}', true)['name'])) {
            return; // محافظت: بدون اسم و لقب، ساخت کامل نمی‌شه
        }
        state=jsondecode(state = json_decode(state=jsond​ecode(user['state'], true);
        b=PlayerService::BACKGROUNDS[b = PlayerService::BACKGROUNDS[b=PlayerService::BACKGROUNDS[bg];
        $code = 'SL-' . random_int(100000, 999999);

        DB::pdo()->prepare(
            "UPDATE users SET player_code=?, character_name=?, nickname=?, background=?, cash=?, state=NULL, updated_at=? WHERE id=?"
        )->execute([code,code,code,state['name'], state[′nick′],state['nick'],state[′nick′],bg, b[′cash′],time(),b['cash'], time(),b[′cash′],time(),user['id']]);

        EconomyService::log((int)user[′id′],′admin′,user['id'], 'admin',user[′id′],′admin′,b['cash'], 'سرمایه اولیه');
        fresh=PlayerService::findByTg((int)fresh = PlayerService::findByTg((int)fresh=PlayerService::findByTg((int)user['

> ⚠️ The response reached the length limit. Reply **continue** to get the rest.
telegram_id']), PlayerService::renderProfile(array_merge($user, [
            'player_code' => code,′charactername′=>code, 'character_name' =>code,′charactern​ame′=>state['name'],
            'nickname' => state[′nick′],′background′=>state['nick'], 'background' =>state[′nick′],′background′=>bg, 'cash' => $b['cash'],
        ])));
        Telegram::sendMessage((int)$user['telegram_id'], "🌑 <i>زندگی جدیدت شروع شد. هر انتخاب، یه رد به جا می‌ذاره...</i>", self::mainKb());
    }

    private function setState(int userId,arrayuserId, arrayuserId,arraydata): void
    {
        DB::pdo()->prepare(
    "INSERT OR IGNORE INTO users (telegram_id, username, player_code, character_name, background,
     cash, created_at, updated_at, state) VALUES (?,?,'PENDING','', 'none', 0, ?, ?, ?)"
)->execute([from[′id′],from['id'],from[′id′],from['username'] ?? null, time(), time(), json_encode(['step' => 'name'])]);


    public static function mainKb(): array
    {
        b=fn(stringb = fn(stringb=fn(stringt, string d)=>Telegram::btn(d) => Telegram::btn(d)=>Telegram::btn(t, $d);
        return Telegram::kb([
            [b(′👤زندگیمن′,′menu:profile′),b('👤 زندگی من', 'menu:profile'),b(′👤زندگیمن′,′menu:profile′),b('💼 شغل و حرفه', 'menu:jobs')],
            [b(′🏢کسب‌وکار′,′menu:biz′),b('🏢 کسب‌وکار', 'menu:biz'),b(′🏢کسب‌وکار′,′menu:biz′),b('🏠 املاک', 'menu:property')],
            [b(′🚗گاراژ′,′menu:garage′),b('🚗 گاراژ', 'menu:garage'),b(′🚗گاراژ′,′menu:garage′),b('🎒 فروشگاه', 'menu:shop')],
            [b(′💰پولوبانک′,′menu:bank′),b('💰 پول و بانک', 'menu:bank'),b(′💰پولوبانک′,′menu:bank′),b('💸 انتقال پول', 'menu:transfer')],
            [b(′🎯مأموریت‌ها′,′menu:missions′),b('🎯 مأموریت‌ها', 'menu:missions'),b(′🎯مأموریت‌ها′,′menu:missions′),b('🏆 دستاوردها', 'menu:achievements')],
            [b(′📊رتبه‌بندی′,′menu:ranking′),b('📊 رتبه‌بندی', 'menu:ranking'),b(′📊رتبه‌بندی′,′menu:ranking′),b('⚙️ تنظیمات', 'menu:settings')],
        ]);
    }
}
