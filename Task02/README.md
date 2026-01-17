# Task02 - Подготовка скриптов для создания таблиц и добавления данных

## Описание

Этот проект содержит утилиту для создания и заполнения базы данных SQLite на основе исходных текстовых и CSV файлов.

## Требования к окружению

Для корректной работы скрипта необходимо установить:

### Обязательное:
- **Python 3.6 или выше** - для запуска скрипта `make_db_init.py`
- **SQLite 3.x** - для создания и работы с базой данных

### Установка на разные платформы:

#### Windows:
```
# Установка Python (если не установлен):
# Скачать с https://www.python.org/downloads/
# или через winget:
winget install Python.Python.3.12

# Установка SQLite:
winget install SQLite.SQLite
```

#### macOS:
```
# Через Homebrew:
brew install python3
brew install sqlite3
```

#### Linux (Ubuntu/Debian):
```
sudo apt-get update
sudo apt-get install python3 python3-pip sqlite3
```

#### Linux (Fedora/CentOS):
```
sudo yum install python3 python3-pip sqlite3
```

## Структура файлов

- `make_db_init.py` - Python скрипт для генерации SQL скрипта на основе исходных данных
- `db_init.bat` - Кроссплатформенный shell-скрипт для запуска процесса инициализации БД
- `db_init.sql` - Генерируемый SQL скрипт (создается при запуске)
- `movies_rating.db` - Созданная база данных SQLite (создается при запуске)
- `movies.csv` - Исходные данные о фильмах
- `ratings.csv` - Исходные данные о рейтингах
- `tags.csv` - Исходные данные о тегах
- `users.txt` - Исходные данные о пользователях
- `genres.txt` - Справочник жанров
- `occupation.txt` - Справочник профессий

## Использование

### На Unix-подобных системах (macOS, Linux):
```bash
chmod +x db_init.bat
./db_init.bat
```

### На Windows (в PowerShell):
```powershell
bash db_init.bat
```

Или непосредственно:
```powershell
python3 make_db_init.py
sqlite3 movies_rating.db < db_init.sql
```

## Процесс работы

1. **Скрипт make_db_init.py:**
   - Читает исходные CSV и TXT файлы
   - Парсит данные о фильмах, пользователях, рейтингах и тегах
   - Генерирует SQL скрипт `db_init.sql` со следующим содержимым:
     - DROP команды для удаления существующих таблиц
     - CREATE TABLE команды для создания таблиц
     - INSERT INTO команды для загрузки данных

2. **Скрипт db_init.bat:**
   - Запускает `make_db_init.py`
   - Передает сгенерированный SQL скрипт в sqlite3
   - Создает заполненную базу данных `movies_rating.db`

## Структура базы данных

### Таблица movies
- `id` (INTEGER PRIMARY KEY) - уникальный идентификатор фильма
- `title` (TEXT) - название фильма
- `year` (INTEGER) - год выпуска
- `genres` (TEXT) - жанры (разделены символом |)

### Таблица users
- `id` (INTEGER PRIMARY KEY) - уникальный идентификатор пользователя
- `name` (TEXT) - имя пользователя
- `email` (TEXT) - электронная почта
- `gender` (TEXT) - пол
- `register_date` (TEXT) - дата регистрации
- `occupation` (TEXT) - профессия

### Таблица ratings
- `id` (INTEGER PRIMARY KEY) - уникальный идентификатор рейтинга
- `user_id` (INTEGER) - идентификатор пользователя
- `movie_id` (INTEGER) - идентификатор фильма
- `rating` (REAL) - оценка (0.5 - 5.0)
- `timestamp` (INTEGER) - временная метка

### Таблица tags
- `id` (INTEGER PRIMARY KEY) - уникальный идентификатор тега
- `user_id` (INTEGER) - идентификатор пользователя
- `movie_id` (INTEGER) - идентификатор фильма
- `tag` (TEXT) - текст тега
- `timestamp` (INTEGER) - временная метка

## Примечания

- База данных автоматически удаляется и создается заново при каждом запуске скрипта
- Все данные обрабатываются с учетом кодировки UTF-8
- Специальные символы в текстовых полях автоматически экранируются
