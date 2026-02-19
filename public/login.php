<?php
$error = isset($error) ? (string)$error : '';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/x-icon" href="/m-tabla/assets/img/logo.ico">

<style>
body{
    background:#0b0d10;
}
.login-card{
    max-width:420px;
    width:100%;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="login-card">

    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-body p-4">

            <!-- LOGO -->
            <div class="text-center mb-4">
                <img src="/m-tabla/assets/img/logo.ico"
                     width="64"
                     height="64"
                     alt="Logo">
            </div>

            <h1 class="h5 text-center text-light mb-4">
                Iniciar sesión
            </h1>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger py-2">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <div class="mb-3">
                    <label class="form-label text-light">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control bg-black text-white border-secondary"
                           placeholder="tu@email.com"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Contraseña</label>
                    <input type="password"
                           name="password"
                           class="form-control bg-black text-white border-secondary"
                           placeholder="••••••••"
                           required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input"
                           type="checkbox"
                           name="remember"
                           id="remember">

                    <label class="form-check-label text-light" for="remember">
                        Recordarme
                    </label>
                </div>

                <button class="btn btn-primary w-100">
                    Entrar
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
