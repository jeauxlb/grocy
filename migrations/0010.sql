CREATE TABLE habits (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(30) NOT NULL UNIQUE,
	description TEXT,
	period_type TEXT NOT NULL,
	period_days INTEGER,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)
