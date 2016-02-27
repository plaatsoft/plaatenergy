Installation manual PlaatEnergy
===============================

Login on Raspberry Pi with user pi

### Step 1 - Install following depending thirdparty software packages
sudo apt-get install apache2
sudo apt-get install php5
sudo apt-get install python
sudo apt-get install mysql-server
sudo apt-get install python-mysqldb

### Step 2 . Create mysql plaatenergy database
mysql -u root -p
CREATE DATABASE plaatenergy;
GRANT ALL ON plaatenergy.* TO plaatenergy@`127.0.0.1` IDENTIFIED BY `plaatenergy`;
FLUSH PRIVILEGES;
QUIT;

### Step 3. Download PlaatEnergy from plaatsoft.nl.
Copy zip file to /tmp
login on the raspberry with user `pi`
cd /var/www/html
sudo cp /tmp/plaatenergy.zip .
sudo unzip *.zip
sudo chmod a+wrx /var/www/html/plaatenergy/backup

### Step 4. Create config.inc with correct database settings
cp config.inc.sample config.inc
	 
### Step 5. Add the following cron job:
crontab -e
* * * * * php /var/www/html/plaatenergy/cron.php

### Step 6. Go to http://[raspberry-ip]/plaatenergy.
Select setting page and customize plaatenergy to your personal needs!

### Step 7. Installation is now ready
Now every minute the energy, gas, (optional) solar and (optional) 
weather station data is fetch and processed.

If there are any questions please let me known!
	 
wplaat
info@plaatsoft.nl
