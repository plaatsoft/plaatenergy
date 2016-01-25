--
--  ===========
--  PlaatEnergy
--  ===========
--
--  Created by wplaat
--
--  For more information visit the following website.
--  Website : www.plaatsoft.nl 
--
--  Or send an email to the following address.
--  Email   : info@plaatsoft.nl
--
--  All copyrights reserved (c) 2008-2016 PlaatSoft
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `config` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `gas_prijs` double NOT NULL,
  `elektra_prijs` double NOT NULL,
  `start_dal` double NOT NULL,
  `start_piek` double NOT NULL,
  `start_gas` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `energy_day` (
`id` int(11) NOT NULL,
  `date` date NOT NULL,
  `dal` double NOT NULL,
  `piek` double NOT NULL,
  `dalterug` double NOT NULL,
  `piekterug` double NOT NULL,
  `solar` double NOT NULL,
  `gas` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sun` (
`id` int(11) NOT NULL,
  `date` varchar(5) NOT NULL,
  `sunrise` time NOT NULL,
  `sunset` time NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `weather` (
`id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `pressure` decimal(6,2) NOT NULL,
  `temperature` decimal(4,2) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

ALTER TABLE `config`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `energy`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

ALTER TABLE `energy_day`
 ADD PRIMARY KEY (`id`), ADD KEY `date` (`date`);

ALTER TABLE `solar`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

ALTER TABLE `sun`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `weather`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

ALTER TABLE `config`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `energy`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `energy_day`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `solar`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `sun`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `weather`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

INSERT INTO `config` (`id`, `date`, `gas_prijs`, `elektra_prijs`, `start_dal`, `start_piek`, `start_gas`) VALUES (1, SYSDATE(), 0.65, 0.23, 0, 0, 0);

