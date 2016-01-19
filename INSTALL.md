Installation manual PlaatEnergy
===============================

Login on Raspberry Pi with user pi

### Step 1 - Install following depending thirdparty software packages
sudo apt-get install apache2
sudo apt-get install php5
sudo apt-get install python
sudo apt-get install mysql-server
sudo apt-get install phpmyadmin
sudo apt-get install svn

### Step 2 - Go to http://[raspberry-ip]/phpmyadmin and login
Username = root
Password = <your password>
	 
### Step 3. Create new database with name “plaatenergy”

### Step 4. Create new database user for “plaatenergy” database with following credentials:
Username = plaatenergy
Password = plaatenergy
Host = 127.0.0.1 
Remark: Give plaatenergy user all database rights else it will not work.

### Step 5. Get latest official version of PlaatEnergy from GitHub repository:
cd /var/www/html
svn checkout https://github.com/wplaat/plaatenergy.git/tags/v0.5 plaatenergy
cd /var/www/plaatenergy

### Step 6. Create config.inc with correct database settings
cp config.inc.sample config.inc
vi config.inc
	 
### Step 7. Add the following cron job:
crontab -e
* * * * * php /var/www/html/plaatenergy/cron.php

### Step 8. Go to http://[raspberry-ip]/plaatenergy.
Select setting page and customize plaatenergy to your personal needs!

### Step 9. Installation is now ready
Now every minute the energy, gas, (optional) solar and (optional) weatherstation data is fetch and processed.

If there are any questions please let me known!
	 
wplaat