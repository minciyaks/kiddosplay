-- 1. Create the Database
-- You can rename 'kiddosplay_db' if you prefer a different name.
CREATE DATABASE kiddosplay_db;
USE kiddosplay_db;

-- 2. users Table
-- Stores primary information for every user (parents, teachers, admins), login details, role, and age. 
CREATE TABLE users (
    user_id INT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher', 'parent', 'admin'),
    status ENUM('pending', 'approved', 'rejected'),
    age INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. certificates Table
-- Stores verification documents (like birth certificates or school IDs).
CREATE TABLE certificates (
    certificate_id INT PRIMARY KEY,
    user_id INT,
    file_path VARCHAR(255) NOT NULL,
    document_type ENUM('birth_certificate', 'school_id') NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected'),
    reviewed_by INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 4. activity_log Table
-- Tracks the time a user spends in each main section of the application for the parent dashboard. 
CREATE TABLE activity_log (
    log_id INT PRIMARY KEY,
    user_id INT,
    section_name VARCHAR(100) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 5. game_scores Table
-- Records the results of games played in the "Brain Play" (quiz) section. 
CREATE TABLE game_scores (
    score_id INT PRIMARY KEY,
    user_id INT,
    question_id INT,
    score INT NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    attempts INT,
    played_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 6. drawings Table
-- Stores the file paths to the pictures created by users in the "Color Fun" (drawing) section. 
CREATE TABLE drawings (
    drawing_id INT PRIMARY KEY,
    user_id INT,
    image_path VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 7. phonics_progress Table
-- Tracks a user's progress in various learning categories, like letters ("Letter Beats"). 
CREATE TABLE phonics_progress (
    phonics_id INT PRIMARY KEY,
    user_id INT,
    category VARCHAR(50) NOT NULL,
    value VARCHAR(255) NOT NULL,
    status ENUM('started', 'completed') NOT NULL,
    completed_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 8. stories_read Table
-- A simple log of which stories a user has opened in the "Story Land" module. 
CREATE TABLE stories_read (
    story_log_id INT PRIMARY KEY,
    user_id INT,
    story_title VARCHAR(255) NOT NULL,
    read_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 9. music_play_log Table
-- A simple log of which songs a user has listened to in the "Tune Town" (music) module. 
CREATE TABLE music_play_log (
    music_log_id INT PRIMARY KEY,
    user_id INT,
    song_title VARCHAR(255) NOT NULL,
    played_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);