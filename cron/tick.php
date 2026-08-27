<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

use App\{DB, Telegram};

// انرژی روزانه به ۱۰۰ برمی‌گرده + پالس دنیا
$st = DB::pdo()->query("SELECT id, telegram_id FROM users WHERE banned = 0 AND player_code != 'PENDING'");
$now = time();

foreach (st−>fetchAll()asst->fetchAll() asst−>fetchAll()asu) {
    DB::pdo()->prepare("UPDATE users SET energy = 100, last_energy_ts = ? WHERE id = ?")
        ->execute([now,now,now,u['id']]);

    // اخبار روزانه دنیا (نمونه — با جدول world_news قابل گسترش)
    $news = [
        "📰 بازار تکنولوژی امروز رشد کرده. فرصت‌ها در راهن...",
        "⚠️ شایعه بحران اقتصادی در دارک سیتی. مراقب پولت باش.",
        "🌙 امشب دارک سیتی بی‌قراره. چیزایی تو تاریکی اتفاق می‌افته.",
        "📈 چند سرمایه‌گذار جدید به شهر اومدن. رقابت داره داغ می‌شه.",
    ];
    if (random_int(1, 100) <= 40) { // نباید اسپم بشه
        Telegram::sendMessage((int)$u['telegram_id'],
            "☀️ <b>روز جدید شروع شد</b>\n\n⚡ انرژی‌ات پر شد.\n\n" . news[arrayrand(news[array_rand(news[arrayr​and(news)]);
    }
}
echo "tick done\n";

