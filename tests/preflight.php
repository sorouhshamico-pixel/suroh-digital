<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'), '_test'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$user='sd_preflight_'.bin2hex(random_bytes(4));$password=bin2hex(random_bytes(24));$db=(string)env('DB_DATABASE');
if(!preg_match('/^[a-zA-Z0-9_]+$/',$db))exit(1);
function runPreflight(array $environment):array{
 $process=proc_open([PHP_BINARY,'tools/preflight.php'],[['pipe','r'],['pipe','w'],['pipe','w']],$pipes,base_path(),$environment);
 fclose($pipes[0]);$output=stream_get_contents($pipes[1]);fclose($pipes[1]);$error=stream_get_contents($pipes[2]);fclose($pipes[2]);
 return [proc_close($process),$output,$error];
}
$pdo->exec("CREATE USER '$user'@'%' IDENTIFIED BY '$password'");
try{
 $pdo->exec("GRANT SELECT ON $db.* TO '$user'@'%'");
 $environment=getenv();$environment['DB_USERNAME']=$user;$environment['DB_PASSWORD']=$password;
 $environment['APP_ENV']='production';$environment['APP_URL']='https://digital.suroohalshami.com';
 [$exit,$output]=runPreflight($environment);
 if($exit!==0)throw new RuntimeException($output);
 echo "PASS preflight accepts valid simulated production configuration\n";
 $environment['APP_ENV']='local';$environment['APP_URL']='http://127.0.0.1:8081';
 [$exit,$output]=runPreflight($environment);
 if($exit===0||!str_contains($output,'FAIL APP_ENV is production'))throw new RuntimeException('Development preflight incorrectly passed');
 echo "PASS preflight rejects development configuration\n";
 $environment['APP_ENV']='production';$environment['APP_URL']='https://digital.suroohalshami.com';$environment['DB_PASSWORD']='deliberately-incorrect';
 [$exit,$output]=runPreflight($environment);if($exit===0||!str_contains($output,'FAIL MySQL connection'))throw new RuntimeException('Disconnected preflight incorrectly passed');
 echo "PASS preflight rejects database outage\nALL 3 PREFLIGHT CHECKS PASSED\n";
}finally{$pdo->exec("DROP USER '$user'@'%'");}
