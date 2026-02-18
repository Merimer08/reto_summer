<?php
declare(strict_types=1);

session_start();

define('DATA_DIR', __DIR__ . '/data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('WEIGHTS_FILE', DATA_DIR . '/weights.json');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');
define('MAX_USERS', 15);

function readJson(string $path, $default) {
    if (!file_exists($path)) return $default;
    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') return $default;
    $data = json_decode($raw, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $data : $default;
}

function writeJson(string $path, $data): void {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($path, $json, LOCK_EX);
}

function requireLogin(): array {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
    return $_SESSION['user'];
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function canViewAllData(array $user): bool {
    return in_array($user['role'] ?? '', ['admin', 'super_admin'], true);
}

function canViewUsers(array $user): bool {
    return in_array($user['role'] ?? '', ['admin', 'super_admin'], true);
}

function canManageUsers(array $user): bool {
    return ($user['role'] ?? '') === 'super_admin';
}

function loadUsers(): array {
    $users = readJson(USERS_FILE, []);
    return is_array($users) ? $users : [];
}

function saveUsers(array $users): void {
    writeJson(USERS_FILE, $users);
}

function findUserByEmail(array $users, string $email): ?array {
    $needle = strtolower(trim($email));
    foreach ($users as $user) {
        if (strtolower($user['email'] ?? '') === $needle) {
            return $user;
        }
    }
    return null;
}

function normalizeSettings(array $settings, int $userId): array {
    if (array_key_exists('target', $settings) && !array_key_exists('targets', $settings)) {
        $settings = [
            'targets' => [
                (string)$userId => $settings['target']
            ]
        ];
        writeJson(SETTINGS_FILE, $settings);
    }

    if (!isset($settings['targets']) || !is_array($settings['targets'])) {
        $settings['targets'] = [];
    }

    return $settings;
}
