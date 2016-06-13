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

UPDATE config SET value="1.2" WHERE token='database_version';

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (61, 'webcam_name', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (61, 'webcam_description', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (61, 'webcam_resolution', '320x240', '320x240,640x480', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (61, 'webcam_present', 'false', 'true,false', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (61, 'webcam_device', '/dev/video0', '/dev/video0,/dev/video1,/dev/video2', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (62, 'webcam_name', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (62, 'webcam_description', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (62, 'webcam_resolution', '320x240', '320x240,640x480', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (62, 'webcam_present', 'false', 'true,false', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (62, 'webcam_device', '/dev/video1', '/dev/video0,/dev/video1,/dev/video2', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (71, 'hue_description', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (71, 'hue_present', 'false', 'true,false', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (71, 'hue_ip_address', '', '', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (71, 'hue_key', '', '', 0, 0);

INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (81, 'notification_present', 'false', 'true,false', 0, 0);
INSERT INTO config (category, token, value, options, readonly, rebuild) VALUES (81, 'notification_nma_key', '', '', 0, 0);

ALTER TABLE config ADD encrypt INT NOT NULL AFTER `rebuild`;
UPDATE config SET encrypt = 1, value="" WHERE token = "home_password";
UPDATE config SET encrypt = 1, value="" WHERE token = "settings_password";

