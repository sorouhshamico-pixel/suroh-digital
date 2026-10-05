<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli')exit(1);
$failed=0;
function verify(bool $ok,string $label):void{global $failed;echo ($ok?'PASS ':'FAIL ').$label."\n";if(!$ok)$failed++;}
verify(env('APP_ENV')==='production','APP_ENV is production');
verify(rtrim((string)env('APP_URL'),'/')==='https://digital.suroohalshami.com','HTTPS production canonical host');
foreach(['pdo_mysql','mbstring','dom','json','session'] as $extension)verify(extension_loaded($extension),'PHP extension '.$extension);
verify(PHP_VERSION_ID>=80200,'PHP 8.2+');
verify((string)env('DB_DATABASE')!==''&&(string)env('DB_USERNAME')!==''&&env('DB_USERNAME')!=='root','dedicated configured database user');
$pdo=App\Database::connection();verify($pdo!==null,'MySQL connection');
if($pdo){
 foreach(['users','posts','leads','tracking_events','settings'] as $table){
  $query=$pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');$query->execute([$table]);verify((bool)$query->fetchColumn(),'table '.$table);
 }
 foreach(['leads'=>['utm_term','visitor_id','session_id','submission_id','consent_at'],'tracking_events'=>['utm_content','utm_term','landing_page']] as $table=>$columns)foreach($columns as $column){
  $query=$pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');$query->execute([$table,$column]);verify((bool)$query->fetchColumn(),'migration '.$table.'.'.$column);
 }
 $query=$pdo->query("SELECT COUNT(*) FROM users WHERE is_active=1 AND role='admin'");verify((int)$query->fetchColumn()>0,'active administrator exists');
 $query=$pdo->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='leads' AND INDEX_NAME='idx_leads_submission' AND NON_UNIQUE=0");verify((bool)$query->fetchColumn(),'unique submission index');
}
foreach(['logs','cache','sessions'] as $directory)verify(is_dir(storage_path($directory))&&is_writable(storage_path($directory)),'private writable storage/'.$directory);
$storage=realpath(storage_path());$public=realpath(public_path());
verify($storage!==false&&$public!==false&&!str_starts_with(str_replace('\\','/',$storage),str_replace('\\','/',$public).'/'),'runtime storage outside public root');
verify(is_file(public_path('.htaccess'))&&is_file(base_path('.htaccess')),'document-root guards present');
verify(!is_file(public_path('.env')),'no public environment file');
verify(is_file(public_path('uploads/.htaccess')),'upload execution guard present');
verify(!is_file(public_path('sitemap.xml'))&&!is_file(public_path('robots.txt')),'dynamic SEO routes not shadowed by files');
foreach(['GTM_CONTAINER_ID'=>'/^GTM-[A-Z0-9]+$/','GA4_MEASUREMENT_ID'=>'/^G-[A-Z0-9]+$/'] as $key=>$regex)verify(env($key,'')===''||(bool)preg_match($regex,(string)env($key)),'optional analytics identifier '.$key);
echo $failed?"Preflight failed: $failed blockers.\n":"Code/config preflight passed. Still verify HTTPS, actual document root, backups and mobile flows on the target server.\n";
exit($failed?1:0);
