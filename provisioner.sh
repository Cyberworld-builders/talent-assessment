#!/bin/bash

# Update
echo "Updating OS"
sudo apt-get update

# Fix for apache invalid mutex directory error
echo "Configuring apache"
sudo a2dismod ssl
source /etc/apache2/envvars
apache2 -V

# Change the apache document root
echo "Changing apache document root"
if grep -q "/var/www/public" /etc/apache2/apache2.conf
then
   echo "Document root already configured"
else
   sed -i -- "s/var\/www\//var\/www\/public/ig" /etc/apache2/apache2.conf
fi

# Add root user to the adm group
echo "Adding root user to the adm group"
sudo usermod -aG adm root

# Create a new directory where we will store our certificates
echo "Configuring apache for SSL"
cd /etc/apache2
if [ -d "ssl" ]; then
   echo "SSL directory already exists"
else
	echo "Creating SSL directory"
	sudo mkdir ssl
fi
cd ssl

# Generate a new Root Certificate Authority
echo "Generating new certificate authority"
if [ -e rootCA.key ]
then
    echo "Certificate authority files already exist"
else
    sudo openssl genrsa -out rootCA.key 2048
	sudo openssl req -x509 -new -nodes -key rootCA.key -sha256 -days 1024 -out rootCA.pem -subj "/C=US/ST=Oklahoma/L=Tulsa/O=THE AOE GROUP, LLC/CN=dev.aoescience.com"
fi

# Generate a new key
echo "Generating new csr key"
if [ -e aoedev.csr ]
then
    echo "Csr key already exists"
else
	sudo openssl req -new -newkey rsa:2048 -sha256 -nodes -keyout aoedev.key -subj "/C=US/ST=Oklahoma/L=Tulsa/O=THE AOE GROUP, LLC/CN=dev.aoescience.com" -out aoedev.csr
fi

# Create ext file that we will need when self-signing the certificate
echo "Creating ext file for signing the certificate"
if [ -e v3.ext ]
then
    echo "Ext file already exists"
else
	echo "authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = DNS:dev.aoescience.com" >> v3.ext
fi

# Self-sign the certificate
echo "Signing the certificate"
if [ -e aoedev.crt ]
then
    echo "Certificate already signed"
else
	sudo openssl x509 -req -in aoedev.csr -CA rootCA.pem -CAkey rootCA.key -CAcreateserial -out aoedev.crt -days 999 -sha256 -extfile v3.ext
fi

# Enable SSL
echo "Enabling SSL"
sudo a2enmod ssl

# Create a symbolic link for our ssl config file in sites-enabled
echo "Creating symbolic links"
if [ -e /etc/apache2/sites-enabled/000-default-ssl.conf ]
then
    echo "Symbolic links already exist"
else
	sudo ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/000-default-ssl.conf
fi

# Edit our SSL configuration
echo "Updating SSL configuration"

# Change SSL document root
if grep -q "/var/www/public" /etc/apache2/sites-enabled/000-default-ssl.conf
then
   echo "SSL document root already configured"
else
   sed -i -- "s/var\/www\/html/var\/www\/public/ig" /etc/apache2/sites-enabled/000-default-ssl.conf
   echo "Changed SSL document root"
fi

# Change SSL certificate file directory
if grep -q "/etc/apache2/ssl/aoedev.crt" /etc/apache2/sites-enabled/000-default-ssl.conf
then
   echo "SSL certificate file directory already configured"
else
   sed -i -- "s/\/etc\/ssl\/certs\/ssl-cert-snakeoil.pem/\/etc\/apache2\/ssl\/aoedev.crt/ig" /etc/apache2/sites-enabled/000-default-ssl.conf
   echo "Changed SSL certificate file directory"
fi

# Change SSL key file directory
if grep -q "/etc/apache2/ssl/aoedev.key" /etc/apache2/sites-enabled/000-default-ssl.conf
then
   echo "SSL key file directory already configured"
else
   sed -i -- "s/\/etc\/ssl\/private\/ssl-cert-snakeoil.key/\/etc\/apache2\/ssl\/aoedev.key/ig" /etc/apache2/sites-enabled/000-default-ssl.conf
   echo "Changed SSL key file directory"
fi

# Restart the server
sudo service apache2 restart

# Setup the laravel project
echo "Setting up the laravel project"
cd /var/www
export COMPOSER_HOME=/var/www
echo "Updating composer"
/usr/local/bin/composer self-update
echo "Installing composer dependencies"
composer install
echo "Installing npm dependencies"
npm install

# Setup the environment
echo "Configuring the environment file"
cp .env.example .env
php artisan key:generate
sudo sed -i -- "s/DB_DATABASE=homestead/DB_DATABASE=scotchbox/ig" .env
sudo sed -i -- "s/DB_USERNAME=homestead/DB_USERNAME=root/ig" .env
sudo sed -i -- "s/DB_PASSWORD=secret/DB_PASSWORD=root/ig" .env

# Migrate and seed the database
echo "Migrating and seeding the database"
php artisan migrate
php artisan db:seed --class='DatabaseSeeder'

# Create a new user for us to use
echo "Creating a test user"
mysql -u root --password=root --execute='use scotchbox; INSERT INTO users (name, username, password, email, created_at) VALUES ("Bob Dylan", "test", "$2y$10$BoMHf6b8b.IeTUxTCUJ6QetENChfAO./gogtIgYr.IEwaz4z5WGvq", "test@test.com", NOW());'
mysql -u root --password=root --execute='use scotchbox; SET FOREIGN_KEY_CHECKS = 0; INSERT INTO role_user (role_id, user_id, created_at) VALUES (1, 1, NOW()); SET FOREIGN_KEY_CHECKS = 1;'

# Remove the default apache htaccess
if [ -e .htaccess ]
then
    echo "Removing default apache htaccess"
	rm .htaccess
fi

# Finished
echo "All done! You can use the username: test (password: test) to log in at the following url: http://192.168.33.10/login"
