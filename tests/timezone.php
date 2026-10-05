<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'),'_test')||!str_contains(str_replace('\\','/',storage_path()),'/storage/testing/'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$checks=0;
function clockCheck(bool $ok,string $label):void{global $checks;if(!$ok)throw new RuntimeException($label);$checks++;echo "PASS $label\n";}
clockCheck($pdo->query('SELECT @@session.time_zone')->fetchColumn()==='+03:00','MySQL session matches Riyadh');
clockCheck(abs(strtotime($pdo->query('SELECT NOW()')->fetchColumn())-time())<=2,'PHP and SQL publication clocks agree');
$query=$pdo->prepare('SELECT CAST(? AS DATETIME)<=NOW()');
$query->execute([date('Y-m-d H:i:s')]);clockCheck((bool)$query->fetchColumn(),'publish now is immediately visible');
$query->execute([date('Y-m-d H:i:s',time()+3600)]);clockCheck(!(bool)$query->fetchColumn(),'future publication stays scheduled');
echo "ALL $checks TIMEZONE CHECKS PASSED\n";
