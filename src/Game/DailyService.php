<?php
namespace App\Game;
use App\DB;
final class DailyService {
 public static function claim(array $u): array {
  $pdo=DB::pdo(); $today=gmdate('Y-m-d'); $pdo->beginTransaction();
  try { $q=$pdo->prepare('SELECT * FROM daily_claims WHERE user_id=?');$q->execute([$u['id']]);$r=$q->fetch();
   if($r && $r['last_claim_date']===$today){$pdo->rollBack();return ['error'=>'already'];}
   $y=gmdate('Y-m-d',time()-86400);$streak=($r && $r['last_claim_date']===$y)?(int)$r['streak']+1:1;
   $reward=500+min($streak,7)*150+(int)$u['level']*50;
   $pdo->prepare('UPDATE users SET cash=cash+?, happiness=MIN(100,happiness+3), updated_at=? WHERE id=?')->execute([$reward,time(),$u['id']]);
   $pdo->prepare('INSERT INTO daily_claims(user_id,streak,last_claim_date,updated_at) VALUES(?,?,?,?) ON CONFLICT(user_id) DO UPDATE SET streak=excluded.streak,last_claim_date=excluded.last_claim_date,updated_at=excluded.updated_at')->execute([$u['id'],$streak,$today,time()]);
   $pdo->commit(); EconomyService::log((int)$u['id'],'daily',$reward,'پاداش روزانه'); return compact('reward','streak');
  } catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
 }
}
