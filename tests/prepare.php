<?php
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'), '_test'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$pdo->exec(file_get_contents(base_path('database/schema.sql')));
$argv=['migrate.php','--backup-confirmed'];require base_path('database/migrate.php');
