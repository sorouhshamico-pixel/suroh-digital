<?php
namespace App;
final class Attribution {
    public const KEYS=['utm_source','utm_medium','utm_campaign','utm_content','utm_term'];
    public static function path(string $value):string {
        $path=parse_url($value,PHP_URL_PATH);
        return is_string($path)&&str_starts_with($path,'/')&&!str_starts_with($path,'//')?mb_substr($path,0,500):'/';
    }
    public static function capture():void {
        $path=request_path();
        if(str_starts_with($path,'/admin')||str_starts_with($path,'/api/')||in_array($path,['/contact','/robots.txt','/sitemap.xml'],true))return;
        $campaign=false;foreach(self::KEYS as $key)if(input_text($_GET,$key,120)!=='')$campaign=true;
        if(!isset($_SESSION['attribution'])||$campaign){
            $data=[];foreach(self::KEYS as $key)$data[$key]=input_text($_GET,$key,120);
            $data['landing_page']=self::path($_SERVER['REQUEST_URI']??'/');
            $ref=parse_url($_SERVER['HTTP_REFERER']??'',PHP_URL_HOST);
            $data['referrer']=is_string($ref)?mb_substr($ref,0,255):'';
            $_SESSION['attribution']=$data;
        }
        if(empty($_SESSION['visitor_id'])){
            $visitor=$_COOKIE['sd_visitor']??'';
            $_SESSION['visitor_id']=is_string($visitor)&&preg_match('/^[a-f0-9]{32}$/',$visitor)?$visitor:bin2hex(random_bytes(16));
            setcookie('sd_visitor',$_SESSION['visitor_id'],['expires'=>time()+31536000,'path'=>route_path(),'httponly'=>true,'secure'=>env('APP_ENV','production')==='production'||($_SERVER['HTTPS']??'')==='on','samesite'=>'Lax']);
        }
        if(empty($_SESSION['tracking_session_id']))$_SESSION['tracking_session_id']=bin2hex(random_bytes(16));
    }
    public static function current():array{return $_SESSION['attribution']??array_fill_keys([...self::KEYS,'landing_page','referrer'],'');}
}
