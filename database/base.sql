-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Gegenereerd op: 05 jan 2016 om 18:20
-- Serverversie: 5.5.44-0+deb8u1
-- PHP-versie: 5.6.14-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `power`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `config`
--

CREATE TABLE IF NOT EXISTS `config` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `gas_prijs` double NOT NULL,
  `elektra_prijs` double NOT NULL,
  `start_dal` double NOT NULL,
  `start_piek` double NOT NULL,
  `start_gas` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `energy`
--

CREATE TABLE IF NOT EXISTS `energy` (
`id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `dal` double NOT NULL,
  `piek` double NOT NULL,
  `dalterug` double NOT NULL,
  `piekterug` double NOT NULL,
  `vermogen` double NOT NULL,
  `vermogenterug` double NOT NULL,
  `gas` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=175230 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `energy_day`
--

CREATE TABLE IF NOT EXISTS `energy_day` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `dal` double NOT NULL,
  `piek` double NOT NULL,
  `dalterug` double NOT NULL,
  `piekterug` double NOT NULL,
  `solar` double NOT NULL,
  `gas` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=150 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `solar`
--

CREATE TABLE IF NOT EXISTS `solar` (
`id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `vdc1` double NOT NULL,
  `idc1` double NOT NULL,
  `vdc2` double NOT NULL,
  `idc2` double NOT NULL,
  `vac` double NOT NULL,
  `iac` double NOT NULL,
  `pac` double NOT NULL,
  `fac` double NOT NULL,
  `etoday` double NOT NULL,
  `etotal` double NOT NULL,
  `temp` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=68312 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sun`
--

CREATE TABLE IF NOT EXISTS `sun` (
`id` int(11) NOT NULL,
  `date` varchar(5) NOT NULL,
  `sunrise` time NOT NULL,
  `sunset` time NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `weather`
--

CREATE TABLE IF NOT EXISTS `weather` (
`id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `pressure` decimal(6,2) NOT NULL,
  `temperature` decimal(4,2) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10232 DEFAULT CHARSET=latin1;

--
-- Indexen voor geëorteerde tabellen
--

--
-- Indexen voor tabel `config`
--
ALTER TABLE `config`
 ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `energy`
--
ALTER TABLE `energy`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexen voor tabel `energy_day`
--
ALTER TABLE `energy_day`
 ADD PRIMARY KEY (`id`), ADD KEY `date` (`date`);

--
-- Indexen voor tabel `solar`
--
ALTER TABLE `solar`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexen voor tabel `sun`
--
ALTER TABLE `sun`
 ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `weather`
--
ALTER TABLE `weather`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- AUTO_INCREMENT voor geëorteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `config`
--
ALTER TABLE `config`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT voor een tabel `energy`
--
ALTER TABLE `energy`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=175230;
--
-- AUTO_INCREMENT voor een tabel `energy_day`
--
ALTER TABLE `energy_day`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=150;
--
-- AUTO_INCREMENT voor een tabel `solar`
--
ALTER TABLE `solar`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=68312;
--
-- AUTO_INCREMENT voor een tabel `sun`
--
ALTER TABLE `sun`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT voor een tabel `weather`
--
ALTER TABLE `weather`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10232;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

