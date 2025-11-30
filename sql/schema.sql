CREATE DATABASE IF NOT EXISTS gestor_inc;
USE gestor_inc;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tickets (
id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  status ENUM('abierta','cerrada') NOT NULL DEFAULT 'abierta'
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(50) NOT NULL,
  entity VARCHAR(50) NOT NULL,
  entity_id INT,
  user_id INT,
  details JSON,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

 CREATE INDEX idx_tickets_nombre ON tickets(title); 
 CREATE INDEX idx_tickets_prioridad ON tickets(priority);
 CREATE INDEX idx_ticket_estado ON tickets(status);