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

-- Sun set / sun rise table is not used anymore
DROP TABLE sun;

-- Refactor config table to a more flexible design
CREATE TABLE IF NOT EXISTS config2 (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(100),
  `readonly` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'database_version', '0.5', SYSDATE(),  1);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'request_counter', 1, SYSDATE(), 1);

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_meter_reading_low', '0', SYSDATE(), 0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_meter_reading_normal', '0', SYSDATE(), 0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'gas_meter_reading', '0', SYSDATE(),  0);

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_price', '0.23', SYSDATE(), 0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'gas_price', '0.65', SYSDATE(), 0);

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_use_forecast', '2899', SYSDATE(),  0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_delivery_forecast', '3012', SYSDATE(),  0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'gas_use_forecast', '956', SYSDATE(),  0);

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'energy_meter_present', 'true', SYSDATE(), 0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_present', 'true', SYSDATE(),  0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'weather_station_present', 'true', SYSDATE(), , 0);

INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_ip', '127.0.0.1', SYSDATE(),  0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_port', '8899', SYSDATE(), 0);
INSERT INTO config2 (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_serial_number', '1606789503', SYSDATE(), 0);

UPDATE config2 SET value = ( SELECT start_dal FROM config), date=( SELECT date FROM config) WHERE config2.token = 'energy_meter_reading_low';
UPDATE config2 SET value = ( SELECT start_piek FROM config), date=( SELECT date FROM config) WHERE config2.token = 'energy_meter_reading_normal';
UPDATE config2 SET value = ( SELECT start_gas FROM config), date=( SELECT date FROM config) WHERE config2.token = 'gas_meter_reading';
UPDATE config2 SET value = ( SELECT elektra_prijs FROM config), date=( SELECT date FROM config) WHERE config2.token = 'energy_price';
UPDATE config2 SET value = ( SELECT gas_prijs FROM config), date=( SELECT date FROM config) WHERE config2.token = 'gas_price';

DROP TABLE config;

ALTER TABLE config2 rename TO config;

