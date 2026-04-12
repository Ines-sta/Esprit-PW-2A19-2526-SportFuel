-- Création de la base de données
CREATE DATABASE IF NOT EXISTS sportfuel COLLATE utf8mb4_unicode_ci;
USE sportfuel;

-- Création de la table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    age INT DEFAULT 0,
    poids FLOAT DEFAULT 0,
    taille FLOAT DEFAULT 0,
    sport_pratique VARCHAR(100) DEFAULT 'Aucun',
    objectif VARCHAR(100) DEFAULT 'Non défini',
    niveau VARCHAR(100) DEFAULT 'Débutant',
    seances_semaine INT DEFAULT 0,
    role VARCHAR(50) DEFAULT 'Sportif',
    statut VARCHAR(50) DEFAULT 'Actif',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
