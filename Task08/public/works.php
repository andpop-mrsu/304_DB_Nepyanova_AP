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

$stmt = $db->prepare('
    SELECT cw.*, s.service_name, s.price 
    FROM completed_work cw 
    JOIN services s ON cw.service_id = s.service_id 
    WHERE cw.employee_id = ? 
    ORDER BY cw.work_date DESC
');
$stmt->execute([$employeeId]);
$works = $stmt->fetchAll();

$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выполненные работы - <?= htmlspecialchars($employee['last_name'] . ' ' . $employee['first_name']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 5px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        .message { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; background: #d4edda; color: #155724; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-right: 5px; cursor: pointer; border: none; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
        .price { font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Выполненные работы</h1>
        <p class="subtitle"><?= htmlspecialchars($employee['last_name'] . ' ' . $employee['first_name'] . ' (' . $employee['specialization'] . ')') ?></p>
        
        <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if (empty($works)): ?>
        <p>Нет выполненных работ</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Услуга</th>
                    <th>Стоимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($works as $work): ?>
                <tr>
                    <td><?= htmlspecialchars($work['work_date']) ?></td>
                    <td><?= htmlspecialchars($work['service_name']) ?></td>
                    <td class="price"><?= htmlspecialchars(number_format($work['price'], 2, ',', ' ')) ?> ₽</td>
                    <td>
                        <a href="work_edit.php?id=<?= $work['work_id'] ?>" class="btn btn-warning">Редактировать</a>
                        <a href="work_delete.php?id=<?= $work['work_id'] ?>" class="btn btn-danger">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="work_add.php?employee_id=<?= $employeeId ?>" class="btn btn-success">Добавить работу</a>
            <a href="index.php" class="btn btn-secondary">Назад</a>
        </div>
    </div>
</body>
</html>
