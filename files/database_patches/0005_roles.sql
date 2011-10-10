CREATE TABLE IF NOT EXISTS `role` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;

ALTER TABLE `user_role` CHANGE COLUMN `role` `role_id` INT(10) UNSIGNED NOT NULL,
 ADD CONSTRAINT `FK_role_id_role` FOREIGN KEY `FK_role_id_role` (`role_id`)
    REFERENCES `role` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
	
CREATE TABLE IF NOT EXISTS `application_settings` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `value` VARCHAR(128) NOT NULL,
  `type` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `IDX_name`(`name`)
)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS  `sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(16) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)
);

CREATE TABLE IF NOT EXISTS `nonce` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(60) NOT NULL,
  `hash` varchar(60) NOT NULL,
  `use_count` int(11) NOT NULL DEFAULT '0',
  `max_uses` int(11) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `failed_attempts` int(11) NOT NULL DEFAULT '0',
  `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_unique` (`type`,`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;