
-- Base de données pour l'application de passage d'examen en ligne

CREATE DATABASE IF NOT EXISTS exam_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_app;

-- Table utilisateurs (étudiants/enseignants simplifiés)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('student', 'teacher') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table examens
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    duration INT, -- en minutes
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- Table questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    content TEXT,
    type ENUM('text', 'multiple_choice', 'true_false') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id)
);

-- Table réponses possibles (pour les QCM)
CREATE TABLE choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    label VARCHAR(255),
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Table réponses des étudiants
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question_id INT,
    answer_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
