<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

// Auto login por cookie
if (!currentUser() && !empty($_COOKIE['remember_user'])) {

    $users = loadUsers();
    $user = findUserByEmail($users, $_COOKIE['remember_user']);

    if ($user) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => (string)$user['name'],
            'email' => (string)$user['email'],
            'role' => (string)$user['role'],
        ];

        header('Location: index.php');
        exit;
    }
}

if (currentUser()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $users = loadUsers();
        $user = findUserByEmail($users, $email);

        if ($user && password_verify($password, (string)($user['password'] ?? ''))) {
            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'name' => (string)$user['name'],
                'email' => (string)$user['email'],
                'role' => (string)$user['role'],
            ];

            // recordar sesión
            if (!empty($_POST['remember'])) {
                setcookie(
                    'remember_user',
                    (string)$user['email'],
                    time() + (60 * 60 * 24 * 30),
                    "/",
                    "",
                    false,
                    true
                );
            }

            header('Location: index.php');
            exit;
        }
    }

    $error = 'Credenciales incorrectas.';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Iniciar sesión</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>

<body class="bg-dark text-light">

<div class="container py-5" style="max-width: 420px;">
    <div class="card bg-dark border-secondary">
        <div class="card-body">
            <h1 class="h5 mb-4">Iniciar sesión</h1>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger py-2">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <label class="form-label text-secondary">Email</label>
                <input type="email"
                       name="email"
                       class="form-control bg-black text-white border-secondary"
                       placeholder="tu@email.com"
                       required
                       autofocus>

                <label class="form-label text-secondary mt-3">Contraseña</label>
                <input type="password"
                       name="password"
                       class="form-control bg-black text-white border-secondary"
                       placeholder="••••••••"
                       required>

                <div class="form-check mt-3">
                    <input class="form-check-input"
                           type="checkbox"
                           name="remember"
                           id="remember">
                    <label class="form-check-label" for="remember">
                        Recordarme
                    </label>
                </div>

                <button class="btn btn-primary w-100 mt-4">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
