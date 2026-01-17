<?php
require_once __DIR__ . '/Database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();

$stmt = $db->prepare('SELECT ws.*, e.first_name, e.last_name FROM work_schedule ws JOIN employees e ON ws.employee_id = e.employee_id WHERE ws.schedule_id = ?');
$stmt->execute([$id]);
$schedule = $stmt->fetch();

if (!$schedule) {
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
        $stmt = $db->prepare('UPDATE work_schedule SET day_of_week = ?, start_time = ?, end_time = ? WHERE schedule_id = ?');
        $stmt->execute([$dayOfWeek, $startTime, $endTime, $id]);
        header('Location: schedule.php?employee_id=' . $schedule['employee_id'] . '&message=График обновлён');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать график</title>
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
        .btn-warning { background: #ffc107; color: #333; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редактировать график</h1>
        <p class="subtitle"><?= htmlspecialchars($schedule['last_name'] . ' ' . $schedule['first_name']) ?></p>
        
        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="day_of_week">День недели</label>
                <select id="day_of_week" name="day_of_week" required>
                    <?php foreach ($daysOfWeek as $num => $name): ?>
                    <option value="<?= $num ?>" <?= (($_POST['day_of_week'] ?? $schedule['day_of_week']) == $num) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_time">Начало работы</label>
                <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($_POST['start_time'] ?? $schedule['start_time']) ?>" required>
            </div>
            <div class="form-group">
                <label for="end_time">Окончание работы</label>
                <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($_POST['end_time'] ?? $schedule['end_time']) ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-warning">Сохранить</button>
                <a href="schedule.php?employee_id=<?= $schedule['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
