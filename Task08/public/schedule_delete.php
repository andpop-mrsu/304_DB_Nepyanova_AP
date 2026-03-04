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
    $stmt = $db->prepare('DELETE FROM work_schedule WHERE schedule_id = ?');
    $stmt->execute([$id]);
    header('Location: schedule.php?employee_id=' . $schedule['employee_id'] . '&message=День удалён из графика');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить день из графика</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 20px; }
        .warning { padding: 15px; border-radius: 4px; margin-bottom: 20px; background: #fff3cd; color: #856404; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Удалить день из графика</h1>
        
        <div class="warning">
            Вы уверены, что хотите удалить <strong><?= htmlspecialchars($daysOfWeek[$schedule['day_of_week']] ?? $schedule['day_of_week']) ?></strong> 
            (<?= htmlspecialchars($schedule['start_time']) ?> - <?= htmlspecialchars($schedule['end_time']) ?>) 
            для мастера <strong><?= htmlspecialchars($schedule['last_name'] . ' ' . $schedule['first_name']) ?></strong>?
        </div>
        
        <form method="POST">
            <div class="buttons">
                <button type="submit" class="btn btn-danger">Удалить</button>
                <a href="schedule.php?employee_id=<?= $schedule['employee_id'] ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
