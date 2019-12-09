DROP VIEW habits_current;
-- dropped

DROP VIEW batteries_current;
CREATE VIEW batteries_current
AS
SELECT
	b.id AS battery_id,
	MAX(l.tracked_time) AS last_tracked_time,
	CASE WHEN b.charge_interval_days = 0
		THEN '2999-12-31 23:59:59'
		ELSE str_to_date(concat(MAX(l.tracked_time), '+', CAST(b.charge_interval_days AS char) , ' day'), 'YYYY-MM-DD HH:MM:SS')
	END AS next_estimated_charge_time
FROM batteries b
LEFT JOIN battery_charge_cycles l
	ON b.id = l.battery_id
GROUP BY b.id, b.charge_interval_days;
