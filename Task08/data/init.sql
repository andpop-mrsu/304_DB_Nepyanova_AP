-- Task08: База данных СТО

CREATE TABLE IF NOT EXISTS employees (
    employee_id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    specialization TEXT NOT NULL,
    hire_date DATE NOT NULL DEFAULT CURRENT_DATE,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1)),
    hourly_rate REAL NOT NULL CHECK (hourly_rate > 0),
    UNIQUE(first_name, last_name)
);

CREATE TABLE IF NOT EXISTS work_schedule (
    schedule_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    day_of_week INTEGER NOT NULL CHECK (day_of_week BETWEEN 1 AND 7),
    start_time TEXT NOT NULL,
    end_time TEXT NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    UNIQUE(employee_id, day_of_week)
);

CREATE TABLE IF NOT EXISTS services (
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_name TEXT NOT NULL,
    duration_hours REAL NOT NULL CHECK (duration_hours > 0),
    price REAL NOT NULL CHECK (price > 0)
);

CREATE TABLE IF NOT EXISTS completed_work (
    work_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    work_date DATE NOT NULL,
    hours_spent REAL NOT NULL CHECK (hours_spent > 0),
    actual_price REAL NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE RESTRICT
);

-- Тестовые данные
INSERT OR IGNORE INTO employees (first_name, last_name, specialization, hire_date, hourly_rate) VALUES 
('Иван', 'Петров', 'Электрик', '2022-01-15', 500.00),
('Сергей', 'Смирнов', 'Механик', '2021-06-20', 550.00),
('Алексей', 'Козлов', 'Слесарь', '2023-03-10', 480.00),
('Василий', 'Иванов', 'Кузовной мастер', '2020-11-05', 600.00),
('Юрий', 'Волков', 'Электрик', '2022-09-12', 520.00);

INSERT OR IGNORE INTO services (service_name, duration_hours, price) VALUES 
('Техническое обслуживание', 2.0, 3000.00),
('Замена масла', 1.0, 1500.00),
('Диагностика двигателя', 1.5, 2500.00),
('Ремонт тормозной системы', 3.0, 5000.00),
('Кузовной ремонт', 4.0, 6500.00);

INSERT OR IGNORE INTO work_schedule (employee_id, day_of_week, start_time, end_time) VALUES 
(1, 1, '09:00', '18:00'),
(1, 2, '09:00', '18:00'),
(1, 3, '09:00', '18:00'),
(2, 1, '08:00', '17:00'),
(2, 2, '08:00', '17:00'),
(2, 4, '08:00', '17:00'),
(3, 2, '10:00', '19:00'),
(3, 3, '10:00', '19:00'),
(3, 5, '10:00', '19:00');

INSERT OR IGNORE INTO completed_work (employee_id, service_id, work_date, hours_spent, actual_price) VALUES 
(1, 3, '2026-01-05', 1.5, 2500.00),
(1, 1, '2026-01-08', 2.0, 3000.00),
(2, 1, '2026-01-05', 2.0, 3000.00),
(2, 4, '2026-01-06', 2.5, 4500.00),
(3, 2, '2026-01-07', 1.0, 1500.00),
(4, 5, '2026-01-06', 3.5, 6000.00),
(4, 5, '2026-01-09', 4.0, 6500.00),
(5, 3, '2026-01-07', 1.5, 2500.00);
