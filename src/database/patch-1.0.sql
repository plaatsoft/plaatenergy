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

UPDATE config SET value="1.0" WHERE token='database_version';

CREATE TABLE IF NOT EXISTS `energy1` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `low_used` double NOT NULL,
  `normal_used` double NOT NULL,
  `low_delivered` double NOT NULL,
  `normal_delivered` double NOT NULL,
  `power_used` double NOT NULL,
  `power_delivered` double NOT NULL,
  `gas_used` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `energy1`
 ADD PRIMARY KEY (`id`);
 
ALTER TABLE `energy1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
 
ALTER TABLE `energy1` ADD UNIQUE(`id`);
 
CREATE TABLE IF NOT EXISTS `energy2` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `low_used` double NOT NULL,
  `normal_used` double NOT NULL,
  `low_delivered` double NOT NULL,
  `normal_delivered` double NOT NULL,
  `power` double NOT NULL,
  `gas_used` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


ALTER TABLE `energy2`
 ADD PRIMARY KEY (`id`);
 
ALTER TABLE `energy2`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

-- Migrate data to new structure
INSERT INTO energy1 (timestamp, low_used, normal_used, low_delivered, normal_delivered, power_used, power_delivered, gas_used)
SELECT timestamp, dal, piek, dalterug, piekterug, vermogen, vermogenterug, gas FROM energy;
UPDATE energy1 SET power_used = (power_delivered*-1) where power_delivered>0;
TABLE energy1 DROP power_delivered;
ALTER TABLE energy1 CHANGE power_used power DOUBLE NOT NULL;

-- Add new configuration items
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_meter_vendor1', 'none', SYSDATE(), 0);
UPDATE config SET options="none,kaifa,landis,kamstrup" WHERE token="energy_meter_vendor1"; 

INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_meter_vendor2', 'none', SYSDATE(), 0);
UPDATE config SET options="none,kaifa,landis,kamstrup" WHERE token="energy_meter_vendor2"; 

DELETE FROM config WHERE token="energy_meter_present";


CREATE TABLE IF NOT EXISTS `energy_summary` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `low_used` double NOT NULL,
  `normal_used` double NOT NULL,
  `low_delivered` double NOT NULL,
  `normal_delivered` double NOT NULL,
  `solar_delivered` double NOT NULL,
  `gas_used` double NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

ALTER TABLE `energy_summary`
 ADD PRIMARY KEY (`id`);
 
ALTER TABLE `energy_summary`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

-- Migrate data to new structure
INSERT INTO energy_summary (date, low_used, normal_used, low_delivered, normal_delivered, solar_delivered, gas_used)
SELECT date, dal, piek, dalterug, piekterug, solar, gas FROM energy_day;
