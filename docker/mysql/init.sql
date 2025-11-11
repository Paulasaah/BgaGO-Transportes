-- docker/mysql/init.sql
-- Script de inicialización para MySQL en Docker

-- Crear base de datos de testing
CREATE DATABASE IF NOT EXISTS `testing`;

-- Dar permisos al usuario sail sobre la BD testing
GRANT ALL PRIVILEGES ON `testing`.* TO 'sail'@'%';

-- Refrescar privilegios
FLUSH PRIVILEGES;

-- Verificar que se creó correctamente
SELECT 'Base de datos testing creada exitosamente' AS status;
