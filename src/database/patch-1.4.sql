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
--  All copyrights reserved (c) 2008-2018 PlaatSoft
--

UPDATE config SET value="1.4" WHERE token='database_version';

CREATE TABLE `energy1_details` (
  `pid` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `voltage_f1` double NOT NULL,
  `voltage_f2` double NOT NULL,
  `voltage_f3` double NOT NULL,
  `current_f1` double NOT NULL,
  `current_f2` double NOT NULL,
  `current_f3` double NOT NULL,
  `power_f1` double NOT NULL,
  `power_f2` double NOT NULL,
  `power_f3` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `energy1_details` ADD PRIMARY KEY (`pid`);

ALTER TABLE `energy1_details` MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO config (category, token, value, options, date, readonly, rebuild, encrypt) VALUES (21, 'energy_store_details', 'false', 'true,false', SYSDATE(), 0, 0, 0);

UPDATE `config` SET `options` = 'kaifa,landis,kamstrup,landis-e350' WHERE `config`.`id` = 21;