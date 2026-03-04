-- Task06: Система управления станцией технического обслуживания автомобилей
-- Нормализованная схема БД с ограничениями целостности

-- Таблица сотрудников (мастера)
CREATE TABLE employees (
    employee_id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    specialization TEXT NOT NULL,
    hire_date DATE NOT NULL DEFAULT CURRENT_DATE,
    is_active INTEGER NOT NULL DEFAULT 1 CHECK (is_active IN (0, 1)),
    hourly_rate REAL NOT NULL CHECK (hourly_rate > 0),
    UNIQUE(first_name, last_name)
);

-- Таблица категорий автомобилей
CREATE TABLE car_categories (
    category_id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_name TEXT NOT NULL UNIQUE,
    category_code TEXT NOT NULL UNIQUE
);

-- Таблица услуг (справочник по категориям автомобилей)
CREATE TABLE services (
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_name TEXT NOT NULL,
    category_id INTEGER NOT NULL,
    duration_hours REAL NOT NULL CHECK (duration_hours > 0),
    price REAL NOT NULL CHECK (price > 0),
    FOREIGN KEY (category_id) REFERENCES car_categories(category_id) ON DELETE RESTRICT,
    UNIQUE(service_name, category_id)
);

-- Таблица предварительных записей (booking)
CREATE TABLE bookings (
    booking_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    specialization_required TEXT NOT NULL,
    is_confirmed INTEGER NOT NULL DEFAULT 0 CHECK (is_confirmed IN (0, 1)),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE RESTRICT
);

-- Таблица выполненных работ (работы, выполненные мастерами)
CREATE TABLE completed_work (
    work_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    work_date DATE NOT NULL,
    hours_spent REAL NOT NULL CHECK (hours_spent > 0),
    actual_price REAL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE RESTRICT
);

-- Таблица зарплаты (расчет заработной платы)
CREATE TABLE salary_calculations (
    salary_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    total_hours REAL NOT NULL DEFAULT 0 CHECK (total_hours >= 0),
    base_salary REAL NOT NULL DEFAULT 0 CHECK (base_salary >= 0),
    bonus_percentage REAL NOT NULL DEFAULT 0 CHECK (bonus_percentage >= 0),
    total_salary REAL NOT NULL DEFAULT 0 CHECK (total_salary >= 0),
    calculation_date DATE NOT NULL DEFAULT CURRENT_DATE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    UNIQUE(employee_id, period_start, period_end)
);

-- Таблица статистических отчетов
CREATE TABLE statistics_reports (
    report_id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_date DATE NOT NULL,
    total_revenue REAL NOT NULL DEFAULT 0,
    total_hours_worked REAL NOT NULL DEFAULT 0,
    total_salary_paid REAL NOT NULL DEFAULT 0,
    average_service_price REAL NOT NULL DEFAULT 0,
    employees_count INTEGER NOT NULL DEFAULT 0
);

-- Индексы для оптимизации запросов
CREATE INDEX idx_employees_active ON employees(is_active);
CREATE INDEX idx_employees_specialization ON employees(specialization);
CREATE INDEX idx_services_category ON services(category_id);
CREATE INDEX idx_bookings_employee ON bookings(employee_id);
CREATE INDEX idx_bookings_service ON bookings(service_id);
CREATE INDEX idx_bookings_date ON bookings(booking_date);
CREATE INDEX idx_completed_work_employee ON completed_work(employee_id);
CREATE INDEX idx_completed_work_service ON completed_work(service_id);
CREATE INDEX idx_completed_work_date ON completed_work(work_date);
CREATE INDEX idx_salary_employee ON salary_calculations(employee_id);
CREATE INDEX idx_salary_period ON salary_calculations(period_start, period_end);
CREATE INDEX idx_statistics_date ON statistics_reports(report_date);

-- ============================================================================
-- ТЕСТОВЫЕ ДАННЫЕ
-- ============================================================================

-- Вставка категорий автомобилей
INSERT INTO car_categories (category_name, category_code) VALUES 
('Легковые автомобили', 'CAR'),
('Внедорожники', 'SUV'),
('Микроавтобусы', 'VAN'),
('Грузовики', 'TRUCK');

-- Вставка сотрудников (мастеров)
INSERT INTO employees (first_name, last_name, specialization, hire_date, hourly_rate) VALUES 
('Иван', 'Петров', 'Электрик', '2022-01-15', 500.00),
('Сергей', 'Смирнов', 'Механик', '2021-06-20', 550.00),
('Алексей', 'Козлов', 'Слесарь', '2023-03-10', 480.00),
('Василий', 'Иванов', 'Кузовной мастер', '2020-11-05', 600.00),
('Юрий', 'Волков', 'Электрик', '2022-09-12', 520.00);

-- Вставка услуг
INSERT INTO services (service_name, category_id, duration_hours, price) VALUES 
('Техническое обслуживание', 1, 2.0, 3000.00),
('Замена масла', 1, 1.0, 1500.00),
('Диагностика двигателя', 1, 1.5, 2500.00),
('Ремонт тормозной системы', 2, 3.0, 5000.00),
('Замена фильтра воздуха', 2, 0.5, 800.00),
('Техническое обслуживание внедорожника', 2, 3.0, 4500.00),
('Техническое обслуживание микроавтобуса', 3, 4.0, 6000.00),
('Ремонт коробки передач', 4, 6.0, 8000.00),
('Техническое обслуживание грузовика', 4, 5.0, 7000.00),
('Кузовной ремонт', 1, 4.0, 6500.00);

-- Вставка выполненных работ
INSERT INTO completed_work (employee_id, service_id, work_date, hours_spent, actual_price) VALUES 
(2, 1, '2026-01-05', 2.0, 3000.00),
(1, 3, '2026-01-05', 1.5, 2500.00),
(4, 10, '2026-01-06', 3.5, 6000.00),
(2, 4, '2026-01-06', 2.5, 4500.00),
(3, 2, '2026-01-07', 1.0, 1500.00),
(5, 3, '2026-01-07', 1.5, 2500.00),
(2, 6, '2026-01-08', 3.0, 4500.00),
(1, 9, '2026-01-08', 4.5, 6500.00),
(4, 10, '2026-01-09', 4.0, 6500.00),
(3, 5, '2026-01-09', 0.5, 800.00);

-- Вставка предварительных записей
INSERT INTO bookings (employee_id, service_id, booking_date, booking_time, specialization_required, is_confirmed) VALUES 
(2, 1, '2026-01-20', '10:00', 'Механик', 1),
(1, 3, '2026-01-20', '14:00', 'Электрик', 1),
(4, 10, '2026-01-21', '09:00', 'Кузовной мастер', 0),
(5, 9, '2026-01-21', '15:00', 'Электрик', 0),
(3, 2, '2026-01-22', '11:00', 'Слесарь', 1);

-- Вставка расчетов зарплаты
INSERT INTO salary_calculations (employee_id, period_start, period_end, total_hours, base_salary, bonus_percentage, total_salary) VALUES 
(2, '2026-01-01', '2026-01-31', 22.0, 12100.00, 10, 13310.00),
(1, '2026-01-01', '2026-01-31', 18.0, 9000.00, 8, 9720.00),
(4, '2026-01-01', '2026-01-31', 20.0, 12000.00, 12, 13440.00),
(3, '2026-01-01', '2026-01-31', 14.5, 6960.00, 5, 7308.00),
(5, '2026-01-01', '2026-01-31', 16.5, 8580.00, 7, 9180.40);

-- Вставка статистических отчетов
INSERT INTO statistics_reports (report_date, total_revenue, total_hours_worked, total_salary_paid, average_service_price, employees_count) VALUES 
('2026-01-10', 27800.00, 18.0, 52958.40, 4158.33, 5),
('2026-01-31', 27800.00, 91.0, 52958.40, 4158.33, 5);
