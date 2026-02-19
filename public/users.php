<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

$user = requireLogin();

if (!canViewUsers($user)) {
    header('Location: index.php');
    exit;
}

$maxUsers = 15;

$stmt = db()->query("
    SELECT id,name,email,role
    FROM users
    ORDER BY id
");

$users = $stmt->fetchAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    if (!canManageUsers($user)) {
        $error = 'No tienes permisos para crear usuarios.';
    } else {
        $countStmt = db()->query("SELECT COUNT(*) FROM users");
        $totalUsers = (int)$countStmt->fetchColumn();

        if ($totalUsers >= $maxUsers) {
            $error = 'Límite de usuarios alcanzado.';
        } else {
            $name = trim((string)($_POST['name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $role = (string)($_POST['role'] ?? 'user');

            if ($name === '' || $email === '' || $password === '') {
                $error = 'Completa todos los campos.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email no válido.';
            } elseif (!in_array($role, ['super_admin', 'admin', 'user'], true)) {
                $error = 'Rol no válido.';
            } else {
                $existsStmt = db()->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
                $existsStmt->execute(['email' => $email]);

                if ($existsStmt->fetch()) {
                    $error = 'El email ya existe.';
                } else {
                    $stmt = db()->prepare("
                        INSERT INTO users (name,email,password,role)
                        VALUES (:name,:email,:password,:role)
                    ");

                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => $role,
                    ]);

                    $success = 'Usuario creado.';

                    $stmt = db()->query("
                        SELECT id,name,email,role
                        FROM users
                        ORDER BY id
                    ");

                    $users = $stmt->fetchAll();
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <link rel="icon" type="image/x-icon" href="/m-tabla/assets/img/logo.ico">
  <title>Usuarios</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>

<body class="bg-dark text-light">

<nav class="navbar navbar-dark bg-dark border-bottom border-secondary">
  <div class="container-fluid">

    <span class="navbar-brand">
        Peso Tracker
    </span>

    <div class="d-flex align-items-center gap-3">

        <span class="text-secondary small">
            <?= htmlspecialchars((string)$user['name']) ?>
        </span>

        <a href="index.php" class="btn btn-sm btn-outline-light">
            Volver
        </a>

        <a href="logout.php" class="btn btn-sm btn-outline-danger">
            Salir
        </a>

    </div>
  </div>
</nav>

<div class="container py-3">

    <h1 class="h4 mb-3">Usuarios</h1>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger py-2">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success py-2">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (canManageUsers($user)): ?>
        <div class="card bg-dark border-secondary mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3 text-secondary">Crear usuario</h2>

                <form method="post">
                    <input type="hidden" name="action" value="create">

                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="name"
                           class="form-control bg-dark text-light border-secondary"
                           required>

                    <label class="form-label mt-2">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control bg-dark text-light border-secondary"
                           required>

                    <label class="form-label mt-2">Contraseña</label>
                    <input type="password"
                           name="password"
                           class="form-control bg-dark text-light border-secondary"
                           required>

                    <label class="form-label mt-2">Rol</label>
                    <select name="role" class="form-select bg-dark text-light border-secondary">
                        <option value="user">user</option>
                        <option value="admin">admin</option>
                        <option value="super_admin">super_admin</option>
                    </select>

                    <button class="btn btn-primary w-100 mt-3">
                        Crear
                    </button>

                    <div class="text-secondary small mt-2">
                        Límite de usuarios: <?= $maxUsers ?>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="card bg-dark border-secondary">
        <div class="card-body">
            <h2 class="h6 mb-3">Listado</h2>

            <div class="table-responsive">
                <table class="table table-dark table-sm align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= (int)$u['id'] ?></td>
                            <td><?= htmlspecialchars((string)$u['name']) ?></td>
                            <td><?= htmlspecialchars((string)$u['email']) ?></td>
                            <td><?= htmlspecialchars((string)$u['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
