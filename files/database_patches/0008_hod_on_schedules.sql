ALTER TABLE `scheduled_report`
 ADD COLUMN `hour_of_day` VARCHAR(60) NOT NULL AFTER `day_of_week`,
 ADD COLUMN `created` DATETIME NOT NULL AFTER `email_template`,
 ADD COLUMN `deleted` DATETIME AFTER `created`,
 ADD COLUMN `created_by_user_id` INT(10) UNSIGNED NOT NULL AFTER `deleted`;
 
ALTER TABLE `scheduled_report` 
 MODIFY COLUMN `day_of_month` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '*' COMMENT 'csv of days to run on, * for all',
 MODIFY COLUMN `month_of_year` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '*' COMMENT 'csv of months to run on, * for all',
 MODIFY COLUMN `day_of_week` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '*' COMMENT 'csv of days to run on, * for all',
 MODIFY COLUMN `hour_of_day` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '*';

ALTER TABLE `scheduled_report` MODIFY COLUMN `email_template` BLOB DEFAULT NULL;
