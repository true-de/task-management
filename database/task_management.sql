-- Create the database
CREATE DATABASE IF NOT EXISTS task_management;
USE task_management;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    `task_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `deadline` datetime DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'low',
  `assignee_id` int DEFAULT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`task_id`)
);

-- Insert sample users
INSERT INTO users (username, password, full_name ) VALUES
('admin', '$2y$10$GEhJKZ0UOdxgEcK1hXWOyOUNDaVSoiN8qwUlZ/Wg2AYBHBipyvxae', 'Admin User'), -- password: admin123
('john', '$2y$10$RuXrNHz0aLaBybdCz9wfXeMRFbZ3W9uYNrPyG0iZ5.u5I3Z65ioW.', 'John Doe'), -- password: john123
('jane', '$2y$10$u4RfxQNHVn3F52Xrj0Y5EO8eH3GIV0VUVvubwV8WyAudvCJtGnm9q', 'Jane Smith'); -- password: jane123

-- Insert sample tasks
INSERT INTO tasks (title, description, deadline, priority, completed, assignee_id) VALUES
('Complete project documentation', 'Create comprehensive documentation for the current project', DATE_ADD(NOW(), INTERVAL 7 DAY), 'high', 0, 1),
('Fix login bug', 'Fix the authentication issue in the login form', DATE_ADD(NOW(), INTERVAL 2 DAY), 'high', 0, 2),
('Design new landing page', 'Create a modern design for the website landing page', DATE_ADD(NOW(), INTERVAL 14 DAY), 'medium', 0, 3),
('Weekly team meeting', 'Prepare agenda for the weekly team meeting', DATE_ADD(NOW(), INTERVAL 1 DAY), 'medium', 0, 1),
('Code review', 'Review pull requests from the development team', DATE_ADD(NOW(), INTERVAL 3 DAY), 'low', 1, 2),
('Optimize database queries', 'Improve performance of slow database queries', DATE_ADD(NOW(), INTERVAL 5 DAY), 'high', 0, NULL),
('Update dependencies', 'Update project dependencies to the latest versions', DATE_ADD(NOW(), INTERVAL 10 DAY), 'low', 0, 3);