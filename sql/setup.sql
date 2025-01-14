-- Create the database 'quizpro'
CREATE DATABASE IF NOT EXISTS quizpro;

-- Use the 'quizpro' database
USE quizpro;

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert an example user (username: hello, password: hello)
-- The password is hashed using SHA256 for this example
-- Hash of 'hello' is: 2cf24dba5fb0a30e26e83b2ac5b9e29e1b1707f8e9bfe8adf4b09ebbe4abfaad
INSERT INTO users (username, password) 
VALUES 
    ('hello', '2cf24dba5fb0a30e26e83b2ac5b9e29e1b1707f8e9bfe8adf4b09ebbe4abfaad');
