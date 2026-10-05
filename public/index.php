<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
    $file = realpath(__DIR__.$path);
    if ($file && str_starts_with($file, __DIR__.DIRECTORY_SEPARATOR) && is_file($file)
        && preg_match('#^/(assets|uploads)/#', $path) && preg_match('/\.(css|js|png|jpe?g|webp|avif|gif|ico|woff2?|ttf|svg)$/i', $path)
        && !preg_match('/\.(php[0-9]?|phtml|phar)(\.|$)/i', $path)
        && !preg_match('#(^|/)\.#', $path) && !(str_starts_with($path,'/uploads/')&&str_ends_with(strtolower($path),'.svg'))) return false;
}
require_once __DIR__ . '/../app/bootstrap.php';

use App\Router;

$router = new Router();
require __DIR__ . '/../routes/web.php';
$path=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($path !== '/' && str_ends_with($path, '/') && in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD'], true)) {
    header('Location: '.rtrim($path, '/').(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), true, 301); exit;
}
$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
