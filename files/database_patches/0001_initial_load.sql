-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

--
-- Definition of table `connection`
--

DROP TABLE IF EXISTS `connection`;
CREATE TABLE `connection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(60) NOT NULL,
  `url` varchar(128) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL,
  `host` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `connection`
--

/*!40000 ALTER TABLE `connection` DISABLE KEYS */;
/*!40000 ALTER TABLE `connection` ENABLE KEYS */;


--
-- Definition of table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('mysql') NOT NULL DEFAULT 'mysql',
  `display_name` varchar(60) NOT NULL,
  `connection_id` int(10) unsigned NOT NULL,
  `visibility` enum('public','private','protected') NOT NULL DEFAULT 'public',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creator_user_id` int(10) unsigned NOT NULL,
  `report_data` blob NOT NULL,
  `description` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `FK_report_connection` (`connection_id`),
  CONSTRAINT `FK_report_connection` FOREIGN KEY (`connection_id`) REFERENCES `connection` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `report`
--

/*!40000 ALTER TABLE `report` DISABLE KEYS */;
/*!40000 ALTER TABLE `report` ENABLE KEYS */;


--
-- Definition of table `report_permission`
--

DROP TABLE IF EXISTS `report_permission`;
CREATE TABLE `report_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX_unique` (`report_id`,`user_id`,`deleted`),
  KEY `FK_report_permission_user` (`user_id`),
  CONSTRAINT `FK_report_permission_report` FOREIGN KEY (`report_id`) REFERENCES `report` (`id`),
  CONSTRAINT `FK_report_permission_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `report_permission`
--

/*!40000 ALTER TABLE `report_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_permission` ENABLE KEYS */;


--
-- Definition of table `report_variable`
--

DROP TABLE IF EXISTS `report_variable`;
CREATE TABLE  `report_variable` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `variable_type` enum('integer','datetime','string') NOT NULL DEFAULT 'string',
  `default_value` varchar(60) NOT NULL DEFAULT '',
  `text_identifier` varchar(60) NOT NULL,
  `display_name` varchar(60) NOT NULL,
  `description` varchar(256) NOT NULL,
  `options_query` blob,
  PRIMARY KEY (`id`),
  KEY `FK_report_variable_report` (`report_id`),
  CONSTRAINT `FK_report_variable_report` FOREIGN KEY (`report_id`) REFERENCES `report` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `report_variable`
--

/*!40000 ALTER TABLE `report_variable` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_variable` ENABLE KEYS */;


--
-- Definition of table `scheduled_report`
--

DROP TABLE IF EXISTS `scheduled_report`;
CREATE TABLE `scheduled_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `variables` blob NOT NULL,
  `day_of_month` varchar(60) NOT NULL COMMENT 'csv of days to run on, * for all',
  `month_of_year` varchar(60) NOT NULL COMMENT 'csv of months to run on, * for all',
  `day_of_week` varchar(60) NOT NULL COMMENT 'csv of days to run on, * for all',
  `email_template` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_scheduled_report_report` (`report_id`),
  KEY `FK_scheduled_report_user` (`user_id`),
  CONSTRAINT `FK_scheduled_report_report` FOREIGN KEY (`report_id`) REFERENCES `report` (`id`),
  CONSTRAINT `FK_scheduled_report_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `scheduled_report`
--

/*!40000 ALTER TABLE `scheduled_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_report` ENABLE KEYS */;


--
-- Definition of table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(60) NOT NULL DEFAULT 'random',
  `lname` varchar(60) NOT NULL DEFAULT 'person',
  `username` varchar(60) NOT NULL,
  `email_address` varchar(60) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `password` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;


--
-- Definition of table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role` enum('internal','external','admin','scheduler') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_id_user` (`user_id`),
  CONSTRAINT `FK_user_id_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
