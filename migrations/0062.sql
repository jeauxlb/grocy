ALTER TABLE shopping_list
ADD shopping_list_id INT DEFAULT 1;

CREATE TABLE shopping_lists (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO shopping_lists
	(name)
VALUES
	('Default');
