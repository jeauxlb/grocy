CREATE TABLE users (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username varchar(30) NOT NULL UNIQUE,
	first_name TEXT,
	last_name TEXT,
	password TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE sessions;

CREATE TABLE sessions (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	session_key varchar(30) NOT NULL UNIQUE,
	user_id INTEGER NOT NULL,
	expires DATETIME,
	last_used DATETIME,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)

