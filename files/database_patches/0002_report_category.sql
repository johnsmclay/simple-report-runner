CREATE TABLE `report_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;
ALTER TABLE `report` ADD COLUMN `category_id` INT(10) UNSIGNED NOT NULL AFTER `description`;

ALTER TABLE `report` ADD CONSTRAINT `FK_report_category` FOREIGN KEY `FK_report_category` (`category_id`)
    REFERENCES `report_category` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
