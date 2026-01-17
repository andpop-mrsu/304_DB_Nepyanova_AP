<?php
const DB_PATH = __DIR__ . '/../service_station.db';

class ServiceStationWeb {
    private PDO $db;
    private ?int $selectedEmployeeId = null;
    private array $employees = [];
    private array $services = [];

    public function __construct() {
        $this->connectToDatabase();
        $this->handleRequest();
        $this->employees = $this->fetchEmployees();
        $this->services = $this->fetchServices($this->selectedEmployeeId);
    }

    private function connectToDatabase(): void {
        try {
            $this->db = new PDO('sqlite:' . DB_PATH);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
        }
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
            $employeeId = (int)$_POST['employee_id'];
            $this->selectedEmployeeId = $employeeId === 0 ? null : $employeeId;
        }
    }

    public function getEmployees(): array {
        return $this->employees;
    }

    public function getServices(): array {
        return $this->services;
    }

    public function getSelectedEmployeeId(): ?int {
        return $this->selectedEmployeeId;
    }

    private function fetchEmployees(): array {
        try {
            $stmt = $this->db->prepare('SELECT employee_id, first_name, last_name, specialization FROM employees ORDER BY last_name, first_name');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Failed to fetch employees: ' . htmlspecialchars($e->getMessage()));
        }
    }

    private function fetchServices(?int $employeeId): array {
        try {
            $query = '
                SELECT 
                    e.employee_id,
                    e.first_name,
                    e.last_name,
                    cw.work_date,
                    s.service_name,
                    cw.actual_price
                FROM completed_work cw
                JOIN employees e ON cw.employee_id = e.employee_id
                JOIN services s ON cw.service_id = s.service_id
            ';

            if ($employeeId !== null) {
                $query .= ' WHERE e.employee_id = :employee_id';
            }

            $query .= ' ORDER BY e.last_name, e.first_name, cw.work_date';

            $stmt = $this->db->prepare($query);
            
            if ($employeeId !== null) {
                $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Failed to fetch services: ' . htmlspecialchars($e->getMessage()));
        }
    }
}

$app = new ServiceStationWeb();
$employees = $app->getEmployees();
$services = $app->getServices();
$selectedEmployeeId = $app->getSelectedEmployeeId();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги СТО</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 25px; font-size: 14px; }
        .filter-section { display: flex; gap: 15px; margin-bottom: 30px; align-items: center; }
        label { font-weight: 600; color: #333; }
        select {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 300px;
        }
        select:focus { outline: none; border-color: #667eea; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        thead { background: #f8f9fa; border-top: 2px solid #667eea; border-bottom: 2px solid #667eea; }
        th { padding: 15px; text-align: left; font-weight: 600; color: #333; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tbody tr:hover { background: #f8f9fa; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Услуги СТО</h1>
        <p class="subtitle">Список оказанных услуг мастерами станции технического обслуживания</p>
        
        <div class="filter-section">
            <form method="POST">
                <label for="employee_id">Фильтр по мастеру:</label>
                <select name="employee_id" id="employee_id" onchange="this.form.submit()">
                    <option value="0">Все мастера</option>
                    <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['employee_id'] ?>" <?php if ($selectedEmployeeId === $emp['employee_id']): ?>selected<?php endif; ?>>
                        <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= htmlspecialchars($emp['specialization']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        
        <?php if (empty($services)): ?>
        <div class="empty-state">
            <p>Нет данных для отображения</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Дата</th>
                    <th>Услуга</th>
                    <th>Цена</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['employee_id']) ?></td>
                    <td><?= htmlspecialchars($service['first_name']) ?></td>
                    <td><?= htmlspecialchars($service['last_name']) ?></td>
                    <td><?= htmlspecialchars($service['work_date']) ?></td>
                    <td><?= htmlspecialchars($service['service_name']) ?></td>
                    <td><?= number_format((float)$service['actual_price'], 2, ',', ' ') ?> руб.</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
