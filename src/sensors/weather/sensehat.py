#!/usr/bin/python

# 
#  ===========
#  PlaatEnergy
#  ===========
#
#  Created by wplaat
#
#  For more information visit the following website.
#  Website : www.plaatsoft.nl 
#
#  Or send an email to the following address.
#  Email   : info@plaatsoft.nl
#
#  All copyrights reserved (c) 2008-2016 PlaatSoft
#

from sense_hat import SenseHat
import time
import _mysql
import sys
from time import strftime
import ConfigParser


lines = [line.rstrip('\n') for line in open('/var/www/html/plaatenergy/config.inc')]
for line in lines:
   if line[:1]=='$':
     line = line.replace(' ','');
     line = line.replace(';','');
     line = line.replace('$','');
     line = line.replace('"','');
     key = line.split('=')
     if key[0]=='dbhost':
        dbhost=key[1]
     if key[0]=='dbname':
        dbname=key[1]
     if key[0]=='dbuser':
        dbuser=key[1]
     if key[0]=='dbpass':
        dbpass=key[1]

con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

sense = SenseHat()
	
temp1 = sense.get_temperature_from_pressure()
temp2 = sense.get_temperature_from_humidity()
temp = (temp1 + temp2) / 2
pressure = sense.get_pressure()-4.6
humidity = sense.get_humidity()

con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

sql = "insert into weather( timestamp,humidity,pressure,temperature) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3})".format( strftime('%d-%m-%Y %H:%M:00', time.localtime()), humidity, pressure, temp)
con.query(sql)
con.close

#
# ---------------------
# THE END
# ---------------------
#

