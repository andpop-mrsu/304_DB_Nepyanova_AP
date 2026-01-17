-- Task05: Нормализованная база данных с ограничениями целостности

-- Удаление старых таблиц
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS movie_genres;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS genders;

-- Таблица справочник полов
CREATE TABLE genders (
    id INTEGER PRIMARY KEY CHECK (id IN (1, 2)),
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL
);

INSERT INTO genders (id, code, name) VALUES (1, 'M', 'Male');
INSERT INTO genders (id, code, name) VALUES (2, 'F', 'Female');

-- Таблица пользователей (нормализованная)
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    gender_id INTEGER NOT NULL CHECK (gender_id IN (1, 2)),
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gender_id) REFERENCES genders(id)
);

-- Таблица жанров (справочник)
CREATE TABLE genres (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

INSERT INTO genres (id, name) VALUES 
(1, 'Action'), (2, 'Adventure'), (3, 'Animation'), (4, 'Children'), 
(5, 'Comedy'), (6, 'Crime'), (7, 'Documentary'), (8, 'Drama'), 
(9, 'Fantasy'), (10, 'Film-Noir'), (11, 'Horror'), (12, 'IMAX'), 
(13, 'Musical'), (14, 'Mystery'), (15, 'Romance'), (16, 'Sci-Fi'), 
(17, 'Thriller'), (18, 'War'), (19, 'Western');

-- Таблица фильмов (нормализованная)
CREATE TABLE movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    release_year INTEGER CHECK (release_year > 0 AND release_year <= 2100)
);

-- Таблица связи фильмы-жанры (many-to-many)
CREATE TABLE movie_genres (
    movie_id INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE RESTRICT,
    FOREIGN KEY (genre_id) REFERENCES genres(id)
);

-- Таблица рецензий (отзывов)
CREATE TABLE reviews (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    rating REAL NOT NULL CHECK (rating >= 0.5 AND rating <= 5.0 AND (rating * 2) % 1 = 0),
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE RESTRICT,
    UNIQUE (user_id, movie_id)
);

-- Индексы для оптимизации поиска
CREATE INDEX idx_users_last_name ON users(last_name);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_movies_title ON movies(title);
CREATE INDEX idx_movies_year ON movies(release_year);
CREATE INDEX idx_reviews_user_id ON reviews(user_id);
CREATE INDEX idx_reviews_movie_id ON reviews(movie_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);
CREATE INDEX idx_movie_genres_genre ON movie_genres(genre_id);
