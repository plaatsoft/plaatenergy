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
--  All copyrights reserved (c) 2008-2018 PlaatSoft
--

UPDATE config SET value="1.5" WHERE token='database_version';

CREATE TABLE `solar_history` (
  `sid` int(11) NOT NULL,
  `date` date NOT NULL,
  `energy` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `solar_history` ADD PRIMARY KEY (`sid`);
ALTER TABLE `solar_history` MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;