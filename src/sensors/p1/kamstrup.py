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

import sys
import os
import stat
import serial
import datetime
import locale
import time
import csv 
import _mysql
from time import strftime

ser          = serial.Serial()
ser.baudrate = 9600
ser.bytesize = serial.SEVENBITS
ser.parity   = serial.PARITY_EVEN
ser.stopbits = serial.STOPBITS_ONE
ser.xonxoff  = 0
ser.rtscts   = 1

ser.port     = "/dev/ttyUSB0"
ser.timeout  = 20

# ---------------------------------
# DO NOT CHANGE ANYTHING BELOW HERE
# ---------------------------------

p1_telegram  = False
p1_log       = True

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

sql = "select value from config where token='energy_meter_present'"
con.query(sql)
result = con.use_result()
value = result.fetch_row()[0]
con.close

if value[0] == 'true':

  stack=[]

  #Open COM port
  try:
    ser.open()
  except:
    sys.exit ("Error during opening serial port %s"  % ser.name)      

  line = 0 
  while p1_log:
    p1_line = ''
    try:
        p1_raw = ser.readline()
    except:
        sys.exit ("Error reading from serial port %s" % ser.name )
        ser.close()

    p1_str  = p1_raw
    p1_str  = str(p1_raw)
    p1_line = p1_str.strip()
    stack.append(p1_line);
    line = line + 1

    if p1_line[0:1] == "/":
        p1_telegram = True
    elif p1_line[0:1] == "!":
        if p1_telegram:
            p1_log      = False	

  #Close port and show status
  try:
    ser.close()
  except:
    sys.exit ("Error closing serial port %s" % ser.name )      

  stack_teller=0

  while stack_teller < len(stack):
     print stack[stack_teller]
     if stack[stack_teller][0:9] == "1-0:1.8.1":
        dal = float(stack[stack_teller][10:19])
     elif stack[stack_teller][0:9] == "1-0:1.8.2":
        normal = float(stack[stack_teller][10:19])
     elif stack[stack_teller][0:9] == "1-0:2.8.1":
        dalterug = float(stack[stack_teller][10:19])
     elif stack[stack_teller][0:9] == "1-0:2.8.2":
        normalterug = float(stack[stack_teller][10:19])
     elif stack[stack_teller][0:9] == "1-0:1.7.0":
        vermogen = int(float(stack[stack_teller][10:17])*1000)
     elif stack[stack_teller][0:9] == "1-0:2.7.0":
        vermogenterug = int(float(stack[stack_teller][10:17])*1000)
     elif stack[stack_teller][0:10] == "0-1:24.3.0":
        gas = float(stack[stack_teller+1][1:10])
     stack_teller = stack_teller +1

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

  sql = "insert into energy( timestamp,dal,piek,dalterug,piekterug,vermogen,vermogenterug,gas) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3},{4},{5},{6},{7})".format( strftime('%d-%m-%Y %H:%M:00', time.localtime()), dal, normal, dalterug, normalterug, vermogen, vermogenterug, gas)

  con.query(sql)

#
# ---------------------
# THE END
