<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/paths.php';

// The development server is a router too: serve files before sessions/routing.
// Strip the installation prefix explicitly; PHP's return-false path lookup cannot
// serve /subfolder/assets from a public document root without that physical folder.
$path = request_path();
if (preg_match('#^/(assets|uploads)/#', $path)) {
    $decoded = rawurldecode($path);
    $file = realpath(__DIR__ . $decoded);
    $types = ['css'=>'text/css; charset=utf-8', 'js'=>'application/javascript; charset=utf-8',
        'png'=>'image/png', 'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'webp'=>'image/webp',
        'avif'=>'image/avif', 'gif'=>'image/gif', 'ico'=>'image/x-icon', 'woff'=>'font/woff',
        'woff2'=>'font/woff2', 'ttf'=>'font/ttf', 'svg'=>'image/svg+xml'];
    $extension = strtolower(pathinfo($decoded, PATHINFO_EXTENSION));
    $safe = $file && is_file($file) && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)
        && isset($types[$extension]) && !preg_match('#(^|/)\.#', $decoded)
        && !preg_match('/\.(php[0-9]?|phtml|phar)(\.|$)/i', $decoded)
        && !(str_starts_with($decoded, '/uploads/') && $extension === 'svg');
    header('X-Content-Type-Options: nosniff');
    if (!$safe) { http_response_code(404); header('Content-Type: text/plain; charset=utf-8'); exit('Asset not found'); }
    if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
        http_response_code(405); header('Allow: GET, HEAD'); exit;
    }
    header('Content-Type: ' . $types[$extension]);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=' . (isset($_GET['v']) ? '31536000, immutable' : '86400'));
    if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') readfile($file);
    exit;
}
require_once __DIR__ . '/../app/bootstrap.php';

use App\Router;
$router = new Router();
require __DIR__ . '/../routes/web.php';
if ($path !== '/' && str_ends_with($path, '/') && in_array($_SERVER['REQUEST_METHOD'], ['GET','HEAD'], true)) {
    header('Location: ' . route_path(rtrim($path, '/')) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''), true, 301);
    exit;
}
$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
