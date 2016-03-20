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
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'solar_meter_vendor', 'unknown', SYSDATE(), 0);

UPDATE config SET options="light,dark" WHERE token="theme"; 
UPDATE config SET options="en,nl" WHERE token="language"; 
UPDATE config SET options="unknown,omnik" WHERE token="solar_meter_vendor"; 

CREATE TABLE IF NOT EXISTS `session` (
  `sid` int(11) NOT NULL,
   `ip` varchar(20) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL, 
  `requests` INT NOT NULL,
  `language` VARCHAR(10),
  `theme` VARCHAR(10)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `session`
 ADD PRIMARY KEY (`sid`);
 
ALTER TABLE `session`
 MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT;
 
DELETE FROM config WHERE token="request_counter";