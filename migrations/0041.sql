CREATE TABLE equipment (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL UNIQUE,
	description TEXT,
	instruction_manual_file_name TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)
