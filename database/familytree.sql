-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 08. zář 2020, 00:07
-- Verze serveru: 10.1.30-MariaDB
-- Verze PHP: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street` varchar(1024) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'street part',
  `streetNumber` int(11) DEFAULT NULL COMMENT 'street number part',
  `houseNumber` int(11) DEFAULT NULL COMMENT 'house number part',
  `zip` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'zip part',
  `town` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'town part',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Adresses of people';

DROP TABLE IF EXISTS `genus`;
CREATE TABLE IF NOT EXISTS `genus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname for this series of people',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Genuses of people';

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Job name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Jobs of people';

DROP TABLE IF EXISTS `name`;
CREATE TABLE IF NOT EXISTS `name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `peopleId` int(11) NOT NULL COMMENT 'ID of people',
  `name` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed name of people',
  `surname` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed surname of people',
  `dateSince` date NOT NULL COMMENT 'Date when name was changed',
  `dateTo` date DEFAULT NULL COMMENT 'To this date people had this name',
  PRIMARY KEY (`id`),
  KEY `people_id` (`peopleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Names of people (history of names)';

DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sex` char(1) COLLATE utf8_czech_ci NOT NULL COMMENT 'Sex of people',
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Name of people',
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname of people',
  `nameDay` date NOT NULL COMMENT 'Nameday of people',
  `birthDate` date NOT NULL COMMENT 'Birthday of person',
  `deathDate` date DEFAULT NULL COMMENT 'Date when people died',
  `motherId` int(11) DEFAULT NULL COMMENT 'Mother of people',
  `fatherId` int(11) DEFAULT NULL COMMENT 'Father of people',
  `genusId` int(11) DEFAULT NULL COMMENT 'Genus ID of people',
  PRIMARY KEY (`id`),
  KEY `mother` (`motherId`) USING BTREE,
  KEY `father` (`fatherId`),
  KEY `genusId` (`genusId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Main table with people';

DROP TABLE IF EXISTS `people2address`;
CREATE TABLE IF NOT EXISTS `people2address` (
  `peopleId` int(11) NOT NULL COMMENT 'People',
  `addressId` int(11) NOT NULL COMMENT 'Address',
  `dateSince` date DEFAULT NULL COMMENT 'Live since this date',
  `dateTo` date DEFAULT NULL COMMENT 'Live to this date',
  PRIMARY KEY (`peopleId`,`addressId`),
  KEY `FK_People2Address_Address` (`addressId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='adresses of peoples';

DROP TABLE IF EXISTS `people2job`;
CREATE TABLE IF NOT EXISTS `people2job` (
  `peopleId` int(11) NOT NULL COMMENT 'People',
  `jobId` int(11) NOT NULL COMMENT 'Job',
  `dateSince` date DEFAULT NULL COMMENT 'Since this date people work here',
  `dateTo` date DEFAULT NULL COMMENT 'To this date people has this job',
  PRIMARY KEY (`peopleId`,`jobId`),
  KEY `FK_Job` (`jobId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='people and theirs jobs';

DROP TABLE IF EXISTS `relation`;
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maleId` int(11) NOT NULL COMMENT 'First of the pair',
  `femaleId` int(11) NOT NULL COMMENT 'Second of the pair',
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  PRIMARY KEY (`id`),
  KEY `male` (`maleId`) USING BTREE,
  KEY `female` (`femaleId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='relation of peoples';

DROP TABLE IF EXISTS `twins`;
CREATE TABLE IF NOT EXISTS `twins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motherId` int(11) NOT NULL COMMENT 'ID of mother',
  `fatherId` int(11) NOT NULL COMMENT 'ID of father',
  `child1Id` int(11) NOT NULL COMMENT 'ID of first child',
  `child2Id` int(11) NOT NULL COMMENT 'ID of second child',
  PRIMARY KEY (`id`),
  KEY `people1_id` (`child1Id`),
  KEY `people2_id` (`child2Id`),
  KEY `parent_id` (`fatherId`),
  KEY `mother_id` (`motherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Borned as a twins';

DROP TABLE IF EXISTS `wedding`;
CREATE TABLE IF NOT EXISTS `wedding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `husbandId` int(11) NOT NULL COMMENT 'Male',
  `wifeId` int(11) NOT NULL COMMENT 'Female',
  `dateSince` date NOT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  PRIMARY KEY (`id`),
  KEY `people1_id` (`husbandId`),
  KEY `people2_id` (`wifeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Wedding of people';

ALTER TABLE `name`
  ADD CONSTRAINT `FK_Name_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`);

ALTER TABLE `people`
  ADD CONSTRAINT `father` FOREIGN KEY (`fatherId`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `genus` FOREIGN KEY (`genusId`) REFERENCES `genus` (`id`),
  ADD CONSTRAINT `mother` FOREIGN KEY (`motherId`) REFERENCES `people` (`id`);

ALTER TABLE `people2address`
  ADD CONSTRAINT `FK_People2Address_Address` FOREIGN KEY (`addressId`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People2Address_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `people2job`
  ADD CONSTRAINT `FK_Job` FOREIGN KEY (`jobId`) REFERENCES `job` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `relation`
  ADD CONSTRAINT `FK_Female` FOREIGN KEY (`femaleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Male` FOREIGN KEY (`maleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION;

ALTER TABLE `twins`
  ADD CONSTRAINT `FK_Child1` FOREIGN KEY (`child1Id`) REFERENCES `people` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Child2` FOREIGN KEY (`child2Id`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Father` FOREIGN KEY (`fatherId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Mother` FOREIGN KEY (`motherId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `wedding`
  ADD CONSTRAINT `FK_Husband` FOREIGN KEY (`husbandId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Wife` FOREIGN KEY (`wifeId`) REFERENCES `people` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
