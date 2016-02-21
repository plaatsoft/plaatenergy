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

print 'Start file analyse';

stack=[]
with open("kaifa.txt") as f:
   for line in f:
     stack.append(line);

stack_teller=0

while stack_teller < len(stack):
   print stack[stack_teller]
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

print "==================";
print "Dal meterstand = {0:.3f}".format(dal);
print "Normal meterstand = {0:.3f}".format(normal);
print "DalTerug meterstand = {0:.3f}".format(dalterug);
print "NormalTerug meterstand = {0:.3f}".format(normalterug);
print "Vermogen (actueel) = {0:.3f}".format(vermogen);
print "VermogenTerug (actueel) = {0:.3f}".format(vermogenterug);
print "Gas meterstand = {0:.3f}".format(gas);
print "==================";
  
print 'End file analyse';

#
# ---------------------
# THE END
# ---------------------
#
