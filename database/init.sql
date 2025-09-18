-- MySQL initialization script for talent assessment database
-- This script runs when the MySQL container starts for the first time

-- Grant comprehensive privileges to talent_user for reseller database operations
GRANT CREATE ON *.* TO 'talent_user'@'%';
GRANT DROP ON *.* TO 'talent_user'@'%';
GRANT ALTER ON *.* TO 'talent_user'@'%';
GRANT INDEX ON *.* TO 'talent_user'@'%';
GRANT INSERT ON *.* TO 'talent_user'@'%';
GRANT SELECT ON *.* TO 'talent_user'@'%';
GRANT UPDATE ON *.* TO 'talent_user'@'%';
GRANT DELETE ON *.* TO 'talent_user'@'%';
GRANT REFERENCES ON *.* TO 'talent_user'@'%';
GRANT CREATE TEMPORARY TABLES ON *.* TO 'talent_user'@'%';
GRANT LOCK TABLES ON *.* TO 'talent_user'@'%';
GRANT EXECUTE ON *.* TO 'talent_user'@'%';
GRANT REPLICATION SLAVE ON *.* TO 'talent_user'@'%';
GRANT REPLICATION CLIENT ON *.* TO 'talent_user'@'%';
GRANT CREATE VIEW ON *.* TO 'talent_user'@'%';
GRANT SHOW VIEW ON *.* TO 'talent_user'@'%';
GRANT CREATE ROUTINE ON *.* TO 'talent_user'@'%';
GRANT ALTER ROUTINE ON *.* TO 'talent_user'@'%';
GRANT EVENT ON *.* TO 'talent_user'@'%';
GRANT TRIGGER ON *.* TO 'talent_user'@'%';

-- Ensure privileges are applied
FLUSH PRIVILEGES;

-- Create a test to verify permissions work correctly
CREATE DATABASE IF NOT EXISTS test_permissions;
USE test_permissions;
CREATE TABLE IF NOT EXISTS test_table (id INT PRIMARY KEY, name VARCHAR(50));
INSERT INTO test_table VALUES (1, 'test');
DROP DATABASE IF EXISTS test_permissions;

-- Log successful initialization
SELECT 'Database permissions initialized successfully' AS status;
