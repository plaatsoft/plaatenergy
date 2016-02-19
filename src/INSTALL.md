Installation manual PlaatEnergy
===============================

Login on Raspberry Pi with user pi

### Step 1 - Install following depending thirdparty software packages
sudo apt-get install apache2
sudo apt-get install php5
sudo apt-get install python
sudo apt-get install mysql-server
sudo apt-get install svn

### Step 2 . Create mysql plaatenergy database
mysql -u root -p
CREATE DATABASE plaatenergy;
GRANT ALL ON plaatenergy.* TO plaatenergy@`127.0.0.1` IDENTIFIED BY `plaatenergy`;
FLUSH PRIVILEGES;

### Step 3. Get latest official version of PlaatEnergy from GitHub repository:
cd /var/www/html
svn checkout https://github.com/wplaat/plaatenergy.git/tags/v0.6 plaatenergy
cd /var/www/plaatenergy
chmod a+wrx /var/www/html/plaatenergy/backup

### Step 4. Create config.inc with correct database settings
cp config.inc.sample config.inc
	 
### Step 5. Add the following cron job:
crontab -e
* * * * * php /var/www/html/plaatenergy/cron.php

### Step 6. Go to http://[raspberry-ip]/plaatenergy.
Select setting page and customize plaatenergy to your personal needs!

### Step 7. Installation is now ready
Now every minute the energy, gas, (optional) solar and (optional) 
weatherstation data is fetch and processed.

If there are any questions please let me known!
	 
wplaat
