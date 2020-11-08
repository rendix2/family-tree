-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 06. říj 2020, 23:59
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
  `townId` int(11) NOT NULL COMMENT 'Town ID',
  `countryId` int(11) NOT NULL COMMENT 'Address Country ID',
  PRIMARY KEY (`id`),
  KEY `K_Address_CountryId` (`countryId`),
  KEY `K_Address_TownId` (`townId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Adresses of persons';

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of countries';

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
  `townId` int(11) DEFAULT NULL COMMENT 'Job has To¨wn',
  `addressId` int(11) DEFAULT NULL COMMENT 'Job has address',
  PRIMARY KEY (`id`),
  KEY `K_Job_AddressId` (`addressId`),
  KEY `K_Job_TownId` (`townId`)
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
  KEY `K_Name_PersonId` (`personId`),
  KEY `K_Name_GenusId` (`genusId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Names of person (history of names)';

DROP TABLE IF EXISTS `notehistory`;
CREATE TABLE IF NOT EXISTS `notehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of note',
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `text` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Text of note',
  `date` datetime NOT NULL COMMENT 'Datetime of creation note',
  PRIMARY KEY (`id`),
  KEY `K_NoteHistory_PersonId` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Person ID',
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
  `birthTownId` int(11) DEFAULT NULL COMMENT 'Town ID of birth',
  `deathTownId` int(11) DEFAULT NULL COMMENT 'Town ID of death',
  `gravedTownId` int(255) DEFAULT NULL COMMENT 'Town ID of graved',
  `note` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Note of person',
  PRIMARY KEY (`id`),
  KEY `K_Person_MotherId` (`motherId`),
  KEY `K_Person_GenusId` (`genusId`),
  KEY `K_Person_FatherId` (`fatherId`),
  KEY `K_Person_DeathTownId` (`deathTownId`)
  KEY `K_Person_GravedTownId` (`gravedTownId`),
  KEY `K_Person_BirthTownId` (`birthTownId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Main table with person';

DROP TABLE IF EXISTS `person2address`;
CREATE TABLE IF NOT EXISTS `person2address` (
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `addressId` int(11) NOT NULL COMMENT 'Address ID',
  `dateSince` date DEFAULT NULL COMMENT 'Live since this date	',
  `dateTo` date DEFAULT NULL COMMENT 'Live to this date',
  `untilNow` tinyint(1) NOT NULL COMMENT 'Until now',
  PRIMARY KEY (`personId`,`addressId`),
  KEY `FK_Person2Address_AddressId` (`addressId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Addresses of persons';

DROP TABLE IF EXISTS `person2job`;
CREATE TABLE IF NOT EXISTS `person2job` (
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `jobId` int(11) NOT NULL COMMENT 'Job ID',
  `dateSince` date DEFAULT NULL COMMENT 'Since this date person work here',
  `dateTo` date DEFAULT NULL COMMENT 'To this date person has this job',
  `untilNow` tinyint(1) NOT NULL COMMENT 'Until now',
  PRIMARY KEY (`personId`,`jobId`),
  KEY `FK_Person2Job_JobId` (`jobId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Persons and theirs jobs';

DROP TABLE IF EXISTS `relation`;
CREATE TABLE IF NOT EXISTS `relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maleId` int(11) NOT NULL COMMENT 'First of the pair',
  `femaleId` int(11) NOT NULL COMMENT 'Second of the pair',
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  `untilNow` tinyint(1) NOT NULL COMMENT 'They are together until now',
  PRIMARY KEY (`id`),
  KEY `K_Relation_MaleId` (`maleId`),
  KEY `K_Relation_FemaleId` (`femaleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Relation of persons';

DROP TABLE IF EXISTS `source`;
CREATE TABLE IF NOT EXISTS `source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(10240) COLLATE utf8_czech_ci NOT NULL,
  `personId` int(11) NOT NULL COMMENT 'Person ID',
  `sourceTypeId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `K_Source_PersonId` (`personId`),
  KEY `K_Source_SourceTypeId` (`sourceTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Source of informations';

DROP TABLE IF EXISTS `sourcetype`;
CREATE TABLE IF NOT EXISTS `sourcetype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Types of sources';

DROP TABLE IF EXISTS `town`;
CREATE TABLE IF NOT EXISTS `town` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of Town',
  `countryId` int(11) NOT NULL COMMENT 'Country ID',
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Place name',
  `zipCode` varchar(100) COLLATE utf8_czech_ci NOT NULL COMMENT 'ZIP code of town',
  PRIMARY KEY (`id`),
  KEY `K_Town_CountryId` (`countryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='List of Towns';

DROP TABLE IF EXISTS `twins`;
CREATE TABLE IF NOT EXISTS `twins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motherId` int(11) NOT NULL COMMENT 'ID of mother',
  `fatherId` int(11) NOT NULL COMMENT 'ID of father',
  `child1Id` int(11) NOT NULL COMMENT 'ID of first child',
  `child2Id` int(11) NOT NULL COMMENT 'ID of second child',
  PRIMARY KEY (`id`),
  KEY `K_Twins_Child1Id` (`child1Id`),
  KEY `K_Twins_MotherId` (`motherId`),
  KEY `K_Twins_FatherId` (`fatherId`),
  KEY `K_Twins_Child2Id` (`child2Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Borned as a twins';

DROP TABLE IF EXISTS `wedding`;
CREATE TABLE IF NOT EXISTS `wedding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `husbandId` int(11) NOT NULL COMMENT 'Male',
  `wifeId` int(11) NOT NULL COMMENT 'Female',
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished',
  `untilNow` tinyint(1) NOT NULL COMMENT 'They are still together',
  `townId` int(255) DEFAULT NULL COMMENT 'Town ID of wedding',
  PRIMARY KEY (`id`),
  KEY `K_Wedding_HusbandId` (`husbandId`),
  KEY `K_Wedding_TownId` (`townId`),
  KEY `K_Wedding_WifeId` (`wifeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Wedding of persons';

ALTER TABLE `address`
  ADD CONSTRAINT `FK_Address_CountryId` FOREIGN KEY (`countryId`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Address_TownId` FOREIGN KEY (`townId`) REFERENCES `town` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `job`
  ADD CONSTRAINT `FK_Job_AddressId` FOREIGN KEY (`addressId`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_Job_TownId` FOREIGN KEY (`townId`) REFERENCES `town` (`id`);

ALTER TABLE `name`
  ADD CONSTRAINT `FK_Name_GenusId` FOREIGN KEY (`genusId`) REFERENCES `genus` (`id`),
  ADD CONSTRAINT `FK_Name_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

ALTER TABLE `notehistory`
  ADD CONSTRAINT `FK_NoteHistory_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

ALTER TABLE `person`
  ADD CONSTRAINT `FK_Person_BirthPlaceId` FOREIGN KEY (`birthTownId`) REFERENCES `town` (`id`),
  ADD CONSTRAINT `FK_Person_DeathPlaceId` FOREIGN KEY (`deathTownId`) REFERENCES `town` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Person_FatherId` FOREIGN KEY (`fatherId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Person_GenusId` FOREIGN KEY (`genusId`) REFERENCES `genus` (`id`),
  ADD CONSTRAINT `FK_Person_GravedPlaceId` FOREIGN KEY (`gravedTownId`) REFERENCES `town` (`id`),
  ADD CONSTRAINT `FK_Person_MotherId` FOREIGN KEY (`motherId`) REFERENCES `person` (`id`);

ALTER TABLE `person2address`
  ADD CONSTRAINT `FK_Person2Address_AddressId` FOREIGN KEY (`addressId`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_Person2Address_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

ALTER TABLE `person2job`
  ADD CONSTRAINT `FK_Person2Job_JobId` FOREIGN KEY (`jobId`) REFERENCES `job` (`id`),
  ADD CONSTRAINT `FK_Person2Job_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`);

ALTER TABLE `relation`
  ADD CONSTRAINT `FK_FemaleId` FOREIGN KEY (`femaleId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_MaleId` FOREIGN KEY (`maleId`) REFERENCES `person` (`id`);

ALTER TABLE `source`
  ADD CONSTRAINT `FK_Source_PersonId` FOREIGN KEY (`personId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Source_SourceTypeId` FOREIGN KEY (`sourceTypeId`) REFERENCES `sourcetype` (`id`);

ALTER TABLE `twins`
  ADD CONSTRAINT `FK_Twins_Child1Id` FOREIGN KEY (`child1Id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Twins_Child2Id` FOREIGN KEY (`child2Id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Twins_FatherId` FOREIGN KEY (`fatherId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Twins_MotherId` FOREIGN KEY (`motherId`) REFERENCES `person` (`id`);

ALTER TABLE `wedding`
  ADD CONSTRAINT `FK_Wedding_HusbandId` FOREIGN KEY (`husbandId`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `FK_Wedding_TownId` FOREIGN KEY (`townId`) REFERENCES `town` (`id`),
  ADD CONSTRAINT `FK_Wedding_WifeId` FOREIGN KEY (`wifeId`) REFERENCES `person` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
