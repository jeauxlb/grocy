CREATE TABLE user_settings (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id INTEGER NOT NULL,
	`key` varchar(80) NOT NULL,
	value TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
	row_updated_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, `key`)
);
