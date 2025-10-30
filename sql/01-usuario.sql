CREATE USER IF NOT EXISTS 'tasker'@'%' IDENTIFIED BY 'taskertasking';
CREATE USER IF NOT EXISTS 'tasker'@'localhost' IDENTIFIED BY 'taskertasking';
GRANT select, insert, delete, update ON task_db.* TO 'tasker'@'%';
GRANT ALL PRIVILEGES ON task_db.* TO 'tasker'@'localhost';

FLUSH PRIVILEGES;