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
from time import strftime

#Set COM port config
ser          = serial.Serial()

# Kaifa meter settings
ser.baudrate = 115200
ser.bytesize = serial.EIGHTBITS
ser.parity   = serial.PARITY_NONE
ser.stopbits = serial.STOPBITS_ONE
ser.xonxoff  = 1
ser.rtscts   = 0

# Kamstrup meter settings
#ser.baudrate = 9600
#ser.bytesize = serial.SEVENBITS
#ser.parity   = serial.PARITY_EVEN
#ser.stopbits = serial.STOPBITS_ONE
#ser.xonxoff  = 0
#ser.rtscts   = 0

# Landis meter settings
#ser.baudrate = 9600
#ser.bytesize = serial.SEVENBITS
#ser.parity   = serial.PARITY_EVEN
#ser.stopbits = serial.STOPBITS_ONE
#ser.xonxoff  = 0
#ser.rtscts   = 0

ser.timeout  = 20
ser.port     = "/dev/ttyUSB0"

# ---------------------------------
# DO NOT CHANGE ANYTHING BELOW HERE 
# ---------------------------------

p1_telegram  = False
p1_log       = True

print 'Start serial analyse';

#Open COM port
try:
  ser.open()
except:
  sys.exit ("Error during opening serial port. %s"  % ser.name)      

while p1_log:
    p1_line = ''
    try:
        p1_raw = ser.readline()
    except:
        sys.exit ("Fout bij het lezen van poort %s. " % ser.name )
        ser.close()

    p1_str  = p1_raw
    p1_str  = str(p1_raw)
    p1_line = p1_str.strip()

    if p1_line[0:1] == "/":
        p1_telegram = True
        line = 1
    elif p1_line[0:1] == "!":
        if p1_telegram:
            p1_telegram = False 
            p1_log      = False	

    print (p1_line)

try:
  ser.close()
except:
  sys.exit ("Error during closing serial port. %s" % ser.name )      

print 'End serial analyse';

#
# ---------------------
# THE END
# ---------------------
#
