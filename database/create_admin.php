<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require __DIR__.'/../app/bootstrap.php';
$email=mb_strtolower(trim($argv[1]??''));$stdin=($argv[2]??'')==='--password-stdin';
$password=$stdin?rtrim(fgets(STDIN)?:'',"\r\n"):($argv[2]??'');
$name=$argv[3]??'مدير صروح الرقمية';$reset=in_array('--reset',$argv,true);
if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12||strlen($password)>72||mb_strlen($name)>120){
 fwrite(STDERR,"Usage: php database/create_admin.php EMAIL --password-stdin NAME [--reset]\nProvide a unique 12–72 byte password on stdin.\n");exit(1);
}
$pdo=App\Database::connection();if(!$pdo){fwrite(STDERR,"Database unavailable. Check private configuration.\n");exit(1);}
$query=$pdo->prepare('SELECT id FROM users WHERE email=?');$query->execute([$email]);$id=$query->fetchColumn();
if($id&&!$reset){fwrite(STDERR,"Account exists. Explicit --reset is required to replace credentials.\n");exit(1);}
$hash=password_hash($password,PASSWORD_DEFAULT);
if($id)$pdo->prepare("UPDATE users SET name=?,password_hash=?,role='admin',is_active=1 WHERE id=?")->execute([$name,$hash,$id]);
else $pdo->prepare("INSERT INTO users(name,email,password_hash,role,is_active) VALUES(?,?,?,'admin',1)")->execute([$name,$email,$hash]);
echo "Administrator saved.\n";
