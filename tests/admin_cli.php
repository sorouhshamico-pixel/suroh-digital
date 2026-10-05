<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'), '_test'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$email='cli-qa-'.bin2hex(random_bytes(4)).'@example.invalid';$password=bin2hex(random_bytes(16));
function invokeAdmin(array $args,string $password):array{
 $process=proc_open([PHP_BINARY,'database/create_admin.php',...$args],[['pipe','r'],['pipe','w'],['pipe','w']],$pipes,base_path(),getenv());
 fwrite($pipes[0],$password."\n");fclose($pipes[0]);$output=stream_get_contents($pipes[1]);fclose($pipes[1]);$error=stream_get_contents($pipes[2]);fclose($pipes[2]);
 return [proc_close($process),$output.$error];
}
$count=0;
function adminCheck(bool $ok,string $label):void{global $count;if(!$ok)throw new RuntimeException($label);$count++;echo "PASS $label\n";}
try{
 [$exit,$output]=invokeAdmin([$email,'--password-stdin','QA'],$password);
 adminCheck($exit===0,'CLI admin created via stdin');
 $query=$pdo->prepare('SELECT password_hash FROM users WHERE email=?');$query->execute([$email]);$hash=$query->fetchColumn();adminCheck(password_verify($password,$hash),'CLI password hash valid');
 [$exit,$output]=invokeAdmin([$email,'--password-stdin','QA'],$password);adminCheck($exit===1,'existing credentials protected by default');
 $newPassword=bin2hex(random_bytes(16));[$exit,$output]=invokeAdmin([$email,'--password-stdin','QA','--reset'],$newPassword);
 $query->execute([$email]);$hash=$query->fetchColumn();adminCheck($exit===0&&password_verify($newPassword,$hash)&&!password_verify($password,$hash),'explicit CLI reset changes password');
 [$exit,$output]=invokeAdmin(['invalid-email','--password-stdin','QA'],$password);adminCheck($exit===1,'invalid admin email rejected');
 [$exit,$output]=invokeAdmin([$email,'--password-stdin','QA'],'weak');adminCheck($exit===1,'weak admin password rejected');
 adminCheck(!str_contains($output,$password)&&!str_contains($output,$newPassword),'CLI does not disclose credentials');
 echo "ALL $count ADMIN CLI CHECKS PASSED\n";
}finally{$pdo->prepare('DELETE FROM users WHERE email=?')->execute([$email]);}
