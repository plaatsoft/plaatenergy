import InverterMsg 
import sys, os
import socket               
import ConfigParser
import time 
from time import strftime
import _mysql
import syslog

ip           = "192.168.0.201"
port         = 8899 
wifi_serial  = 1606789503

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

try:

    con = _mysql.connect('localhost', 'power', 'power', 'power')

    if float(msg.getPAC(1))>0:

      sql = "insert into solar( timestamp,temp,vdc1,vdc2,idc1,idc2,iac,vac,fac,pac,etoday,etotal) values (str_to_date('{0}','%d-%m-%Y %H:%i:%s'),{1},{2},{3},{4},{5},{6},{7},{8},{9},{10},{11})".format( strftime("%d-%m-%Y %H:%M:00", time.localtime()), msg.getTemp(), msg.getVPV(1), msg.getVPV(2),msg.getIPV(1),msg.getIPV(2), msg.getIAC(), msg.getVAC(1), msg.getFAC(1), msg.getPAC(1), msg.getEToday(), msg.getETotal())

      con.query(sql)

except _mysql.Error, e:

    print "Error %d: %s" % (e.args[0], e.args[1])
    syslog.syslog('SQL error!')
    sys.exit(1)

finally:

    if con:
        con.close()

