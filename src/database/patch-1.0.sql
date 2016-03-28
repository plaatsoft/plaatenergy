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

ALTER TABLE energy RENAME TO energy1; 
ALTER TABLE energy1 CHANGE `dal` `low_used` DOUBLE NOT NULL;
ALTER TABLE energy1 CHANGE `piek` `normal_used` DOUBLE NOT NULL;
ALTER TABLE energy1 CHANGE `dalterug` `low_delivered` DOUBLE NOT NULL;
ALTER TABLE energy1 CHANGE `piekterug` `normal_delivered` DOUBLE NOT NULL;
ALTER TABLE energy1 CHANGE `vermogen` `power` DOUBLE NOT NULL;
ALTER TABLE energy1 CHANGE `gas` `gas_used` DOUBLE NOT NULL;
UPDATE energy1 SET power = (vermogenterug*-1) where vermogenterug>0;
ALTER TABLE energy1 DROP vermogenterug;

ALTER TABLE energy_day RENAME TO energy_summary; 
ALTER TABLE energy_summary CHANGE `dal` `low_used` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `piek` `normal_used` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `dalterug` `low_delivered` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `piekterug` `normal_delivered` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `vermogen` `power` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `solar` `solar_delivered` DOUBLE NOT NULL;
ALTER TABLE energy_summary CHANGE `gas` `gas_used` DOUBLE NOT NULL;

ALTER TABLE config ADD rebuild INT NOT NULL AFTER readonly;

UPDATE config SET rebuild=1 WHERE token="meter_reading_used_low";
UPDATE config SET rebuild=1 WHERE token="meter_reading_used_normal";
UPDATE config SET rebuild=1 WHERE token="meter_reading_delivered_low";
UPDATE config SET rebuild=1 WHERE token="meter_reading_delivered_normal";
UPDATE config SET rebuild=1 WHERE token="meter_reading_used_gas";
UPDATE config SET rebuild=1 WHERE token="solar_meter_present";
UPDATE config SET rebuild=1 WHERE token="energy_meter_present";

INSERT INTO config (token, value, date, readonly, rebuild) VALUES ('gas_meter_present', 'true', SYSDATE(), 0, 1);
UPDATE config SET options="true,false" WHERE token="gas_meter_present"; 

ALTER TABLE solar RENAME TO solar1; 

ALTER TABLE `config` ADD `category` INT NOT NULL AFTER `id`;
UPDATE config SET category=11 WHERE token="gas_meter_present";
UPDATE config SET category=11 WHERE token="gas_price";
UPDATE config SET category=11 WHERE token="meter_reading_used_gas";

UPDATE config SET category=21 WHERE token="energy_meter_present";
UPDATE config SET category=21 WHERE token="energy_meter_vendor";
UPDATE config SET category=21 WHERE token="energy_price";
UPDATE config SET category=21 WHERE token="meter_reading_delivered_low";
UPDATE config SET category=21 WHERE token="meter_reading_delivered_normal";
UPDATE config SET category=21 WHERE token="meter_reading_used_low";
UPDATE config SET category=21 WHERE token="meter_reading_used_normal";

UPDATE config SET category=31 WHERE token="solar_meter_present";
UPDATE config SET category=31 WHERE token="solar_meter_vendor";
UPDATE config SET category=31 WHERE token="solar_meter_port";
UPDATE config SET category=31 WHERE token="solar_meter_ip";
UPDATE config SET category=31 WHERE token="solar_meter_serial_number";

UPDATE config SET category=41 WHERE token="weather_station_present";

UPDATE config SET category=51 WHERE token="settings_password";
UPDATE config SET category=51 WHERE token="home_password";

UPDATE config SET category=52 WHERE token="chart_dimensions";
UPDATE config SET category=52 WHERE token="chart_legend";
UPDATE config SET category=52 WHERE token="slide_show_on";
UPDATE config SET category=52 WHERE token="slide_show_page_delay";


INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_meter_present', 'false', 'true,false', 0, 1);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_meter_vendor', 'unknown', 'unknown,omnik', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_meter_port', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_meter_ip', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_meter_serial_number', '', '', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_meter_present', 'false', 'true,false', 0, 1);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_meter_vendor', 'unknown', 'unknown,omnik', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_meter_port', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_meter_ip', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_meter_serial_number', '', '', 0, 0);

UPDATE config SET options='unknown,omnik,hosola' WHERE token="solar_meter_vendor" and category=31;
UPDATE config SET options='unknown,omnik,hosola' WHERE token="solar_meter_vendor" and category=32;
UPDATE config SET options='unknown,omnik,hosola' WHERE token="solar_meter_vendor" and category=33;

CREATE TABLE IF NOT EXISTS `solar2` (
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

ALTER TABLE `solar2`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

ALTER TABLE `solar2`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `solar3` (
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

ALTER TABLE `solar3`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

ALTER TABLE `solar3`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;



