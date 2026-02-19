<?php
declare(strict_types=1);

session_start();

/* =========================
   ENV LOADER SIMPLE
========================= */
function env(string $key, ?string $default = null): ?string
{
    static $vars = null;

    if ($vars === null) {
        $vars = [];

        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                [$k, $v] = array_map('trim', explode('=', $line, 2));
                $vars[$k] = $v;
            }
        }
    }

    return $vars[$key] ?? $default;
}

/* =========================
   PDO CONNECTION (singleton)
========================= */

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
            env('DB_HOST'),
            env('DB_PORT'),
            env('DB_NAME')
        );

        $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASS'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}

/* =========================
   AUTH
========================= */

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): array
{
    $u = currentUser();
    if (!$u) {
        header('Location: login.php');
        exit;
    }
    return $u;
}

function canViewUsers(array $user): bool
{
    return in_array($user['role'], ['admin','super_admin'], true);
}

function canManageUsers(array $user): bool
{
    return $user['role'] === 'super_admin';
}