-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Ned 23. srp 2020, 14:45
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
-- Databáze: `familytree`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `adress`
--

CREATE TABLE `adress` (
  `id` int(11) NOT NULL,
  `street` varchar(1024) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL,
  `street_number` int(11) DEFAULT NULL,
  `house_number` int(11) DEFAULT NULL,
  `zip` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL,
  `town` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci DEFAULT NULL,
  `date_since` date NOT NULL,
  `date_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Adresses of people';

--
-- Vypisuji data pro tabulku `adress`
--

INSERT INTO `adress` (`id`, `street`, `street_number`, `house_number`, `zip`, `town`, `date_since`, `date_to`) VALUES
(1, 'Budovatelů', 2407, 20, '43401', 'Most', '1988-02-01', '2018-05-01'),
(2, 'Jaroslava Vrchlického', 2669, 12, '43401', 'Most', '2020-05-01', NULL),
(3, 'Nad pekárnami', 12, 0, '', 'Praha', '2020-05-01', NULL),
(4, 'Budovatelů', 2407, 20, '43401', 'Most', '1993-05-19', '2020-05-01'),
(5, 'Budovatelů', 2407, 20, '43401', 'Most', '1980-05-01', '2019-07-01'),
(6, 'Budovatelů', 2407, 20, '43401', 'Most', '1980-05-01', '2019-07-01');

-- --------------------------------------------------------

--
-- Struktura tabulky `genus`
--

CREATE TABLE `genus` (
  `id` int(11) NOT NULL,
  `lastname` varchar(512) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Genuses of people';

-- --------------------------------------------------------

--
-- Struktura tabulky `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `date_since` date NOT NULL,
  `date_to` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Jobs of people';

-- --------------------------------------------------------

--
-- Struktura tabulky `names`
--

CREATE TABLE `names` (
  `id` int(11) NOT NULL,
  `people_id` int(11) NOT NULL,
  `name` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL,
  `surname` varchar(512) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL,
  `date_since` date NOT NULL,
  `date_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Names of people';

--
-- Vypisuji data pro tabulku `names`
--

INSERT INTO `names` (`id`, `people_id`, `name`, `surname`, `date_since`, `date_to`) VALUES
(1, 2, 'Alena', 'Kristková', '1967-07-26', '1984-05-01');

-- --------------------------------------------------------

--
-- Struktura tabulky `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `sex` char(1) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `name_day` date NOT NULL,
  `birdth_date` date NOT NULL,
  `death_date` date DEFAULT NULL,
  `mother_id` int(11) DEFAULT NULL,
  `father_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Main table with people';

--
-- Vypisuji data pro tabulku `people`
--

INSERT INTO `people` (`id`, `sex`, `name`, `surname`, `name_day`, `birdth_date`, `death_date`, `mother_id`, `father_id`) VALUES
(1, 'm', 'Stanislav', 'Babický', '0000-00-00', '1957-02-12', NULL, 0, 0),
(2, 'f', 'Alena', 'Babická', '2000-08-14', '1962-07-26', NULL, 0, 0),
(3, 'm', 'Tomáš', 'Babický', '2000-07-04', '1993-05-19', NULL, 2, 0),
(4, 'f', 'Lenka', 'Babická', '0000-00-00', '1988-02-01', NULL, 2, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `people2adress`
--

CREATE TABLE `people2adress` (
  `id` int(11) NOT NULL,
  `people_id` int(11) NOT NULL,
  `adress_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='adresses of peoples';

-- --------------------------------------------------------

--
-- Struktura tabulky `people2jobs`
--

CREATE TABLE `people2jobs` (
  `id` int(11) NOT NULL,
  `people_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='people and theirs jobs';

-- --------------------------------------------------------

--
-- Struktura tabulky `relations`
--

CREATE TABLE `relations` (
  `id` int(11) NOT NULL,
  `people1_id` int(11) NOT NULL,
  `people2_id` int(11) NOT NULL,
  `date_since` date DEFAULT NULL,
  `date_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='relation of peoples';

-- --------------------------------------------------------

--
-- Struktura tabulky `twins`
--

CREATE TABLE `twins` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `people1_id` int(11) NOT NULL,
  `people2_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Borned as a twins';

-- --------------------------------------------------------

--
-- Struktura tabulky `wedding`
--

CREATE TABLE `wedding` (
  `id` int(11) NOT NULL,
  `people1_id` int(11) NOT NULL,
  `people2_id` int(11) NOT NULL,
  `since_date` date NOT NULL,
  `to_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Wedding of people';

--
-- Vypisuji data pro tabulku `wedding`
--

INSERT INTO `wedding` (`id`, `people1_id`, `people2_id`, `since_date`, `to_date`) VALUES
(1, 1, 2, '1970-05-01', NULL);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `adress`
--
ALTER TABLE `adress`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `genus`
--
ALTER TABLE `genus`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `names`
--
ALTER TABLE `names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people_id` (`people_id`);

--
-- Klíče pro tabulku `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `people2adress`
--
ALTER TABLE `people2adress`
  ADD PRIMARY KEY (`people_id`,`adress_id`);

--
-- Klíče pro tabulku `people2jobs`
--
ALTER TABLE `people2jobs`
  ADD PRIMARY KEY (`people_id`,`job_id`);

--
-- Klíče pro tabulku `relations`
--
ALTER TABLE `relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people1_id` (`people1_id`),
  ADD KEY `people2_id` (`people2_id`);

--
-- Klíče pro tabulku `twins`
--
ALTER TABLE `twins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people1_id` (`people1_id`),
  ADD KEY `people2_id` (`people2_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Klíče pro tabulku `wedding`
--
ALTER TABLE `wedding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people1_id` (`people1_id`),
  ADD KEY `people2_id` (`people2_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `adress`
--
ALTER TABLE `adress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `genus`
--
ALTER TABLE `genus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `names`
--
ALTER TABLE `names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `relations`
--
ALTER TABLE `relations`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
