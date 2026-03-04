<?php
require_once __DIR__ . '/Database.php';

$employeeId = $_GET['employee_id'] ?? null;
if (!$employeeId) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();

$stmt = $db->prepare('SELECT * FROM employees WHERE employee_id = ?');
$stmt->execute([$employeeId]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: index.php');
    exit;
}

$stmt = $db->query('SELECT * FROM services ORDER BY service_name');
$services = $stmt->fetchAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = (int)($_POST['service_id'] ?? 0);
    $workDate = trim($_POST['work_date'] ?? '');
    
    if ($serviceId <= 0 || $workDate === '') {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $stmt = $db->prepare('INSERT INTO completed_work (employee_id, service_id, work_date) VALUES (?, ?, ?)');
        $stmt->execute([$employeeId, $serviceId, $workDate]);
        header('Location: works.php?employee_id=' . $employeeId . '&message=Работа добавлена');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить выполненную работу</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 5px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        .error { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        select, input[type="date"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добавить выполненную работу</h1>
        <p class="subtitle"><?= htmlspecialchars($employee['last_name'] . ' ' . $employee['first_name']) ?></p>
        
        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="service_id">Услуга</label>
                <select id="service_id" name="service_id" required>
                    <option value="">Выберите услугу</option>
                    <?php foreach ($services as $service): ?>
                    <option value="<?= $service['service_id'] ?>" <?= (($_POST['service_id'] ?? '') == $service['service_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['service_name']) ?> (<?= number_format($service['price'], 2, ',', ' ') ?> ₽)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="work_date">Дата выполнения</label>
                <input type="date" id="work_date" name="work_date" value="<?= htmlspecialchars($_POST['work_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-success">Добавить</button>
                <a href="works.php?employee_id=<?= $employeeId ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
