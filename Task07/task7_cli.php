<?php
/**
 * Task07: CLI приложение для вывода услуг от мастеров СТО
 */

const DB_PATH = __DIR__ . '/../service_station.db';

class ServiceStationCLI {
    private PDO $db;

    public function __construct() {
        $this->connectToDatabase();
    }

    private function connectToDatabase(): void {
        try {
            $this->db = new PDO('sqlite:' . DB_PATH);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->exitWithError('Database connection failed: ' . $e->getMessage());
        }
    }

    public function run(): void {
        $this->clearScreen();
        $this->displayHeader();
        $employeeId = $this->selectEmployee();
        $this->displayServices($employeeId);
    }

    private function clearScreen(): void {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }

    private function displayHeader(): void {
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║      Список оказанных услуг мастерами СТО                    ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
    }

    private function selectEmployee(): ?int {
        $employees = $this->fetchEmployees();
        
        if (empty($employees)) {
            $this->exitWithError('No employees found in database.');
        }

        echo "Выберите мастера для фильтрации:\n";
        echo "────────────────────────────────────────────────────────────────\n";
        
        foreach ($employees as $emp) {
            printf(" %2d. %s %s (%s)\n", 
                $emp['employee_id'], 
                $emp['first_name'], 
                $emp['last_name'], 
                $emp['specialization']
            );
        }
        
        echo "  0. Все мастера\n";
        echo "────────────────────────────────────────────────────────────────\n";
        
        $input = trim(fgets(STDIN));
        
        if ($input === '') {
            return null;
        }

        $employeeId = (int)$input;
        
        if ($employeeId === 0) {
            return null;
        }

        $validIds = array_column($employees, 'employee_id');
        if (!in_array($employeeId, $validIds, true)) {
            $this->exitWithError('Invalid employee ID.');
        }

        return $employeeId;
    }

    private function fetchEmployees(): array {
        try {
            $stmt = $this->db->prepare('SELECT employee_id, first_name, last_name, specialization FROM employees ORDER BY last_name, first_name');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->exitWithError('Failed to fetch employees: ' . $e->getMessage());
        }
    }

    private function displayServices(?int $employeeId): void {
        $services = $this->fetchServices($employeeId);
        
        if (empty($services)) {
            echo "\nНет данных для отображения.\n";
            return;
        }

        $this->printServicesTable($services);
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
            $this->exitWithError('Failed to fetch services: ' . $e->getMessage());
        }
    }

    private function printServicesTable(array $services): void {
        $columnWidths = [4, 15, 15, 12, 30, 10];
        
        echo "\n";
        $this->printTableLine($columnWidths);
        $this->printTableHeader();
        $this->printTableLine($columnWidths);
        
        foreach ($services as $row) {
            printf("| %2d  | %-13s | %-13s | %10s | %-28s | %8.2f |\n",
                $row['employee_id'],
                mb_substr($row['first_name'], 0, 13),
                mb_substr($row['last_name'], 0, 13),
                $row['work_date'],
                mb_substr($row['service_name'], 0, 28),
                (float)$row['actual_price']
            );
        }
        
        $this->printTableLine($columnWidths);
        echo "\n";
    }

    private function printTableLine(array $widths): void {
        echo '+';
        foreach ($widths as $width) {
            echo str_repeat('-', $width + 2) . '+';
        }
        echo "\n";
    }

    private function printTableHeader(): void {
        printf("| %-2s  | %-13s | %-13s | %-10s | %-28s | %-8s |\n",
            'ID', 'Имя', 'Фамилия', 'Дата', 'Услуга', 'Цена'
        );
    }

    private function exitWithError(string $message): void {
        echo "\nОшибка: $message\n";
        exit(1);
    }
}

$app = new ServiceStationCLI();
$app->run();
