# PriceNotifier

Laravel web application that allows users to track prices and stock on websites.

## Installation Steps for Ubuntu 20.04
#### 1. Install required apt packages (Apache2, PHP 8.0, Mysql PHP Driver, NPM):

```
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php

sudo apt-get update
sudo apt install apache2 php8.3 libapache2-mod-php8.3 php8.3-bcmath php8.3-mbstring php8.3-xml php8.3-zip php-curl php8.3-mysql npm
```


#### 2. Install composer:
```
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/bin --filename=composer
```


#### 3. Install web application:
```
cd ~/
git clone https://github.com/cormierm/price-notifier
cd price-notifier
cp .env.example .env
composer install
npm install
npm run build
cd ~/
cp ~/price-notifier /var/www
sudo chgrp -R www-data /var/www/price-notifier
sudo chmod -R 775 /var/www/price-notifier/storage
```

#### 4. Configure .env
Requires configuration for database, pusher and email credentials.
```
cp /var/www/price-notifier/.env.example /var/www/price-notifier/.env
vi /var/www/price-notifier/.env
```

#### 5. Setup crontab for laravel scheduler:
```
sudo crontab -e
* * * * * cd /var/www/price-notifier && php artisan schedule:run >> /dev/null 2>&1
```

#### 6. Install puppeteer (Required for browsershot client):
```
curl -sL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev libxshmfence-dev
sudo npm install --global --unsafe-perm puppeteer
sudo apt-get install chromium-browser
```

#### 7. Configure Apache2 and permissions:
```
sudo vim /etc/apache2/sites-available/laravel.conf
sudo a2dissite 000-default.conf
sudo a2ensite laravel.conf
sudo a2enmod rewrite
sudo service apache2 restart
```
laravel.conf:
```
<VirtualHost *:80>
    ServerName price.itup.ca

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/price-notifier/public

    <Directory /var/www/price-notifier>
        AllowOverride All
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
RewriteEngine on
RewriteCond %{SERVER_NAME} =price.itup.ca
RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
</VirtualHost>
```

#### 8. Create SSL Certification using Certbot (Optional)
```
sudo snap install --classic certbot
sudo certbot --apache
```

