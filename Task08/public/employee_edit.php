<?php
require_once __DIR__ . '/Database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    
    if ($firstName === '' || $lastName === '' || $specialization === '') {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $stmt = $db->prepare('UPDATE employees SET first_name = ?, last_name = ?, specialization = ? WHERE employee_id = ?');
        $stmt->execute([$firstName, $lastName, $specialization, $id]);
        header('Location: index.php?message=Данные мастера обновлены');
        exit;
    }
}

$stmt = $db->prepare('SELECT * FROM employees WHERE employee_id = ?');
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать мастера</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 20px; }
        .error { padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редактировать мастера</h1>
        
        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="last_name">Фамилия</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? $employee['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="first_name">Имя</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? $employee['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="specialization">Специализация</label>
                <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($_POST['specialization'] ?? $employee['specialization']) ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-warning">Сохранить</button>
                <a href="index.php" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
