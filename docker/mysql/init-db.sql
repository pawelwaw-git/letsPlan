-- Create the 'app_test' database
CREATE DATABASE IF NOT EXISTS `app_test`;

-- Grant privileges to the user for 'app_test' database
GRANT ALL PRIVILEGES ON `app_test`.* TO 'app'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;