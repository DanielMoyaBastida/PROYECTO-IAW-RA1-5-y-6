CREATE DATABASE IF NOT EXISTS gestor_inc;
USE gestor_inc;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_usuario VARCHAR(100) NOT NULL UNIQUE,
  contrasena INT UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_ticket VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  prioridad ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  estado ENUM('abierta','cerrada') NOT NULL DEFAULT 'abierta'
);

CREATE TABLE IF NOT EXISTS logs_audioria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  accion VARCHAR(50) NOT NULL,
  entidad VARCHAR(50) NOT NULL,
  id_entidad INT,
  id_usuario INT,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

 CREATE INDEX idx_tickets_nombre ON tickets(nombre_ticket); 
 CREATE INDEX idx_tickets_prioridad ON tickets(prioridad);
 CREATE INDEX idx_ticket_estado ON tickets(estado);