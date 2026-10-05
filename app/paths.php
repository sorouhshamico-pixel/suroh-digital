<?php
declare(strict_types=1);

// Shared by the front controller and bootstrap: no sessions or database access.
function base_path(string $path = ''): string { return dirname(__DIR__) . ($path ? '/' . ltrim($path, '/') : ''); }
function public_path(string $path = ''): string { return base_path('public' . ($path ? '/' . ltrim($path, '/') : '')); }
function env(string $key, mixed $default = null): mixed { $value = getenv($key); return $value === false ? $default : $value; }
function load_env(): void {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;
    $file = base_path('.env');
    if (!is_file($file)) return;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) continue;
        if (strlen($value) >= 2 && in_array($value[0], ['"', "'"], true) && $value[0] === $value[strlen($value) - 1]) $value = substr($value, 1, -1);
        if (getenv($key) === false) { putenv("{$key}={$value}"); $_ENV[$key] = $value; }
    }
}
load_env();

function app_base_path(): string {
    return rtrim((string) (parse_url((string) env('APP_URL', 'https://digital.suroohalshami.com'), PHP_URL_PATH) ?: ''), '/');
}
/** Same-origin public URL, including the configured installation directory. */
function route_path(string $path = '/'): string {
    $base = app_base_path();
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}
/** Logical route used by the router, access control, analytics and canonicals. */
function request_path(): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = app_base_path();
    if ($base === '') return $path;
    if ($path === $base || $path === $base . '/') return '/';
    return str_starts_with($path, $base . '/') ? substr($path, strlen($base)) : '/__outside_mount__';
}
/** Stored local media uses logical /assets or /uploads URLs; external media stays intact. */
function media_url(string $path): string {
    return str_starts_with($path, '/') && !str_starts_with($path, '//') ? route_path($path) : $path;
}
function asset(string $path): string {
    $path = 'assets/' . ltrim($path, '/');
    $file = public_path($path);
    static $versions = [];
    $version = $versions[$path] ??= (is_file($file) ? substr(hash_file('sha256', $file), 0, 12) : '');
    return route_path($path) . ($version !== '' ? '?v=' . $version : '');
}
