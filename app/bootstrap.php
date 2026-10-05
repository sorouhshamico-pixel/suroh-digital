<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly'=>true,'secure'=>(($_SERVER['HTTPS'] ?? '') === 'on'),'samesite'=>'Lax']);
    session_start();
}

function base_path(string $path = ''): string { return dirname(__DIR__) . ($path ? '/' . ltrim($path, '/') : ''); }
function view_path(string $path = ''): string { return base_path('resources/views' . ($path ? '/' . ltrim($path, '/') : '')); }
function public_path(string $path = ''): string { return base_path('public' . ($path ? '/' . ltrim($path, '/') : '')); }
function storage_path(string $path = ''): string { return base_path('storage' . ($path ? '/' . ltrim($path, '/') : '')); }

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
function env(string $key, mixed $default=null): mixed { $v=getenv($key); return $v===false ? $default : $v; }
function config(string $key, mixed $default = null): mixed { static $data=null; if($data===null)$data=require base_path('config/app.php'); return $data[$key] ?? $default; }
function asset(string $path): string { return '/assets/' . ltrim($path, '/'); }
function url(string $path=''): string { return rtrim((string)config('url'),'/') . '/' . ltrim($path,'/'); }
function e(mixed $value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string { if (empty($_SESSION['_token'])) $_SESSION['_token']=bin2hex(random_bytes(32)); return $_SESSION['_token']; }
function csrf_field(): string { return '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void { if (!hash_equals($_SESSION['_token'] ?? '', $_POST['_token'] ?? '')) { http_response_code(419); exit('انتهت صلاحية الجلسة. أعد تحميل الصفحة وحاول مرة أخرى.'); } }
function redirect(string $to): never { header('Location: '.$to); exit; }
function flash(string $key, ?string $value=null): ?string { if($value!==null){$_SESSION['_flash'][$key]=$value;return null;} $v=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $v; }
function old(string $key, string $default=''): string { return (string)($_SESSION['_old'][$key] ?? $default); }
function remember_old(array $data): void { $_SESSION['_old']=$data; }
function clear_old(): void { unset($_SESSION['_old']); }
function slugify(string $text): string { $text=trim(mb_strtolower($text)); $text=preg_replace('/[^\p{Arabic}\p{L}\p{N}]+/u','-',$text) ?? ''; return trim($text,'-') ?: bin2hex(random_bytes(4)); }
function is_admin(): bool { return !empty($_SESSION['admin_user_id']); }
