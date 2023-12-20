#!/bin/bash



getPanelPort(){
    env_file="/var/www/html/panel/.env"
    local port_panel_value=$(grep "^PORT_PANEL=" "$env_file" | cut -d '=' -f 2-)

    if [ -n "$port_panel_value" ]; then
        echo "$port_panel_value"
    else
        echo "8081"  # Default value if PORT_PANEL is not found
    fi
}

panelPort=$(getPanelPort)
sslPanelPort=$((panelPort+1))

# Check if the script is run as root
if [ "$EUID" -ne 0 ]; then
    echo "Please run as root or with sudo."
    exit 1
fi

# Check for the presence of Certbot
if ! command -v certbot &> /dev/null; then
    echo "Certbot is not installed. Installing..."
    apt update
    apt install -y certbot
fi

# Get subdomain as input
read -p "Enter your subdomain: " SUBDOMAIN

# Stop Apache temporarily
systemctl stop apache2

# Obtain SSL certificate
certbot certonly --standalone -d $SUBDOMAIN

# Configure Apache to use the SSL certificate
CONF_FILE="/etc/apache2/sites-available/default-ssl.conf"
cat <<EOL > $CONF_FILE
<IfModule mod_ssl.c>
	<VirtualHost *:443>
		ServerAdmin RockerSSH@$SUBDOMAIN
		ServerName $SUBDOMAIN

		DocumentRoot /var/www/html

		ErrorLog \${APACHE_LOG_DIR}/error.log
		CustomLog \${APACHE_LOG_DIR}/access.log combined

		SSLEngine on
    	SSLCertificateFile /etc/letsencrypt/live/$SUBDOMAIN/fullchain.pem
    	SSLCertificateKeyFile /etc/letsencrypt/live/$SUBDOMAIN/privkey.pem
    	SSLCertificateChainFile /etc/letsencrypt/live/$SUBDOMAIN/chain.pem

		<FilesMatch '\.(cgi|shtml|phtml|php)$'>
			SSLOptions +StdEnvVars
		</FilesMatch>

		<Directory /usr/lib/cgi-bin>
			SSLOptions +StdEnvVars
		</Directory>

		<Directory "/var/www/html">
			AllowOverride All
		</Directory>
		
        <Directory '/var/www/html/panel'>
            Require all denied
        </Directory>
	</VirtualHost>

	<VirtualHost *:$sslPanelPort>
		ServerAdmin RockerSSH@$SUBDOMAIN
		DocumentRoot /var/www/html/panel

		ServerName $SUBDOMAIN

		ErrorLog \${APACHE_LOG_DIR}/error.log
		CustomLog \${APACHE_LOG_DIR}/access.log combined

		SSLEngine on
    	SSLCertificateFile /etc/letsencrypt/live/$SUBDOMAIN/fullchain.pem
    	SSLCertificateKeyFile /etc/letsencrypt/live/$SUBDOMAIN/privkey.pem
    	SSLCertificateChainFile /etc/letsencrypt/live/$SUBDOMAIN/chain.pem
		
		<FilesMatch '\.(cgi|shtml|phtml|php)$'>
			SSLOptions +StdEnvVars
		</FilesMatch>

		<Directory /usr/lib/cgi-bin>
			SSLOptions +StdEnvVars
		</Directory>
	
		<Directory "/var/www/html/panel">
			AllowOverride All
		</Directory>
	</VirtualHost>
</IfModule>
EOL

cat <<EOL > /etc/apache2/ports.conf
Listen 80
Listen $panelPort
<IfModule ssl_module>
	Listen $sslPanelPort
	Listen 443
</IfModule>
<IfModule mod_gnutls.c>
	Listen $sslPanelPort
	Listen 443
</IfModule>
EOL

# Enable SSL module and the virtual host
a2enmod ssl
sudo a2ensite default-ssl
# Restart Apache
systemctl restart apache2

echo "SSL certificate for $SUBDOMAIN has been configured successfully."
