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

UPDATE config SET value="0.8" WHERE token='database_version';

INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'access_password', '', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'settings_password', '', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'language', 'en', SYSDATE(), 1);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'theme', 'light', SYSDATE(), 1);

UPDATE config SET options="light,dark" WHERE token="theme"; 
UPDATE config SET options="en,nl" WHERE token="language"; 