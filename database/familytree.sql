-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Úte 01. zář 2020, 15:33
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

--
-- Databáze: `rodokmen`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `street` varchar(1024) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'street part',
  `streetNumber` int(11) DEFAULT NULL COMMENT 'street number part',
  `houseNumber` int(11) DEFAULT NULL COMMENT 'house number part',
  `zip` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'zip part',
  `town` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL COMMENT 'town part',
  `dateSince` date DEFAULT NULL COMMENT 'live since this date',
  `dateTo` date DEFAULT NULL COMMENT 'live to this date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Adresses of people';

--
-- Struktura tabulky `genus`
--

CREATE TABLE `genus` (
  `id` int(11) NOT NULL,
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname for this series of people'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Genuses of people';

-- --------------------------------------------------------

--
-- Struktura tabulky `job`
--

CREATE TABLE `job` (
  `id` int(11) NOT NULL,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Job name'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Jobs of people';

-- --------------------------------------------------------

--
-- Struktura tabulky `name`
--

CREATE TABLE `name` (
  `id` int(11) NOT NULL,
  `peopleId` int(11) NOT NULL COMMENT 'ID of people',
  `name` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed name of people',
  `surname` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL COMMENT 'Changed surname of people',
  `dateSince` date NOT NULL COMMENT 'Date when name was changed',
  `dateTo` date DEFAULT NULL COMMENT 'To this date people had this name'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Names of people (history of names)';

-- --------------------------------------------------------

--
-- Struktura tabulky `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `sex` char(1) COLLATE utf8_czech_ci NOT NULL COMMENT 'Sex of people',
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Name of people',
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL COMMENT 'Surname of people',
  `nameDay` date NOT NULL COMMENT 'Nameday of people',
  `birthDate` date NOT NULL COMMENT 'Birthday of person',
  `deathDate` date DEFAULT NULL COMMENT 'Date when people died',
  `motherId` int(11) DEFAULT NULL COMMENT 'Mother of people',
  `fatherId` int(11) DEFAULT NULL COMMENT 'Father of people'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Main table with people';

-- --------------------------------------------------------

--
-- Struktura tabulky `people2address`
--

CREATE TABLE `people2address` (
  `peopleId` int(11) NOT NULL COMMENT 'People',
  `addressId` int(11) NOT NULL COMMENT 'Address'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='adresses of peoples';

--
-- Vypisuji data pro tabulku `people2address`
--

--
-- Struktura tabulky `people2job`
--

CREATE TABLE `people2job` (
  `peopleId` int(11) NOT NULL COMMENT 'People',
  `jobId` int(11) NOT NULL COMMENT 'Job'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='people and theirs jobs';

-- --------------------------------------------------------

--
-- Struktura tabulky `relation`
--

CREATE TABLE `relation` (
  `id` int(11) NOT NULL,
  `maleId` int(11) NOT NULL COMMENT 'First of the pair',
  `femaleId` int(11) NOT NULL COMMENT 'Second of the pair',
  `dateSince` date DEFAULT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='relation of peoples';

-- --------------------------------------------------------

--
-- Struktura tabulky `twins`
--

CREATE TABLE `twins` (
  `id` int(11) NOT NULL,
  `motherId` int(11) NOT NULL COMMENT 'ID of mother',
  `fatherId` int(11) NOT NULL COMMENT 'ID of father',
  `child1Id` int(11) NOT NULL COMMENT 'ID of first child',
  `child2Id` int(11) NOT NULL COMMENT 'ID of second child'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Borned as a twins';

-- --------------------------------------------------------

--
-- Struktura tabulky `wedding`
--

CREATE TABLE `wedding` (
  `id` int(11) NOT NULL,
  `husbandId` int(11) NOT NULL COMMENT 'Male',
  `wifeId` int(11) NOT NULL COMMENT 'Female',
  `dateSince` date NOT NULL COMMENT 'Created',
  `dateTo` date DEFAULT NULL COMMENT 'Finished'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Wedding of people';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `genus`
--
ALTER TABLE `genus`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `name`
--
ALTER TABLE `name`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people_id` (`peopleId`);

--
-- Klíče pro tabulku `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mother` (`motherId`) USING BTREE,
  ADD KEY `father` (`fatherId`);

--
-- Klíče pro tabulku `people2address`
--
ALTER TABLE `people2address`
  ADD PRIMARY KEY (`peopleId`,`addressId`),
  ADD KEY `FK_People2Address_Address` (`addressId`);

--
-- Klíče pro tabulku `people2job`
--
ALTER TABLE `people2job`
  ADD PRIMARY KEY (`peopleId`,`jobId`),
  ADD KEY `FK_Job` (`jobId`);

--
-- Klíče pro tabulku `relation`
--
ALTER TABLE `relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `male` (`maleId`) USING BTREE,
  ADD KEY `female` (`femaleId`) USING BTREE;

--
-- Klíče pro tabulku `twins`
--
ALTER TABLE `twins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people1_id` (`child1Id`),
  ADD KEY `people2_id` (`child2Id`),
  ADD KEY `parent_id` (`fatherId`),
  ADD KEY `mother_id` (`motherId`);

--
-- Klíče pro tabulku `wedding`
--
ALTER TABLE `wedding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people1_id` (`husbandId`),
  ADD KEY `people2_id` (`wifeId`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `genus`
--
ALTER TABLE `genus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `job`
--
ALTER TABLE `job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `name`
--
ALTER TABLE `name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `relation`
--
ALTER TABLE `relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `twins`
--
ALTER TABLE `twins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `wedding`
--
ALTER TABLE `wedding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `name`
--
ALTER TABLE `name`
  ADD CONSTRAINT `FK_Name_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`);

--
-- Omezení pro tabulku `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `father` FOREIGN KEY (`fatherId`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `mother` FOREIGN KEY (`motherId`) REFERENCES `people` (`id`);

--
-- Omezení pro tabulku `people2address`
--
ALTER TABLE `people2address`
  ADD CONSTRAINT `FK_People2Address_Address` FOREIGN KEY (`addressId`) REFERENCES `address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People2Address_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `people2job`
--
ALTER TABLE `people2job`
  ADD CONSTRAINT `FK_Job` FOREIGN KEY (`jobId`) REFERENCES `job` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_People` FOREIGN KEY (`peopleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `relation`
--
ALTER TABLE `relation`
  ADD CONSTRAINT `FK_Female` FOREIGN KEY (`femaleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Male` FOREIGN KEY (`maleId`) REFERENCES `people` (`id`) ON DELETE NO ACTION;

--
-- Omezení pro tabulku `twins`
--
ALTER TABLE `twins`
  ADD CONSTRAINT `FK_Child1` FOREIGN KEY (`child1Id`) REFERENCES `people` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Child2` FOREIGN KEY (`child2Id`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Father` FOREIGN KEY (`fatherId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Mother` FOREIGN KEY (`motherId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `wedding`
--
ALTER TABLE `wedding`
  ADD CONSTRAINT `FK_Husband` FOREIGN KEY (`husbandId`) REFERENCES `people` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Wife` FOREIGN KEY (`wifeId`) REFERENCES `people` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
