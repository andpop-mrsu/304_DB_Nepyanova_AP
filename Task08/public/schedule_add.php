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

$error = null;

$daysOfWeek = [
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dayOfWeek = (int)($_POST['day_of_week'] ?? 0);
    $startTime = trim($_POST['start_time'] ?? '');
    $endTime = trim($_POST['end_time'] ?? '');
    
    if ($dayOfWeek < 1 || $dayOfWeek > 7 || $startTime === '' || $endTime === '') {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $stmt = $db->prepare('INSERT INTO work_schedule (employee_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)');
        $stmt->execute([$employeeId, $dayOfWeek, $startTime, $endTime]);
        header('Location: schedule.php?employee_id=' . $employeeId . '&message=День добавлен в график');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить день в график</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 5px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        .error { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        select, input[type="time"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добавить день в график</h1>
        <p class="subtitle"><?= htmlspecialchars($employee['last_name'] . ' ' . $employee['first_name']) ?></p>
        
        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="day_of_week">День недели</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Выберите день</option>
                    <?php foreach ($daysOfWeek as $num => $name): ?>
                    <option value="<?= $num ?>" <?= (($_POST['day_of_week'] ?? '') == $num) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_time">Начало работы</label>
                <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($_POST['start_time'] ?? '09:00') ?>" required>
            </div>
            <div class="form-group">
                <label for="end_time">Окончание работы</label>
                <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($_POST['end_time'] ?? '18:00') ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-success">Добавить</button>
                <a href="schedule.php?employee_id=<?= $employeeId ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
