<?php

/**
 * Script d'initialisation de la base de données
 * Crée automatiquement toutes les tables nécessaires si elles n'existent pas encore
 * A exécuter une seule fois (ou à chaque déploiement, les CREATE TABLE IF NOT EXISTS sont sans danger)
 *
 * Usage : php database.php  (en CLI)
 * ou visiter database.php dans le navigateur une fois, puis le supprimer/protéger
 */

require_once 'cnx.php';

try {

    // Table des utilisateurs
    $sqlUsers = "
        CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($sqlUsers);
    echo "Table 'users' prête." . PHP_EOL;

    // Table des tâches (todoliste), liée à l'utilisateur propriétaire
    $sqlTodo = "
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
                FOREIGN KEY (user_id) REFERENCES users(user_id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($sqlTodo);
    echo "Table 'todoliste' prête." . PHP_EOL;

    echo "Base de données initialisée avec succès." . PHP_EOL;

} catch (PDOException $e) {
    die("Erreur lors de la création des tables : " . $e->getMessage());
}
