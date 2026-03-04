# Описание структуры файлов данных

Этот каталог содержит набор данных о кинофильмах и оценках пользователей.

## Структура файлов

### movies.csv
Содержит информацию о фильмах.

Формат: CSV (значения, разделенные запятыми)

Столбцы:
- `movieId`: уникальный идентификатор фильма
- `title`: название фильма с годом выпуска
- `genres`: жанры фильма, разделенные символом `|`

Пример:
```
movieId,title,genres
1,Toy Story (1995),Adventure|Animation|Children|Comedy|Fantasy
2,Jumanji (1995),Adventure|Children|Fantasy
```

### ratings.csv
Содержит оценки фильмов, выставленные пользователями.

Формат: CSV (значения, разделенные запятыми)

Столбцы:
- `userId`: уникальный идентификатор пользователя
- `movieId`: уникальный идентификатор фильма
- `rating`: оценка (от 0.5 до 5.0)
- `timestamp`: время оценки в формате Unix timestamp

Пример:
```
userId,movieId,rating,timestamp
1,1,4.0,964982703
1,3,4.0,964981247
```

### tags.csv
Содержит теги (ключевые слова), которые пользователи назначили фильмам.

Формат: CSV (значения, разделенные запятыми)

Столбцы:
- `userId`: уникальный идентификатор пользователя
- `movieId`: уникальный идентификатор фильма
- `tag`: текст тега

### users.txt
Содержит информацию о пользователях.

### genres.txt
Содержит список доступных жанров.

### occupation.txt
Содержит список профессий пользователей.

## Статистика данных

### ratings.csv анализ
- Минимальный userId: 1 (232 оценки)
- Максимальный userId: 120 (22 оценки)

## Установленное ПО

### SQLite
- **Версия:** 3.51.2 2026-01-09 17:27:48 (64-bit)

#### Доступные режимы вывода:
- ascii - Columns/rows delimited by 0x1F and 0x1E
- box - Tables using unicode box-drawing characters
- csv - Comma-separated values
- column - Output in columns
- html - HTML table code
- insert - SQL insert statements
- json - Results in a JSON array
- line - One value per line
- list - Values delimited by "|"
- markdown - Markdown table format
- qbox - Shorthand for box formatting
- quote - Escape answers as for SQL
- table - ASCII-art table
- tabs - Tab-separated values
- tcl - TCL list elements
- `timestamp`: время создания тега в формате Unix timestamp

Пример:
```
userId,movieId,tag,timestamp
2,60756,funny,1445714994
2,60756,Highly quotable,1445714996
```

### genres.txt
Содержит список всех доступных жанров фильмов (по одному жанру на строку).

### users.txt
Содержит информацию о пользователях.

### occupation.txt
Содержит список категорий профессий пользователей.
