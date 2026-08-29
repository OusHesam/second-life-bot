<?php
namespace App\Game;
use App\DB;
final class CrimeService {
 public static function attempt(array $u,string $kind):array{
  $types=['pickpocket'=>['name'=>'جیب‌بری','need'=>20,'min'=>1,'reward'=>[250,700],'risk'=>18],'store'=>['name'=>'سرقت فروشگاه','need'=>35,'min'=>3,'reward'=>[900,2400],'risk'=>28],'bank'=>['name'=>'دستبرد بزرگ','need'=>55,'min'=>7,'reward'=>[3500,9000],'risk'=>42]];
  if(!isset($types[$kind]))return ['error'=>'invalid'];$t=$types[$kind]; if($u['energy']<$t['need'])return ['error'=>'energy']; if($u['level']<$t['min'])return ['error'=>'level'];
  $q=DB::pdo()->prepare('SELECT last_crime_ts FROM crime_cooldowns WHERE user_id=?');$q->execute([$u['id']]);$last=(int)($q->fetchColumn()?:0);$wait=max(0,900-(time()-$last));if($wait)return ['error'=>'cooldown','wait'=>$wait];
  $chance=max(35,min(82,72-$t['risk']/2+(int)$u['power']/20+(int)$u['influence']/30));$ok=random_int(1,100)<=$chance;$pdo=DB::pdo();$pdo->prepare('INSERT INTO crime_cooldowns(user_id,last_crime_ts) VALUES(?,?) ON CONFLICT(user_id) DO UPDATE SET last_crime_ts=excluded.last_crime_ts')->execute([$u['id'],time()]);
  $pdo->prepare('UPDATE users SET energy=MAX(0,energy-?), updated_at=? WHERE id=?')->execute([$t['need'],time(),$u['id']]);
  if($ok){$amount=random_int(...$t['reward']);$pdo->prepare('UPDATE users SET cash=cash+?, fame=fame+1 WHERE id=?')->execute([$amount,$u['id']]);EconomyService::log((int)$u['id'],'crime',$amount,$t['name']);return ['ok'=>true,'amount'=>$amount,'name'=>$t['name']];}
  $fine=random_int(100,500);$pdo->prepare('UPDATE users SET cash=MAX(0,cash-?), health=MAX(1,health-5), happiness=MAX(0,happiness-4) WHERE id=?')->execute([$fine,$u['id']]);EconomyService::log((int)$u['id'],'fine',-$fine,$t['name'].' ناموفق');return ['ok'=>false,'fine'=>$fine,'name'=>$t['name']];
 }
}
