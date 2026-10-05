<?php
declare(strict_types=1);
require_once __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli')exit(1);
$pdo=App\Database::connection();if(!$pdo){fwrite(STDERR,"Database unavailable.\n");exit(1);}
if(!in_array('--backup-confirmed',$argv,true)){fwrite(STDERR,"Take and verify a database backup first, then pass --backup-confirmed. Fresh databases also require explicit confirmation.\n");exit(1);}
$columns=[
 'leads'=>['utm_term'=>'VARCHAR(120) NULL','referrer'=>'VARCHAR(500) NULL','visitor_id'=>'VARCHAR(80) NULL','session_id'=>'VARCHAR(80) NULL','submission_id'=>'VARCHAR(64) NULL','consent_at'=>'DATETIME NULL'],
 'tracking_events'=>['utm_content'=>'VARCHAR(120) NULL','utm_term'=>'VARCHAR(120) NULL','landing_page'=>'VARCHAR(500) NULL']
];
foreach($columns as $table=>$definitions)foreach($definitions as $name=>$definition){
 $query=$pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');
 $query->execute([$table,$name]);if(!$query->fetchColumn())$pdo->exec("ALTER TABLE $table ADD COLUMN $name $definition");
}
$query=$pdo->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='leads' AND INDEX_NAME='idx_leads_submission'");
if(!$query->fetchColumn())$pdo->exec('ALTER TABLE leads ADD UNIQUE KEY idx_leads_submission(submission_id)');
echo "Additive P0 migration complete. Existing lead statuses and records preserved.\n";
