CREATE TABLE userentities (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name varchar(80) NOT NULL,
	caption TEXT NOT NULL,
	description TEXT,
	show_in_sidebar_menu TINYINT NOT NULL DEFAULT 1,
	icon_css_class TEXT,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,

	UNIQUE(name)
);

CREATE TABLE userobjects (
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userentity_id INTEGER NOT NULL,
	row_created_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);


