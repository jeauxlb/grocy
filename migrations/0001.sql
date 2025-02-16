CREATE TABLE products (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(30) NOT NULL UNIQUE,
	description TEXT,
	location_id INTEGER NOT NULL,
	qu_id_purchase INTEGER NOT NULL,
	qu_id_stock INTEGER NOT NULL,
	qu_factor_purchase_to_stock REAL NOT NULL,
	barcode TEXT,
	min_stock_amount INTEGER NOT NULL DEFAULT 0,
	default_best_before_days INTEGER NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)
