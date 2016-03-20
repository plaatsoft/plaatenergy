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
import _mysql
import datetime

sense = SenseHat()

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

sense = SenseHat()

now = datetime.datetime.now();
con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

try:
    sql = "select power from energy1 where timestamp='" +now.strftime("%Y-%m-%d %H:%M:00")+"'"
    con.query(sql)
    result = con.use_result()
    row = result.fetch_row()[0]
    power = row[0]
    con.close

    value = vermogen + " Watt"  

    sense.set_rotation(180)
    sense.show_message(value, scroll_speed=0.1, text_colour=[255,255,0], back_colour=[0,0,0])

except:
    print "no data available!"  


#
# ---------------------
# THE END
# ---------------------
#

