ALTER TABLE habits_log
ADD done_by_user_id INTEGER;

DROP TABLE api_keys;

CREATE TABLE api_keys (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	api_key varchar(80) NOT NULL UNIQUE,
	user_id INTEGER NOT NULL,
	expires DATETIME,
	last_used DATETIME,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
