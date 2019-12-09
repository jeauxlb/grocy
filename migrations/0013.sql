CREATE TABLE batteries (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(30) NOT NULL UNIQUE,
	description TEXT,
	used_in TEXT,
	charge_interval_days INTEGER NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)
