<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$user = requireLogin();

$weights = readJson(WEIGHTS_FILE, []);
$weights = is_array($weights) ? $weights : [];

$settings = readJson(SETTINGS_FILE, ['targets' => []]);
$settings = normalizeSettings(is_array($settings) ? $settings : [], (int)$user['id']);
$target = $settings['targets'][(string)$user['id']] ?? null;

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'add') {
    $date = trim((string)($_POST['date'] ?? ''));
    $weight = (float)($_POST['weight'] ?? 0);

    if ($date !== '' && $weight > 0) {
      $weights[] = [
        'user_id' => (int)$user['id'],
        'date' => $date,
        'weight' => round($weight, 2),
      ];

      usort($weights, function ($a, $b) {
        $dateCompare = strcmp($a['date'], $b['date']);
        if ($dateCompare !== 0) return $dateCompare;
        return ((int)($a['user_id'] ?? 0)) <=> ((int)($b['user_id'] ?? 0));
      });
      writeJson(WEIGHTS_FILE, $weights);
    }

  } elseif ($action === 'target') {
    $target = trim((string)($_POST['target'] ?? ''));
    $settings['targets'][(string)$user['id']] =
      ($target === '') ? null : round((float)$target, 2);
    writeJson(SETTINGS_FILE, $settings);
  }

  header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
  exit;
}

usort($weights, function ($a, $b) {
  $dateCompare = strcmp($a['date'], $b['date']);
  if ($dateCompare !== 0) return $dateCompare;
  return ((int)($a['user_id'] ?? 0)) <=> ((int)($b['user_id'] ?? 0));
});

$entriesForChart = array_values(array_filter(
  $weights,
  fn($e) => (int)($e['user_id'] ?? 0) === (int)$user['id']
));

$tableEntries = canViewAllData($user) ? $weights : $entriesForChart;

$usersById = [];
if (canViewAllData($user)) {
  foreach (loadUsers() as $u) {
    $usersById[(int)$u['id']] = $u['name'] ?? ('ID ' . $u['id']);
  }
}

// datos para JS
$labels = array_map(fn($e) => $e['date'], $entriesForChart);
$weightsData = array_map(fn($e) => (float)$e['weight'], $entriesForChart);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Registro de peso</title>

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

        <?php if (($user['role'] ?? '') === 'super_admin'): ?>
            <a href="users.php" class="btn btn-sm btn-outline-light">
                Usuarios
            </a>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-sm btn-outline-danger">
            Salir
        </a>

    </div>
  </div>
</nav>

<div class="container py-3">

    <h1 class="h4 mb-3">Registro de peso</h1>

    <div class="row g-3">

        <!-- FORMULARIOS -->
        <div class="col-12 col-lg-4">

            <div class="card bg-dark border-secondary">
                <div class="card-body">

                    <div class="mb-2">
                        <span class="badge bg-success-subtle text-success">
                            Objetivo:
                            <?= $target !== null ? $target . ' kg' : 'no definido' ?>
                        </span>
                    </div>

                    <form method="post" class="mb-3">
                        <input type="hidden" name="action" value="add">

                        <label class="form-label">Fecha</label>
                        <input type="date"
                               name="date"
                               class="form-control bg-dark text-light border-secondary"
                               value="<?= date('Y-m-d') ?>">

                        <label class="form-label mt-2">Peso (kg)</label>
                        <input type="number"
                               step="0.1"
                               name="weight"
                               class="form-control bg-dark text-light border-secondary">

                        <button class="btn btn-primary w-100 mt-3">
                            Guardar peso
                        </button>
                    </form>

                    <form method="post">
                        <input type="hidden" name="action" value="target">

                        <label class="form-label">Peso objetivo</label>
                        <input type="number"
                               step="0.1"
                               name="target"
                               class="form-control bg-dark text-light border-secondary"
                               value="<?= $target ?? '' ?>">

                        <button class="btn btn-outline-light w-100 mt-3">
                            Guardar objetivo
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- GRAFICA + LISTA -->
        <div class="col-12 col-lg-8">

            <div class="card bg-dark border-secondary mb-3">
                <div class="card-body">

                    <div class="chart-container">
                        <canvas id="chart"></canvas>
                    </div>

                </div>
            </div>

            <div class="card bg-dark border-secondary">
                <div class="card-body">

                    <h6 class="mb-3">Histórico</h6>

                    <div class="table-responsive">
                        <table class="table table-dark table-sm align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <?php if (canViewAllData($user)): ?>
                                    <th>Usuario</th>
                                <?php endif; ?>
                                <th class="text-end">Peso</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach (array_reverse($tableEntries) as $e): ?>
                                <tr>
                                    <td><?= $e['date'] ?></td>
                                    <?php if (canViewAllData($user)): ?>
                                        <td><?= htmlspecialchars((string)($usersById[(int)($e['user_id'] ?? 0)] ?? 'Desconocido')) ?></td>
                                    <?php endif; ?>
                                    <td class="text-end"><?= $e['weight'] ?> kg</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>

<script>
window.chartData = {
  labels: <?= json_encode($labels) ?>,
  weights: <?= json_encode($weightsData) ?>,
  target: <?= $target === null ? 'null' : json_encode($target) ?>
};
</script>

<script src="assets/js/chart.js"></script>

</body>
</html>