<?php
require __DIR__.'/../app/bootstrap.php';
if(!str_ends_with((string)env('DB_DATABASE'), '_test')||!str_contains(str_replace('\\','/',storage_path()),'/storage/testing/'))exit(1);
foreach(glob(storage_path('cache/rate-limits/*.json'))?:[] as $bucket)unlink($bucket);
$password=bin2hex(random_bytes(16));$email='ui-qa@example.invalid';
$pdo=App\Database::connection();if(!$pdo)exit(1);
$pdo->prepare("INSERT INTO users(name,email,password_hash,role,is_active) VALUES('UI QA',?,?,'admin',1) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash),is_active=1")->execute([$email,password_hash($password,PASSWORD_DEFAULT)]);
echo json_encode(compact('email','password'));
