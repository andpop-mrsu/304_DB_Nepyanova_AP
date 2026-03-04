-- Task05: Добавление новых данных

-- Добавление 5 новых пользователей (Moiseev и 4 соседей по группе)
-- Используем подзапрос для получения максимального ID чтобы не зависеть от текущих данных

-- Пользователь 1: Moiseev (самого себя)
INSERT INTO users (id, first_name, last_name, email, gender_id, registration_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM users) + 1, 'Oleg', 'Moiseev', 'moiseev.om@example.com', 1, CURRENT_TIMESTAMP;

-- Пользователь 2: Сосед 1
INSERT INTO users (id, first_name, last_name, email, gender_id, registration_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM users) + 1, 'Ivan', 'Petrov', 'ivanov.iv@example.com', 1, CURRENT_TIMESTAMP;

-- Пользователь 3: Сосед 2
INSERT INTO users (id, first_name, last_name, email, gender_id, registration_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM users) + 1, 'Anna', 'Smirnova', 'smirnova.an@example.com', 2, CURRENT_TIMESTAMP;

-- Пользователь 4: Сосед 3
INSERT INTO users (id, first_name, last_name, email, gender_id, registration_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM users) + 1, 'Sergei', 'Kozlov', 'kozlov.sg@example.com', 1, CURRENT_TIMESTAMP;

-- Пользователь 5: Сосед 4
INSERT INTO users (id, first_name, last_name, email, gender_id, registration_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM users) + 1, 'Marina', 'Volkova', 'volkova.mr@example.com', 2, CURRENT_TIMESTAMP;

-- Добавление 3 новых фильмов разных жанров
INSERT INTO movies (id, title, release_year)
SELECT (SELECT COALESCE(MAX(id), 0) FROM movies) + 1, 'The Future of Cinema', 2024;

INSERT INTO movies (id, title, release_year)
SELECT (SELECT COALESCE(MAX(id), 0) FROM movies) + 1, 'Last Summer Romance', 2023;

INSERT INTO movies (id, title, release_year)
SELECT (SELECT COALESCE(MAX(id), 0) FROM movies) + 1, 'Space Odyssey Quest', 2024;

-- Добавление жанров для новых фильмов
-- Фильм 1: The Future of Cinema - Drama
INSERT INTO movie_genres (movie_id, genre_id)
SELECT (SELECT MAX(id) FROM movies WHERE title = 'The Future of Cinema'), 8;

-- Фильм 2: Last Summer Romance - Romance
INSERT INTO movie_genres (movie_id, genre_id)
SELECT (SELECT MAX(id) FROM movies WHERE title = 'Last Summer Romance'), 15;

-- Фильм 3: Space Odyssey Quest - Sci-Fi
INSERT INTO movie_genres (movie_id, genre_id)
SELECT (SELECT MAX(id) FROM movies WHERE title = 'Space Odyssey Quest'), 16;

-- Добавление 3 новых отзывов от пользователя Moiseev о добавленных фильмах
INSERT INTO reviews (id, user_id, movie_id, rating, review_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM reviews) + 1, 
        (SELECT id FROM users WHERE last_name = 'Moiseev' AND first_name = 'Oleg' LIMIT 1),
        (SELECT id FROM movies WHERE title = 'The Future of Cinema'),
        4.5, CURRENT_TIMESTAMP;

INSERT INTO reviews (id, user_id, movie_id, rating, review_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM reviews) + 1,
        (SELECT id FROM users WHERE last_name = 'Moiseev' AND first_name = 'Oleg' LIMIT 1),
        (SELECT id FROM movies WHERE title = 'Last Summer Romance'),
        3.5, CURRENT_TIMESTAMP;

INSERT INTO reviews (id, user_id, movie_id, rating, review_date)
SELECT (SELECT COALESCE(MAX(id), 0) FROM reviews) + 1,
        (SELECT id FROM users WHERE last_name = 'Moiseev' AND first_name = 'Oleg' LIMIT 1),
        (SELECT id FROM movies WHERE title = 'Space Odyssey Quest'),
        5.0, CURRENT_TIMESTAMP;
