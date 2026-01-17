<?php
require_once __DIR__ . '/Database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();

$stmt = $db->prepare('SELECT * FROM employees WHERE employee_id = ?');
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Удаляем связанные записи
    $stmt = $db->prepare('DELETE FROM completed_work WHERE employee_id = ?');
    $stmt->execute([$id]);
    
    $stmt = $db->prepare('DELETE FROM work_schedule WHERE employee_id = ?');
    $stmt->execute([$id]);
    
    // Удаляем мастера
    $stmt = $db->prepare('DELETE FROM employees WHERE employee_id = ?');
    $stmt->execute([$id]);
    
    header('Location: index.php?message=Мастер удалён');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить мастера</title>
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
        <h1>Удалить мастера</h1>
        
        <div class="warning">
            Вы уверены, что хотите удалить мастера <strong><?= htmlspecialchars($employee['last_name'] . ' ' . $employee['first_name']) ?></strong>?<br>
            Также будут удалены все связанные записи графика и выполненных работ.
        </div>
        
        <form method="POST">
            <div class="buttons">
                <button type="submit" class="btn btn-danger">Удалить</button>
                <a href="index.php" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
