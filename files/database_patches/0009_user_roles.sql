ALTER TABLE `user_role` DROP COLUMN `id`,
 DROP PRIMARY KEY,
 ADD PRIMARY KEY  USING BTREE(`user_id`, `role_id`);
 
ALTER TABLE `role` ADD COLUMN `deleted` DATETIME AFTER `created`;

ALTER TABLE `user_role` ADD COLUMN `created` DATETIME NOT NULL AFTER `role_id`;

ALTER TABLE `role` ADD COLUMN `description` VARCHAR(256) NOT NULL DEFAULT '' AFTER `name`;

ALTER TABLE `user_role` ADD COLUMN `created_by_user_id` INT(10) UNSIGNED NOT NULL AFTER `created`;