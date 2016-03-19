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

