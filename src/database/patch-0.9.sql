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

UPDATE config SET value="0.9" WHERE token='database_version';

INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'home_password', '', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'settings_password', '', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'language', 'en', SYSDATE(), 1);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'theme', 'light', SYSDATE(), 1);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'session_id', '', SYSDATE(), 1);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_vendor', 'unknown', SYSDATE(), 0);

UPDATE config SET options="light,dark" WHERE token="theme"; 
UPDATE config SET options="en,nl" WHERE token="language"; 
UPDATE config SET options="unknown,omnik" WHERE token="solar_meter_vendor"; 


uPDATE config SET token="home_password" WHERE token="access_password"; 