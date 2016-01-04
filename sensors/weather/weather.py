#!/usr/bin/python
from sense_hat import SenseHat
import time
import _mysql
import sys
from time import strftime

sense = SenseHat()

temp1 = sense.get_temperature_from_pressure()
temp2 = sense.get_temperature_from_humidity()
temp = (temp1 + temp2) / 2
pressure = sense.get_pressure()-4.6
humidity = sense.get_humidity()

try:
    con = _mysql.connect('localhost', 'power', 'power', 'power')

    sql = "insert into weather( timestamp,humidity,pressure,temperature) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3})".format( strftime('%d-%m-%Y %H:%M:00', time.localtime()), humidity, pressure, temp)

    con.query(sql)

except _mysql.Error, e:

    print "Error %d: %s %s " % (e.args[0], e.args[1], sql)
    sys.exit(1)

finally:

    if con:
        con.close()

