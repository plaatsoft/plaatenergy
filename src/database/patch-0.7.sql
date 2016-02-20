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

UPDATE config SET value="0.7" WHERE token='database_version';

INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'slide_show_on', 'false', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'slide_show_page_delay', '10', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'chart_legend', 'none', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'chart_dimensions', 'width:950px; height:300px', SYSDATE(), 0);

ALTER TABLE config DROP description;
ALTER TABLE config ADD options VARCHAR(255) NOT NULL AFTER value;
UPDATE config SET options="true,false" WHERE token="energy_meter_present"; 
UPDATE config SET options="true,false" WHERE token="solar_meter_present"; 
UPDATE config SET options="true,false" WHERE token="weather_meter_present"; 
UPDATE config SET options="true,false" WHERE token="slide_show_on"; 
UPDATE config SET options="right,none" WHERE token="chart_dimensions"; 


