<?php
namespace App\Controllers;
use App\Database; use App\Attribution; use App\RateLimiter;
final class TrackingController extends Controller {
 public function store():void {
    header('Cache-Control: no-store');
    if((int)($_SERVER['CONTENT_LENGTH']??0)>8192)$this->json(['ok'=>false],413);
    if(!str_starts_with(strtolower($_SERVER['CONTENT_TYPE']??''),'application/json'))$this->json(['ok'=>false],415);
    $raw=file_get_contents('php://input',false,null,0,8193);if(strlen($raw)>8192)$this->json(['ok'=>false],413);
    $payload=json_decode($raw,true);
    if(!is_array($payload)||json_last_error()!==JSON_ERROR_NONE)$this->json(['ok'=>false],400);
    $token=$payload['_token']??null;
    if(!is_string($token)||empty($_SESSION['_token'])||!hash_equals($_SESSION['_token'],$token))$this->json(['ok'=>false],403);
    $event=input_text($payload,'event',40);
    // form_submit is recorded transactionally by LeadController only after persistence.
    if(!in_array($event,['page_view','whatsapp_click','phone_click','form_start','cta_click','service_view'],true))$this->json(['ok'=>false],422);
    if(!RateLimiter::allow('track:'.($_SERVER['REMOTE_ADDR']??''),180,60))$this->json(['ok'=>false],429);
    $pdo=Database::connection();if(!$pdo)$this->json(['ok'=>false],503);
    $a=Attribution::current();
    $st=$pdo->prepare("INSERT INTO tracking_events(visitor_id,session_id,event_name,page_url,landing_page,referrer,utm_source,utm_medium,utm_campaign,utm_content,utm_term,metadata) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
    $st->execute([$_SESSION['visitor_id']??'',$_SESSION['tracking_session_id']??'',$event,Attribution::path(input_text($payload,'page')),$a['landing_page'],$a['referrer'],$a['utm_source'],$a['utm_medium'],$a['utm_campaign'],$a['utm_content'],$a['utm_term'],'{}']);
    $this->json(['ok'=>true]);
 }
}
