CREATE TABLE userfields (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	entity varchar(80) NOT NULL,
	name varchar(80) NOT NULL,
	caption TEXT NOT NULL,
	type TEXT NOT NULL,
	show_as_column_in_tables TINYINT NOT NULL DEFAULT 0,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,

	UNIQUE(entity, name)
);

CREATE TABLE userfield_values (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	field_id INTEGER NOT NULL,
	object_id INTEGER NOT NULL,
	value TEXT NOT NULL,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,

	UNIQUE(field_id, object_id)
);

CREATE VIEW userfield_values_resolved
AS
SELECT
	u.*,
	uv.object_id,
	uv.value
FROM userfields u
JOIN userfield_values uv
	ON u.id = uv.field_id;




