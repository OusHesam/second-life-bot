<?php
namespace App\Handlers;
use App\{Handler,Telegram}; use App\Game\{PlayerService,DailyService,CrimeService};
final class CityHandler implements Handler{
 public function handle(array $update):bool{$cb=$update['callback_query']??null;if(!$cb)return false;$d=$cb['data']??'';if(!str_starts_with($d,'daily:')&&!str_starts_with($d,'crime:')&&!str_starts_with($d,'city:'))return false;$chat=(int)$cb['message']['chat']['id'];$u=PlayerService::findByTg($chat);Telegram::answerCallback($cb['id']);if(!$u)return true;
 if($d==='daily:claim'){$r=DailyService::claim($u);Telegram::sendMessage($chat,isset($r['error'])?'⏳ پاداش امروز را گرفتی. فردا برگرد!':"🎁 <b>پاداش روزانه!</b>\n💵 ".number_format($r['reward'])." دلار\n🔥 استریک: {$r['streak']} روز");return true;}
 if($d==='city:crime'){$kb=Telegram::kb([[Telegram::btn('🧤 جیب‌بری','crime:pickpocket')],[Telegram::btn('🏪 سرقت فروشگاه','crime:store')],[Telegram::btn('🏦 دستبرد بزرگ','crime:bank')]]);Telegram::sendMessage($chat,"🌃 <b>دارک سیتی — منطقه خطر</b>\nهر جرم ریسک دارد. شکست = جریمه و آسیب.\n⏱ هر ۱۵ دقیقه یک اقدام.",$kb);return true;}
 if(str_starts_with($d,'crime:')){$r=CrimeService::attempt($u,substr($d,6));if(isset($r['error'])){$m=['energy'=>'⚡ انرژی کافی نداری.','level'=>'🔒 لولت کافی نیست.','cooldown'=>'⏳ باید کمی صبر کنی.','invalid'=>'❌ نامعتبر'];Telegram::sendMessage($chat,$m[$r['error']]??'❌');return true;}Telegram::sendMessage($chat,$r['ok']?"🕶 <b>موفق شدی!</b>\n{$r['name']} → +".number_format($r['amount'])." دلار":"🚨 <b>خراب شد!</b>\n{$r['name']} → ".number_format($r['fine'])." دلار جریمه و آسیب");return true;}return false;}
}
