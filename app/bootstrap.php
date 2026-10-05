<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

function base_path(string $path = ''): string { return dirname(__DIR__) . ($path ? '/' . ltrim($path, '/') : ''); }
function view_path(string $path = ''): string { return base_path('resources/views' . ($path ? '/' . ltrim($path, '/') : '')); }
function public_path(string $path = ''): string { return base_path('public' . ($path ? '/' . ltrim($path, '/') : '')); }
function storage_path(string $path = ''): string { $root=getenv('STORAGE_PATH') ?: base_path('storage'); return rtrim($root,'/\\') . ($path ? '/' . ltrim($path, '/') : ''); }

function load_env(): void {
    static $loaded = false; if ($loaded) return; $loaded = true;
    $file = base_path('.env'); if (!is_file($file)) return;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line=trim($line); if ($line==='' || str_starts_with($line,'#') || !str_contains($line,'=')) continue;
        [$key,$value]=array_map('trim',explode('=',$line,2));
        $value=trim($value," \t\n\r\0\x0B\"'");
        if (getenv($key) === false) { putenv("{$key}={$value}"); $_ENV[$key]=$value; }
    }
}
load_env();
date_default_timezone_set('Asia/Riyadh');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', storage_path('logs/php.log'));
if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
    $sessionDirectory = storage_path('sessions');
    if (!is_dir($sessionDirectory) && !mkdir($sessionDirectory, 0700, true)) throw new RuntimeException('Session storage unavailable');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    session_save_path($sessionDirectory);
    session_name('sd_session');
    session_set_cookie_params(['path'=>'/', 'httponly'=>true, 'secure'=>env('APP_ENV','production')==='production' || ($_SERVER['HTTPS'] ?? '')==='on', 'samesite'=>'Lax']);
    session_start();
}
set_exception_handler(function (Throwable $exception): void {
    $reference = bin2hex(random_bytes(6));
    error_log('Application failure '.$reference.' ['.get_class($exception).']');
    if (PHP_SAPI === 'cli') { fwrite(STDERR, "Operation failed. See private application log. Reference: $reference\n"); exit(1); }
    http_response_code(500);
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow');
    if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok'=>false,'error'=>'تعذر إتمام الطلب.','reference'=>$reference], JSON_UNESCAPED_UNICODE);
    } else {
        $title='تعذر إتمام الطلب | صروح الرقمية'; $description='يرجى المحاولة لاحقًا.'; $noindex=true;
        $contentView=view_path('pages/error.php'); require view_path('layouts/app.php');
    }
});
if (PHP_SAPI !== 'cli') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-".csp_nonce()."' https://unpkg.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' https: data:; connect-src 'self' https://*.google-analytics.com https://*.analytics.google.com https://www.googletagmanager.com; frame-src https://www.googletagmanager.com; object-src 'none'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");
    if (env('APP_ENV','production')==='production') header('Strict-Transport-Security: max-age=31536000');
    if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin')) {
        header('Cache-Control: no-store'); header('X-Robots-Tag: noindex, nofollow');
    }
}
function env(string $key, mixed $default=null): mixed { $v=getenv($key); return $v===false ? $default : $v; }
function config(string $key, mixed $default = null): mixed { static $data=null; if($data===null)$data=require base_path('config/app.php'); return $data[$key] ?? $default; }
function asset(string $path): string { return '/assets/' . ltrim($path, '/'); }
function url(string $path=''): string { return rtrim((string)config('url'),'/') . '/' . ltrim($path,'/'); }
function e(mixed $value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string { if (empty($_SESSION['_token'])) $_SESSION['_token']=bin2hex(random_bytes(32)); return $_SESSION['_token']; }
function csrf_field(): string { return '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void { $token=$_POST['_token']??null; if (!is_string($token) || empty($_SESSION['_token']) || !hash_equals($_SESSION['_token'], $token)) { http_response_code(419); exit('انتهت صلاحية الجلسة. أعد تحميل الصفحة وحاول مرة أخرى.'); } }
function redirect(string $to): never { header('Location: '.$to); exit; }
function flash(string $key, ?string $value=null): ?string { if($value!==null){$_SESSION['_flash'][$key]=$value;return null;} $v=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $v; }
function old(string $key, string $default=''): string { return (string)($_SESSION['_old'][$key] ?? $default); }
function remember_old(array $data): void { $_SESSION['_old']=$data; }
function clear_old(): void { unset($_SESSION['_old']); }
function slugify(string $text): string { $text=trim(mb_strtolower($text)); $text=preg_replace('/[^\p{Arabic}\p{L}\p{N}]+/u','-',$text) ?? ''; return trim($text,'-') ?: bin2hex(random_bytes(4)); }
function is_admin(): bool {
    if (empty($_SESSION['admin_user_id'])) return false;
    if (time()-($_SESSION['admin_last_activity']??0)>1800 || time()-($_SESSION['admin_authenticated_at']??0)>28800) {
        unset($_SESSION['admin_user_id'],$_SESSION['admin_name']); return false;
    }
    $pdo=\App\Database::connection(); if (!$pdo) return false;
    $query=$pdo->prepare("SELECT id FROM users WHERE id=? AND is_active=1 AND role='admin'");
    $query->execute([$_SESSION['admin_user_id']]); if (!$query->fetchColumn()) return false;
    $_SESSION['admin_last_activity']=time(); return true;
}
function input_text(array $data, string $key, int $length=500): string {
    $value=$data[$key]??''; return is_string($value) ? mb_substr(trim($value),0,$length) : '';
}
function json_safe(mixed $data): string { return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR); }
function csp_nonce(): string { static $nonce=null; return $nonce??=base64_encode(random_bytes(18)); }
if (PHP_SAPI !== 'cli' && in_array($_SERVER['REQUEST_METHOD']??'GET',['GET','HEAD'],true)) \App\Attribution::capture();
