#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они оценили. В списке оставить первые 100 записей."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "SELECT DISTINCT u1.name AS user1, u2.name AS user2, m.title AS movie FROM ratings r1 JOIN ratings r2 ON r1.movie_id = r2.movie_id AND r1.user_id < r2.user_id JOIN users u1 ON r1.user_id = u1.id JOIN users u2 ON r2.user_id = u2.id JOIN movies m ON r1.movie_id = m.id ORDER BY m.title, u1.name, u2.name LIMIT 100;"
echo " "

echo "2. Найти 10 самых свежих оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "SELECT DISTINCT u.name, m.title, r.rating, date(r.timestamp, 'unixepoch') AS rating_date FROM ratings r JOIN users u ON r.user_id = u.id JOIN movies m ON r.movie_id = m.id ORDER BY r.timestamp DESC LIMIT 10;"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке Рекомендуем для фильмов должно быть написано Да или Нет."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "WITH avg_ratings AS (SELECT movie_id, AVG(rating) AS avg_rating FROM ratings GROUP BY movie_id), max_min AS (SELECT MAX(avg_rating) AS max_avg, MIN(avg_rating) AS min_avg FROM avg_ratings) SELECT m.title, m.year, ar.avg_rating, CASE WHEN ar.avg_rating = mm.max_avg THEN 'Да' ELSE 'Нет' END AS Рекомендуем FROM movies m JOIN avg_ratings ar ON m.id = ar.movie_id CROSS JOIN max_min mm WHERE ar.avg_rating = mm.max_avg OR ar.avg_rating = mm.min_avg ORDER BY m.year, m.title;"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-женщины в период с 2010 по 2012 год."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "SELECT COUNT(*) AS rating_count, AVG(r.rating) AS avg_rating FROM ratings r JOIN users u ON r.user_id = u.id WHERE u.gender = 'F' AND datetime(r.timestamp, 'unixepoch') BETWEEN '2010-01-01' AND '2012-12-31 23:59:59';"
echo " "

echo "5. Составить список фильмов с указанием их средней оценки и места в рейтинге по средней оценке. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "SELECT m.title, m.year, AVG(r.rating) AS avg_rating, RANK() OVER (ORDER BY AVG(r.rating) DESC) AS rating_rank FROM movies m LEFT JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year ORDER BY m.year, m.title LIMIT 20;"
echo " "

echo "6. Вывести список из 10 последних зарегистрированных пользователей в формате Фамилия Имя|Дата регистрации (сначала фамилия, потом имя)."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "SELECT name || '|' || register_date AS user_info FROM users ORDER BY register_date DESC LIMIT 10;"
echo " "

echo "7. С помощью рекурсивного CTE составить таблицу умножения для чисел от 1 до 10."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE mult(n, m, result) AS (SELECT 1, 1, 1 UNION ALL SELECT CASE WHEN m = 10 THEN n + 1 ELSE n END, CASE WHEN m = 10 THEN 1 ELSE m + 1 END, CASE WHEN m = 10 THEN (n + 1) * 1 ELSE n * (m + 1) END FROM mult WHERE n <= 10 AND NOT (n = 10 AND m = 10)) SELECT n || 'x' || m || '=' || result AS multiplication FROM mult;"
echo " "

echo "8. С помощью рекурсивного CTE выделить все жанры фильмов, имеющиеся в таблице movies (каждый жанр в отдельной строке)."
echo "===================================================="
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE split_genres(genre, rest) AS (SELECT CASE WHEN instr(genres, '|') > 0 THEN substr(genres, 1, instr(genres, '|') - 1) ELSE genres END, CASE WHEN instr(genres, '|') > 0 THEN substr(genres, instr(genres, '|') + 1) ELSE NULL END FROM movies UNION ALL SELECT CASE WHEN instr(rest, '|') > 0 THEN substr(rest, 1, instr(rest, '|') - 1) ELSE rest END, CASE WHEN instr(rest, '|') > 0 THEN substr(rest, instr(rest, '|') + 1) ELSE NULL END FROM split_genres WHERE rest IS NOT NULL) SELECT DISTINCT genre FROM split_genres WHERE genre IS NOT NULL AND genre != '' ORDER BY genre;"
