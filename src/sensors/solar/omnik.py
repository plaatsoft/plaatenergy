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

import InverterMsg 
import sys, os
import socket               
import ConfigParser
import time 
from time import strftime
import _mysql
import syslog

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

sql = "select value from config where token='solar_meter_present'"
con.query(sql)
result = con.use_result()
value = result.fetch_row()[0]
con.close

if value[0] == 'true':

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)
  sql = "select value from config where token='solar_meter_ip'"
  con.query(sql)
  result = con.use_result()
  value = result.fetch_row()[0]
  ip = value[0]
  con.close()

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)
  sql = "select value from config where token='solar_meter_port'"
  con.query(sql)
  result = con.use_result()
  value = result.fetch_row()[0]
  port = value[0]
  con.close()

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)
  sql = "select value from config where token='solar_meter_serial_number'"
  con.query(sql)
  result = con.use_result()
  value = result.fetch_row()[0]
  wifi_serial = int(value[0])
  con.close()

  # Connect the socket to the port where the server is listening
  server_address = ((ip, port))

  for res in socket.getaddrinfo(ip, port, socket.AF_INET , socket.SOCK_STREAM):
    af, socktype, proto, canonname, sa = res
    try:
        # print >>sys.stderr, 'connecting to %s port %s' % server_address
        s = socket.socket(af, socktype, proto)
        s.settimeout(10)
    except socket.error as msg:
        s = None
        continue
    try:
        s.connect(sa)
    except socket.error as msg:
        s.close()
        s = None
        continue
    break

  if s is None:
    print 'could not open socket'
    syslog.syslog('Could not open socket')
    sys.exit(1)
    
  s.sendall(InverterMsg.generate_string(wifi_serial))
  data = ''
  while 1:
    data += s.recv(1024)
    if len(data) >= 198: break
  s.close()

  msg = InverterMsg.InverterMsg(data) 

  con = _mysql.connect(dbhost, dbname, dbuser, dbpass)

  if float(msg.getPAC(1))>0:

    sql = "insert into solar( timestamp,temp,vdc1,vdc2,idc1,idc2,iac,vac,fac,pac,etoday,etotal) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3},{4},{5},{6},{7},{8},{9},{10},{11})".format( strftime("%d-%m-%Y %H:%M:00", time.localtime()), msg.getTemp(), msg.getVPV(1), msg.getVPV(2),msg.getIPV(1),msg.getIPV(2), msg.getIAC(), msg.getVAC(1), msg.getFAC(1), msg.getPAC(1), msg.getEToday(), msg.getETotal())

    con.query(sql)
    con.close()

#
# ---------------------
# THE END
# ---------------------
#
