USE incidencias;

INSERT INTO users (username, password) VALUES
('admin', 'admin123');

INSERT INTO incidencias (titulo, descripcion, prioridad, estado) VALUES
('Fallo de login','No se puede iniciar sesión','alta','abierta'),
('Error en página','Página 404 aparece al cargar','media','abierta'),
('Impresora no funciona','Impresora oficina central','baja','cerrada');