-- Select the correct database
CREATE DATABASE quizpro;
USE quizpro;

-- Create `users` table for user management with a points column
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `games` table for storing game details with username instead of user_id
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `games_username_fk` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `game_sessions` table for managing active game sessions with username instead of user_id
CREATE TABLE `game_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `host_username` varchar(255) NOT NULL,
  `session_code` varchar(9) NOT NULL,
  `game_state` enum('waiting','in_progress','finished') DEFAULT 'waiting',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_code` (`session_code`),
  KEY `game_id` (`game_id`),
  KEY `host_username` (`host_username`),
  CONSTRAINT `game_sessions_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `game_sessions_ibfk_2` FOREIGN KEY (`host_username`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `game_players` table to track players in game sessions, keeping track of points
CREATE TABLE `game_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0, -- Players' points in the session
  `joined_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `game_players_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `game_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `game_players_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `questions` table for storing game questions
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `answer_a` varchar(255) NOT NULL,
  `answer_b` varchar(255) NOT NULL,
  `answer_c` varchar(255) NOT NULL,
  `answer_d` varchar(255) NOT NULL,
  `correct_answer` char(1) NOT NULL, -- This is the correct answer (a, b, c, or d)
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `game_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_code` varchar(9) NOT NULL,
  `username` varchar(255) NOT NULL,
  `joined_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `session_code` (`session_code`),
  KEY `username` (`username`),
  CONSTRAINT `game_participants_ibfk_1` FOREIGN KEY (`session_code`) REFERENCES `game_sessions` (`session_code`) ON DELETE CASCADE,
  CONSTRAINT `game_participants_ibfk_2` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
