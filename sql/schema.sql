CREATE DATABASE IF NOT EXISTS gestor_inc;
USE gestor_inc;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_usuario VARCHAR(100) NOT NULL UNIQUE,
  hash_contrasena VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  prioridad ENUM('baja','media','alta') NOT NULL DEFAULT 'media',
  estado ENUM('abierta','cerrada') NOT NULL DEFAULT 'abierta'
);

CREATE TABLE IF NOT EXISTS registros_auditoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  accion VARCHAR(50) NOT NULL,
  entidad VARCHAR(50) NOT NULL,
  id_entidad INT,
  id_usuario INT,
  detalles JSON,
  CONSTRAINT fk_auditoria_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE INDEX idx_tickets_titulo ON tickets(titulo); 
CREATE INDEX idx_tickets_prioridad ON tickets(prioridad);
CREATE INDEX idx_tickets_estado ON tickets(estado);