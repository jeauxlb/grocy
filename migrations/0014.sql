CREATE TABLE battery_charge_cycles (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	battery_id TEXT NOT NULL,
	tracked_time DATETIME,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)
