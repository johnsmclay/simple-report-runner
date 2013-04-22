ALTER TABLE `connection` 
  ADD COLUMN `swap_pre` VARCHAR(45) NULL AFTER dbcollat,
  ADD COLUMN `autoinit` CHAR(5) DEFAULT 'TRUE' AFTER `swap_pre`,
  ADD COLUMN `stricton` CHAR(5) DEFAULT 'FALSE' AFTER `autoinit`,
  ADD COLUMN `port` INT(5) DEFAULT 3306  AFTER `stricton` ;
