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

-- Sun table is not used anymore
DROP TABLE sun;

-- Refactor config table to a more flexible design
CREATE TABLE IF NOT EXISTS config2 (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(25),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'database_version', '0.5', SYSDATE(), 'Current database version');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_meter_reading_low', '0', SYSDATE(),'Energy meter reading - low tariff (kwh)');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_meter_reading_normal', '0', SYSDATE(), 'Energy meter reading - normal tariff (kwh)');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'gas_meter_reading', '0', SYSDATE(), 'Gas meter reading (m3)');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_price', '0', SYSDATE(),'Energy price per kwh');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'gas_price', '0', SYSDATE(),'Gas price per m3');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_use_forecast', '2899', SYSDATE(), 'Energy use forecast (kWh)');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_delivery_forecast', '3012', SYSDATE(), 'Energy delivery forecast (kWh)');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'gas_use_forecast_m3', '956', SYSDATE(), 'Gas use forecast (m3)');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'energy_meter_present', 'true', SYSDATE(), 'Energy meter present');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'solar_meter_present', 'true', SYSDATE(), 'Solar meter present');
INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'weather_station_present', 'true', SYSDATE(), 'Weather Station present');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'solar_meter_ip_address', '192.168.0.201', SYSDATE(), 'Solar meter IP address');

INSERT INTO config2 (`id`, `key`, `value`, `date`, `description`) VALUES (NULL, 'request_counter', 1, SYSDATE(), 'Page request counter');

UPDATE config2 SET value = ( SELECT start_dal FROM config), date=( SELECT date FROM config) WHERE config2.key = 'energy_meter_reading_low';
UPDATE config2 SET value = ( SELECT start_piek FROM config), date=( SELECT date FROM config) WHERE config2.key = 'energy_meter_reading_normal';
UPDATE config2 SET value = ( SELECT start_gas FROM config), date=( SELECT date FROM config) WHERE config2.key = 'gas_meter_reading';
UPDATE config2 SET value = ( SELECT elektra_prijs FROM config), date=( SELECT date FROM config) WHERE config2.key = 'energy_price';
UPDATE config2 SET value = ( SELECT gas_prijs FROM config), date=( SELECT date FROM config) WHERE config2.key = 'gas_price';

DROP TABLE config;

ALTER TABLE config2 rename TO config;

