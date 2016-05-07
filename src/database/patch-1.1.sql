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

UPDATE config SET value="1.1" WHERE token='database_version';

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (52, 'system_name', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (41, 'weather_station_vendor', 'pi', 'pi,sensehat', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (31, 'solar_description', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_description', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_description', '', '', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (31, 'solar_peak_power', '0', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (32, 'solar_peak_power', '0', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (33, 'solar_peak_power', '0', '', 0, 0);





