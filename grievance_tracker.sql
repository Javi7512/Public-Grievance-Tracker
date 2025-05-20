-- Create the database
CREATE DATABASE grievance_tracker;
USE grievance_tracker;

-- Create users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user'
);

-- Create grievances table
CREATE TABLE grievances (
  id INT AUTO_INCREMENT PRIMARY KEY,
  userId INT NOT NULL,
  category VARCHAR(50) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(255) NOT NULL,
  dateFiled DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending', 'in-progress', 'resolved') DEFAULT 'pending',
  department VARCHAR(100),
  imagePath VARCHAR(255),
  FOREIGN KEY (userId) REFERENCES users(id)
);

-- Create responses table
CREATE TABLE responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grievanceId INT NOT NULL,
  adminId INT NOT NULL,
  comment TEXT NOT NULL,
  statusUpdated ENUM('pending', 'in-progress', 'resolved') NOT NULL,
  department VARCHAR(100) NOT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (grievanceId) REFERENCES grievances(id),
  FOREIGN KEY (adminId) REFERENCES users(id)
);

-- Insert admin user
INSERT INTO users (username, name, email, password, role)
VALUES ('admin', 'Admin User', 'admin@example.com', '$2y$10$XA7QIV6XuQarq.R/nQcTY.uGDY8D9KanHjj5I7iZ9BKSb.n0vcUZG', 'admin');
-- Note: password is 'admin123' hashed with bcrypt

-- Insert sample categories (optional)
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value VARCHAR(50) NOT NULL UNIQUE,
  label VARCHAR(100) NOT NULL
);

INSERT INTO categories (value, label) VALUES
('road', 'Road Repair'),
('water', 'Water Supply'),
('garbage', 'Garbage Disposal'),
('transport', 'Public Transport'),
('electricity', 'Electricity Issues'),
('other', 'Other');

-- Insert sample departments (optional)
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value VARCHAR(50) NOT NULL UNIQUE,
  label VARCHAR(100) NOT NULL
);

INSERT INTO departments (value, label) VALUES
('public-works', 'Public Works Department'),
('water-authority', 'Water Authority'),
('sanitation', 'Sanitation Department'),
('transport-authority', 'Transport Authority'),
('electricity-board', 'Electricity Board'),
('municipal-office', 'Municipal Office');