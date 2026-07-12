-- ==========================================
-- Création de la base de données (optionnel)
-- ==========================================
CREATE DATABASE IF NOT EXISTS todolist_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE todolist_db;

-- ==========================================
-- Table des utilisateurs
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- Table des tâches
-- ==========================================
CREATE TABLE IF NOT EXISTS todoliste (
    todo_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    todo_name VARCHAR(255) NOT NULL,
    todo_reset TINYINT(1) DEFAULT 0,
    start_date VARCHAR(20) DEFAULT NULL,
    start_time VARCHAR(20) DEFAULT NULL,
    start VARCHAR(30) DEFAULT NULL,
    end_date VARCHAR(20) DEFAULT NULL,
    end_time VARCHAR(20) DEFAULT NULL,
    end VARCHAR(30) DEFAULT NULL,
    duration VARCHAR(30) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_todoliste_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;