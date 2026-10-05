<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'), '_test'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$original=(string)env('DB_DATABASE');$scratch='sd_migration_'.bin2hex(random_bytes(4)).'_test';
$pdo->exec("CREATE DATABASE $scratch CHARACTER SET utf8mb4");$count=0;
try{
 $pdo->exec("USE $scratch");$pdo->exec(file_get_contents(__DIR__.'/fixtures/legacy-schema.sql'));
 $pdo->exec("INSERT INTO leads(lead_id,name,phone,status,utm_source) VALUES('SD-LEGACY','Legacy fixture','0500000000','lost','haraj')");
 $pdo->exec("INSERT INTO tracking_events(event_name,metadata) VALUES('page_view','{}')");
 $argv=['migrate.php','--backup-confirmed'];require base_path('database/migrate.php');require base_path('database/migrate.php');
 $lead=$pdo->query('SELECT * FROM leads')->fetch();
 foreach(['legacy lead preserved'=>$lead['lead_id']==='SD-LEGACY','legacy status preserved'=>$lead['status']==='lost','legacy source preserved'=>$lead['utm_source']==='haraj','migration adds nullable consent'=>array_key_exists('consent_at',$lead)&&$lead['consent_at']===null,'legacy event preserved'=>(int)$pdo->query('SELECT COUNT(*) FROM tracking_events')->fetchColumn()===1] as $label=>$ok){if(!$ok)throw new RuntimeException($label);$count++;echo "PASS $label\n";}
 $pdo->exec("UPDATE leads SET submission_id='unique-qa'");
 try{$pdo->exec("INSERT INTO leads(lead_id,name,phone,submission_id) VALUES('SD-DUP','Duplicate','0500000000','unique-qa')");throw new RuntimeException('Unique constraint missing');}catch(PDOException $error){if($error->getCode()!=='23000')throw $error;$count++;echo "PASS unique submission enforced\n";}
 echo "ALL $count LEGACY MIGRATION CHECKS PASSED\n";
}finally{$pdo->exec("USE $original");$pdo->exec("DROP DATABASE $scratch");}
