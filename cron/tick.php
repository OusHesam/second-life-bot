<?php
declare(strict_types=1);
require dirname(__DIR__).'/src/bootstrap.php';
use App\DB;
$now=time();
DB::pdo()->prepare('DELETE FROM pending_events WHERE expires_at < ?')->execute([$now]);
// Regenerate 1 energy per 5 minutes, capped at 100.
$st=DB::pdo()->query('SELECT id,energy,last_energy_ts FROM users');
foreach($st->fetchAll() as $u){$last=(int)$u['last_energy_ts'];if($last<=0){DB::pdo()->prepare('UPDATE users SET last_energy_ts=? WHERE id=?')->execute([$now,$u['id']]);continue;}$steps=intdiv(max(0,$now-$last),300);if($steps>0){DB::pdo()->prepare('UPDATE users SET energy=MIN(100,energy+?),last_energy_ts=? WHERE id=?')->execute([$steps,$last+$steps*300,$u['id']]);}}
echo "OK $now\n";
