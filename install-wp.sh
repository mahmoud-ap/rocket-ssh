#!/bin/bash

ivp4=$(curl -s ipv4.icanhazip.com)

# Get user input for database information
read -p "Enter the database username (default: wp_user): " DB_USER
DB_USER=${DB_USER:-wp_user}

read -p "Enter the database password (default: random password): " DB_PASS
DB_PASS=${DB_PASS:-$(openssl rand -base64 12)}

read -p "Enter the database name (default: wp_database): " DB_NAME
DB_NAME=${DB_NAME:-wp_database}

# Specify the WordPress folder location
WP_DIR="/var/www/html/wordpress"
WP_URL="https://wordpress.org/latest.zip"

# Check if the database and user exist and remove them
EXIST_DB=$(mysql -u root -p -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='$DB_NAME';" | grep -v COUNT)
if [ "$EXIST_DB" -eq 1 ]; then
    echo "Removing existing database $DB_NAME..."
    mysql -u root -p -e "DROP DATABASE $DB_NAME;"
fi

EXIST_USER=$(mysql -u root -p -e "SELECT COUNT(*) FROM mysql.user WHERE user = '$DB_USER';" | grep -v COUNT)
if [ "$EXIST_USER" -eq 1 ]; then
    echo "Removing existing user $DB_USER..."
    mysql -u root -p -e "DROP USER '$DB_USER'@'localhost';"
fi

# Create WordPress subfolder
sudo mkdir -p $WP_DIR
sudo chown -R www-data:www-data $WP_DIR
cd $WP_DIR

mv "$WP_DIR/wordpress/*" "$WP_DIR"
rm -r "$WP_DIR/wordpress"

# Download and unzip WordPress
sudo wget $WP_URL
sudo unzip latest.zip
sudo rm latest.zip

# Create MySQL database and user
mysql -u root -p -e "CREATE DATABASE $DB_NAME;"
mysql -u root -p -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"


# Set file permissions
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chown -R www-data:www-data .

# Update Apache configuration to point to the WordPress folder
sudo sed -i "s|DocumentRoot /var/www/html/wp|DocumentRoot $WP_DIR|" /etc/apache2/sites-available/000-default.conf
sudo sed -i "s|<Directory '/var/www/html/wp'>|<Directory '$WP_DIR'>|" /etc/apache2/sites-available/000-default.conf

# Restart Apache
sudo systemctl restart apache2
await

# Display database information to the user
echo "WordPress installation in subfolder is complete!"
echo "Database Name: $DB_NAME"
echo "Database User: $DB_USER"
echo "Database Password: $DB_PASS"
echo "Please finish the setup by visiting: http://${ivp4}"

