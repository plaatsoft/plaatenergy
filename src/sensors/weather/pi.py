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

import time
import _mysql
import sys
import os
from time import strftime
import ConfigParser

# Return CPU temperature as a character string                                      
def getCPUtemperature():
    res = os.popen('vcgencmd measure_temp').readline()
    return(res.replace("temp=","").replace("'C\n",""))

lines = [line.rstrip('\n') for line in open('/var/www/html/plaatenergy/config.php')]
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

temp = getCPUtemperature();
pressure = 0 
humidity = 0 

con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

sql = "insert into weather( timestamp,humidity,pressure,temperature) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3})".format( strftime('%d-%m-%Y %H:%M:00', time.localtime()), humidity, pressure, temp)
con.query(sql)
con.close

#
# ---------------------
# THE END
# ---------------------
#

