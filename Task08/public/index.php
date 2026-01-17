<?php
require_once __DIR__ . '/Database.php';

$db = Database::getInstance();
$stmt = $db->prepare('SELECT employee_id, first_name, last_name, specialization FROM employees ORDER BY last_name, first_name');
$stmt->execute();
$employees = $stmt->fetchAll();

$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СТО - Управление мастерами</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 20px; }
        .message { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-right: 5px; cursor: pointer; border: none; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn:hover { opacity: 0.9; }
        .actions { white-space: nowrap; }
        .add-btn { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Мастера СТО</h1>
        
        <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Специализация</th>
                    <th class="actions">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                <tr>
                    <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                    <td><?= htmlspecialchars($emp['last_name']) ?></td>
                    <td><?= htmlspecialchars($emp['first_name']) ?></td>
                    <td><?= htmlspecialchars($emp['specialization']) ?></td>
                    <td class="actions">
                        <a href="employee_edit.php?id=<?= $emp['employee_id'] ?>" class="btn btn-warning">Редактировать</a>
                        <a href="employee_delete.php?id=<?= $emp['employee_id'] ?>" class="btn btn-danger">Удалить</a>
                        <a href="schedule.php?employee_id=<?= $emp['employee_id'] ?>" class="btn btn-info">График</a>
                        <a href="works.php?employee_id=<?= $emp['employee_id'] ?>" class="btn btn-primary">Выполненные работы</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="add-btn">
            <a href="employee_add.php" class="btn btn-success">Добавить мастера</a>
        </div>
    </div>
</body>
</html>
