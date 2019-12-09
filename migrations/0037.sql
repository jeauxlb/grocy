ALTER TABLE products
ADD product_group_id INTEGER;

CREATE TABLE product_groups (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
