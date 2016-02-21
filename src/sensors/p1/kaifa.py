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

# Kaifa with gas meter P1 string
#
# /KFM5KAIFA-METER
# 
# 1-3:0.2.8(42)
# 0-0:1.0.0(160221094737W)
# 0-0:96.1.1(4530303235313030303331373337343135)
# 1-0:1.8.1(000464.336*kWh)												Totaal verbruik tarief 1 (nacht)
# 1-0:1.8.2(000556.850*kWh)												Totaal verbruik tarief 1 (dag)
# 1-0:2.8.1(000183.181*kWh)												Totaal verbruik tarief 2 (nacht)
# 1-0:2.8.2(000581.870*kWh)												Totaal verbruik tarief 2 (dag)
# 0-0:96.14.0(0001)															Actuele tarief (1)
# 1-0:1.7.0(00.175*kW)
# 1-0:2.7.0(00.000*kW)
# 0-0:96.7.21(00014)
# 0-0:96.7.9(00009)
# 1-0:99.97.0(1)(0-0:96.7.19)(000101000001W)(2147483647*s)
# 1-0:32.32.0(00000)
# 1-0:32.36.0(00000)
# 0-0:96.13.1()
# 0-0:96.13.0()
# 1-0:31.7.0(001*A)
# 1-0:21.7.0(00.162*kW)
# 1-0:22.7.0(00.000*kW)
# 0-1:24.1.0(003)
# 0-1:96.1.0(4730303332353631323335313832373135)
# 0-1:24.2.1(160221090000W)(00295.739*m3)
# !1102

ser          = serial.Serial()
ser.baudrate = 115200
ser.bytesize = serial.EIGHTBITS
ser.parity   = serial.PARITY_NONE
ser.stopbits = serial.STOPBITS_ONE
ser.xonxoff  = 1
ser.rtscts   = 0

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
    elif elif p1_line[0:1] == "!":
        if p1_telegram:
            p1_log      = False	

  #Close port and show status
  try:
    ser.close()
  except:
    sys.exit ("Error closing serial port %s" % ser.name )      

  stack_teller=0
  while stack_teller < len(stack):
   if stack[stack_teller][0:9] == "1-0:1.8.1":
      dal = float(stack[stack_teller][10:20])
   elif stack[stack_teller][0:9] == "1-0:1.8.2":
      normal = float(stack[stack_teller][10:20])
   elif stack[stack_teller][0:9] == "1-0:2.8.1":
      dalterug = float(stack[stack_teller][10:20])
   elif stack[stack_teller][0:9] == "1-0:2.8.2":
      normalterug = float(stack[stack_teller][10:20])
   elif stack[stack_teller][0:9] == "1-0:1.7.0":
      vermogen = int(float(stack[stack_teller][10:16])*1000)
   elif stack[stack_teller][0:9] == "1-0:2.7.0":
      vermogenterug = int(float(stack[stack_teller][10:16])*1000)
   elif stack[stack_teller][0:10] == "0-1:24.2.1":
      gas = float(stack[stack_teller][26:35])
   stack_teller = stack_teller +1

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

  sql = "insert into energy( timestamp,dal,piek,dalterug,piekterug,vermogen,vermogenterug,gas) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3},{4},{5},{6},{7})".format( strftime('%d-%m-%Y %H:%M:00', time.localtime()), dal, normal, dalterug, normalterug, vermogen, vermogenterug, gas)

  con.query(sql)

#
# ---------------------
# THE END
# ---------------------
#
