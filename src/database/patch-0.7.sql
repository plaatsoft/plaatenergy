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
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'chart_legend_on', 'none', SYSDATE(), 0);
INSERT INTO config (`id`, `token`, `value`, `date`, readonly) VALUES (NULL, 'chart_style', 'width:950px; height:300px', SYSDATE(), 0);

ALTER TABLE `config` DROP `description`;


