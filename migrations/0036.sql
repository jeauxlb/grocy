CREATE TABLE tasks (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL UNIQUE,
	description TEXT,
	due_date DATETIME,
	done TINYINT NOT NULL DEFAULT 0 CHECK(done IN (0, 1)),
	done_timestamp DATETIME,
	category_id INTEGER,
	assigned_to_user_id INTEGER,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE task_categories (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL UNIQUE,
	description TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE VIEW tasks_current
AS
SELECT *
FROM tasks
WHERE done = 0;
