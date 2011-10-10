ALTER TABLE `connection` 
	ADD COLUMN `type` ENUM('MySQL','MSSQL','Brain Honey') NOT NULL DEFAULT 'MySQL' AFTER `hostname`, 
	ADD COLUMN `database` VARCHAR(60) NOT NULL  AFTER `type`, 
	ADD COLUMN `dbprefix` VARCHAR(45) NULL  AFTER `database`,
	ADD COLUMN `pconnect` VARCHAR(45) NULL DEFAULT 'FALSE'  AFTER `dbprefix`,
	ADD COLUMN `db_debug` VARCHAR(45) NULL DEFAULT 'TRUE'  AFTER `pconnect`,
	ADD COLUMN `cache_on` VARCHAR(45) NULL DEFAULT 'FALSE'  AFTER `db_debug`, 
	ADD COLUMN `cachedir` VARCHAR(45) NULL  AFTER `cache_on`, 
	ADD COLUMN `char_set` VARCHAR(45) NULL DEFAULT 'utf8'  AFTER `cachedir`, 
	ADD COLUMN `dbcollat` VARCHAR(45) NULL DEFAULT 'utf8_general_ci'  AFTER `char_set`, 
	CHANGE COLUMN `url` `url` VARCHAR(128) NULL, 
	CHANGE COLUMN `host` `hostname` VARCHAR(60) NOT NULL  
;
