-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Stř 23. zář 2020, 14:23
-- Verze serveru: 10.1.30-MariaDB
-- Verze PHP: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Adresses of persons';

DROP TABLE IF EXISTS `genus`;
CREATE TABLE IF NOT EXISTS `genus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname for this series of persons',
  `surnameFonetic` varchar(512) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Fonetic surname',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Genuses of persons';

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Name of company',
  `position` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Name of position',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Jobs of persons';

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `langName` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Set of languages';

INSERT INTO `language` (`id`, `langName`) VALUES
(1, 'cs.CZ'),
(2, 'en.US');

DROP TABLE IF EXISTS `name`;
CREATE TABLE IF NOT EXISTS `name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personId` int(11) NOT NULL COMMENT 'ID of person',
  `genusId` int(11) NOT NULL COMMENT 'ID of genus',
  `name` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed name of person',
  `nameFonetic` varchar(512) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Fonetic name',
  `surname` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed surname of person',
  `dateSince` date DEFAULT NULL COMMENT 'Date when name was changed',
  `dateTo` date DEFAULT NULL COMMENT 'To this date person had this name',
  `untilNow` tinyint(1) NOT NULL COMMENT 'Person has this name until now',
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`),
  KEY `genusId` (`genusId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Names of person (history of names)';

DROP TABLE IF EXISTS `notehistory`;
CREATE TABLE IF NOT EXISTS `notehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of note',
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `text` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Text of note',
  `date` datetime NOT NULL COMMENT 'Datetime of creation note',
  PRIMARY KEY (`id`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gender` char(1) COLLATE utf8_czech_ci NOT NULL COMMENT 'Gender of person',
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Name of person',
  `nameFonetic` varchar(512) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Fonetic name',
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname of person',
  `hasBirthDate` tinyint(1) NOT NULL COMMENT 'Has birth date',
  `birthDate` date DEFAULT NULL COMMENT 'Birthday of person',
  `hasBirthYear` tinyint(1) NOT NULL COMMENT 'Has birth year',
  `birthYear` int(4) DEFAULT NULL COMMENT 'Birth year of of person',
  `stillAlive` tinyint(1) NOT NULL COMMENT 'Person still live',
  `hasDeathDate` tinyint(1) NOT NULL COMMENT 'Has death date',
  `deathDate` date DEFAULT NULL COMMENT 'Date when person died',
  `hasDeathYear` tinyint(1) NOT NULL COMMENT 'Has death year',
  `deathYear` int(4) DEFAULT NULL COMMENT 'Death year',
  `hasAge` tinyint(1) NOT NULL COMMENT 'Has age',
  `age` int(11) DEFAULT NULL COMMENT 'Direct age',
  `motherId` int(11) DEFAULT NULL COMMENT 'Mother of person',
  `fatherId` int(11) DEFAULT NULL COMMENT 'Father of person',
  `genusId` int(11) DEFAULT NULL COMMENT 'Genus of person',
  `birthPlaceId` int(11) DEFAULT NULL COMMENT 'Place ID of birth',
  `deathPlaceId` int(11) DEFAULT NULL COMMENT 'Place ID of death',
  `gravedPlaceId` int(255) DEFAULT NULL COMMENT 'Place ID of graved',
  `note` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Note of person',
  PRIMARY KEY (`id`),
  KEY `mother` (`motherId`) USING BTREE,
  KEY `genusId` (`genusId`),
  KEY `birthPlaceId` (`birthPlaceId`),
  KEY `deathPlaceId` (`deathPlaceId`),
  KEY `father` (`fatherId`),
  KEY `gravedPlaceId` (`gravedPlaceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Main table with person';

DROP TABLE IF EXISTS `person2address`;
CREATE TABLE IF NOT EXISTS `person2address` (
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `addressId` int(11) NOT NULL COMMENT 'Address ID',
  `dateSince` date DEFAULT NULL COMMENT 'Live since this date	',
  `dateTo` date DEFAULT NULL COMMENT 'Live to this date',
  PRIMARY KEY (`personId`,`addressId`),
  KEY `FK_People2Address_Address` (`addressId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Addresses of persons';

DROP TABLE IF EXISTS `person2job`;
CREATE TABLE IF NOT EXISTS `person2job` (
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `jobId` int(11) NOT NULL COMMENT 'Job ID',
  `dateSince` date DEFAULT NULL COMMENT 'Since this date person work here',
  `dateTo` date DEFAULT NULL COMMENT 'To this date person has this job',
  PRIMARY KEY (`personId`,`jobId`),
  KEY `FK_Job` (`jobId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Persons and theirs jobs';

DROP TABLE IF EXISTS `place`;
CREATE TABLE IF NOT EXISTS `place` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of place',
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'place name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Places for persons';

DROP TABLE IF EXISTS `relation`;
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maleId` int(11) NOT NULL COMMENT 'First of the pair',
  `femaleId` int(11) NOT NULL COMMENT 'Second of the pair',
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  `untilNow` tinyint(1) NOT NULL COMMENT 'They are together until now',
  PRIMARY KEY (`id`),
  KEY `male` (`maleId`) USING BTREE,
  KEY `female` (`femaleId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Relation of persons';

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
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  `untilNow` tinyint(1) NOT NULL COMMENT 'They are still together',
  PRIMARY KEY (`id`),
  KEY `people1_id` (`husbandId`),
  KEY `people2_id` (`wifeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Wedding of persons';

ALTER TABLE `name`
  ADD CONSTRAINT `FK_Name_Genus` FOREIGN KEY (`genusId`) REFERENCES `genus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Name_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `notehistory`
  ADD CONSTRAINT `notehistory_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `person`
  ADD CONSTRAINT `FK_gravedPlaceId` FOREIGN KEY (`gravedPlaceId`) REFERENCES `place` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `birthPlace` FOREIGN KEY (`birthPlaceId`) REFERENCES `place` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `deathPlace` FOREIGN KEY (`deathPlaceId`) REFERENCES `place` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `father` FOREIGN KEY (`fatherId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `genus` FOREIGN KEY (`genusId`) REFERENCES `genus` (`id`),
  ADD CONSTRAINT `mother` FOREIGN KEY (`motherId`) REFERENCES `person` (`id`);

ALTER TABLE `person2address`
  ADD CONSTRAINT `FK_People2Address_Address` FOREIGN KEY (`addressId`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People2Address_People` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `person2job`
  ADD CONSTRAINT `FK_Job` FOREIGN KEY (`jobId`) REFERENCES `job` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People` FOREIGN KEY (`personId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `relation`
  ADD CONSTRAINT `FK_Female` FOREIGN KEY (`femaleId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Male` FOREIGN KEY (`maleId`) REFERENCES `person` (`id`) ON DELETE NO ACTION;

ALTER TABLE `twins`
  ADD CONSTRAINT `FK_Child1` FOREIGN KEY (`child1Id`) REFERENCES `person` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Child2` FOREIGN KEY (`child2Id`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Father` FOREIGN KEY (`fatherId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Mother` FOREIGN KEY (`motherId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `wedding`
  ADD CONSTRAINT `FK_Husband` FOREIGN KEY (`husbandId`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Wife` FOREIGN KEY (`wifeId`) REFERENCES `person` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
