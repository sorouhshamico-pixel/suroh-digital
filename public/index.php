<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Router;

$router = new Router();
require __DIR__ . '/../routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
