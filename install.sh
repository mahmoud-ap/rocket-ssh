#!/bin/bash

 
userInputs(){

    echo -e "\n\n****** Welecome to installation of the Rocket SSH Panel ****** \n"
    printf "Default username is \e[33m${username}\e[0m, let it blank to use this username: "
    read usernameTmp

    if [[ -n "${usernameTmp}" ]]; then
     username=${usernameTmp}
    fi

    echo -e "\nPlease input Panel admin password."
    printf "Default password is \e[33m${password}\e[0m, let it blank to use this password: "
    read passwordTmp

    if [[ -n "${passwordTmp}" ]]; then
     password=${passwordTmp}
    fi

    echo -e "\nPlease input UDPGW Port ."
    printf "Default Port is \e[33m${udpPort}\e[0m, let it blank to use this Port: "
    read udpPortTmp

    if [[ -n "${udpPortTmp}" ]]; then
     udpPort=${udpPortTmp}
    fi

    echo -e "\nPlease input SSH Port ."
    printf "Default Port is \e[33m${sshPort}\e[0m, let it blank to use this Port: "
    read sshPortTmp

    if [[ -n "${sshPortTmp}" ]]; then
     sshPort=${sshPortTmp}
    fi

    echo -e "\nPlease input Panel Port ."
    printf "Default Port is \e[33m${panelPort}\e[0m, let it blank to use this Port: "
    read panelPortTmp

    if [[ -n "${panelPortTmp}" ]]; then
     panelPort=${panelPortTmp}
    fi
}

getAppVersion(){
    version=$(sudo curl -Ls "https://api.github.com/repos/mahmoud-ap/rocket-ssh/releases/latest" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
    echo $version;
}

encryptAdminPass(){
   tempPass=$(php -r "echo password_hash('$password', PASSWORD_BCRYPT);");
   echo $tempPass
}

getServerIpV4(){
    ivp4Temp=$(curl -s ipv4.icanhazip.com)
    echo $ivp4Temp
}

getPanelPath(){
    panelPathTmp="/var/www/html/panel"
    if [ -d "$panelPathTmp" ]; then
        rm -rf $panelPathTmp
    fi

    echo $panelPathTmp
}

getSshPort(){
    sed -i 's/#Port 22/Port 22/' /etc/ssh/sshd_config
    po=$(cat /etc/ssh/sshd_config | grep "^Port")
    port=$(echo "$po" | sed "s/Port //g")
    if [ -z "$port" ]; then
        port="22"  # Set default port to 22 if $port is empty
    fi

    echo "$port"
}

getPanelPort(){
    env_file="/var/www/html/panel/.env"
    local port_panel_value=$(grep "^PORT_PANEL=" "$env_file" | cut -d '=' -f 2-)

    if [ -n "$port_panel_value" ]; then
        echo "$port_panel_value"
    else
        echo "8081"  # Default value if PORT_PANEL is not found
    fi

}

checkRoot() {
    if [ "$EUID" -ne 0 ]; then
        echo "Please run as root"
        exit 1
    fi
}

updateShhConfig(){
    sed -i "s/^(\s*#?\s*Port\s+)[0-9]+/Port ${sshPort}/" /etc/ssh/sshd_config
    sed -E -i "s/^(\s*#?\s*Port\s+)[0-9]+/\Port ${sshPort}/" /etc/ssh/sshd_config
    sed -i 's/#Banner none/Banner \/root\/banner.txt/g' /etc/ssh/sshd_config
    sed -i 's/AcceptEnv/#AcceptEnv/g' /etc/ssh/sshd_config  
}

installPackages(){
    apt update -y
    phpv=$(php -v)
    if [[ $phpv == *"7.4"* ]]; then
        apt autoremove -y
        echo "PHP Is Installed :)"
    else
        sudo NEETRESTART_MODE=a apt-get update --yes
        sudo apt-get -y install software-properties-common
        apt-get install -y cmake && apt-get install -y screenfetch && apt-get install -y openssl
        sudo add-apt-repository ppa:ondrej/php -y
        apt-get install apache2 zip unzip net-tools curl mariadb-server -y
        apt-get install php php-cli php-mbstring php-dom php-pdo php-mysql -y
        sudo apt-get install coreutils
        apt install php7.4 php7.4-mysql php7.4-xml php7.4-curl cron -y
    fi
    echo "/bin/false" >> /etc/shells
    echo "/usr/sbin/nologin" >> /etc/shells 
}

installSshCall(){
    file=/etc/systemd/system/videocall.service
    if [ -e "$file" ]; then
        echo "SSH call is installed"
    else
      apt update -y
    apt install git cmake -y
    git clone https://github.com/ambrop72/badvpn.git /root/badvpn
    mkdir /root/badvpn/badvpn-build
    cd  /root/badvpn/badvpn-build
    cmake .. -DBUILD_NOTHING_BY_DEFAULT=1 -DBUILD_UDPGW=1 &
    wait
    make &
    wait
    cp udpgw/badvpn-udpgw /usr/local/bin
    cat >  /etc/systemd/system/videocall.service << ENDOFFILE
    [Unit]
    Description=UDP forwarding for badvpn-tun2socks
    After=nss-lookup.target

    [Service]
    ExecStart=/usr/local/bin/badvpn-udpgw --loglevel none --listen-addr 127.0.0.1:$udpPort --max-clients 999
    User=videocall

    [Install]
    WantedBy=multi-user.target
ENDOFFILE
    useradd -m videocall
    systemctl enable videocall
    systemctl start videocall
    fi

}

copyPanelRepo(){

    panelFolderPath="/var/www/html/panel"
    accountFolderPath="/var/www/html/account"

    if [ ! -d "$panelFolderPath" ]; then
        mkdir -p "$panelFolderPath"
    else
        rm -rf /var/www/html/panel
    fi

    if [ ! -d "$accountFolderPath" ]; then
        mkdir -p "$accountFolderPath"
    else
        rm -rf /var/www/html/account
    fi

   link=https://github.com/mahmoud-ap/rocket-ssh/raw/master/app.zip

    if [[ -n "$link" ]]; then
        rm -fr /var/www/html/update.zip
        wait
        sudo wget -O /var/www/html/update.zip $link
        wait
        sudo unzip -o /var/www/html/update.zip -d /var/www/html &
    else
        echo "Error extracting the ZIP file link."
        exit 1
    fi

    touch /var/www/html/panel/banner.txt
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/sbin/adduser' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/sbin/userdel' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/sed' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/passwd' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/curl' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/kill' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/killall' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/lsof' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/sbin/lsof' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/sed' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/rm' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/crontab' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/mysqldump' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/pgrep' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/sbin/nethogs' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/nethogs' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/local/sbin/nethogs' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/netstat' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/systemctl restart sshd' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/systemctl reboot' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/systemctl daemon-reload' | sudo EDITOR='tee -a' visudo &
    wait
    echo 'www-data ALL=(ALL:ALL) NOPASSWD:/usr/bin/systemctl restart videocall' | sudo EDITOR='tee -a' visudo &
    wait
    sudo chown -R www-data:www-data /var/www/html/panel
    wait
    chown www-data:www-data /var/www/html/panel/index.php
    wait
    sudo chown -R www-data:www-data /var/www/html/account
    wait
    chown www-data:www-data /var/www/html/account/index.php
    wait

    sudo a2enmod rewrite
    wait
    sudo service apache2 restart
    wait
    sudo systemctl restart apache2
    wait
    sudo service apache2 restart
    wait
    sudo sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf &
    wait
}

configAppache(){
    serverPort=${panelPort##*=}
    ##Remove the "" marks from the variable as they will not be needed
    serverPort=${panelPort//'"'}
     echo "<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        <Directory '/var/www/html'>
            AllowOverride All
        </Directory>
        <Directory '/var/www/html/panel'>
            Require all denied
        </Directory>
    </VirtualHost>
    <VirtualHost *:$panelPort>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/html/panel

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        <Directory '/var/www/html/panel'>
            AllowOverride All
        </Directory>
    </VirtualHost>
    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet" > /etc/apache2/sites-available/000-default.conf
    wait
    echo "sites-available"
    
    ##Replace 'Virtual Hosts' and 'List' entries with the new port number
    sudo  sed -i.bak 's/.*NameVirtualHost.*/NameVirtualHost *:'$serverPort'/' /etc/apache2/ports.conf
    echo "Listen 80
    Listen $serverPort
    <IfModule ssl_module>
        Listen 443
    </IfModule>
    <IfModule mod_gnutls.c>
        Listen 443
    </IfModule>" > /etc/apache2/ports.conf
    echo '#RocketSSH' > /var/www/rocketsshport
    sudo sed -i -e '$a\'$'\n''rocketsshport '$serverPort /var/www/rocketsshport
    wait
    
    ##Replace 'Virtual Hosts' and 'List' entries with the new port number
    sudo  sed -i.bak 's/.*NameVirtualHost.*/NameVirtualHost *:'$serverPort'/' /etc/apache2/ports.conf
    echo "Listen 80
    Listen $serverPort
    <IfModule ssl_module>
        Listen 443
    </IfModule>
    <IfModule mod_gnutls.c>
        Listen 443
    </IfModule>" > /etc/apache2/ports.conf
    echo '#RocketSSH' > /var/www/rocketsshport
    sudo sed -i -e '$a\'$'\n''rocketsshport '$serverPort /var/www/rocketsshport
    wait
    ##Restart the apache server to use new port
    sudo /etc/init.d/apache2 reload
    sudo service apache2 restart
    chown www-data:www-data /var/www/html/panel/* &
    chown www-data:www-data /var/www/html/account/* &
    wait
    systemctl restart mariadb &
    wait
    systemctl enable mariadb &
    wait
    sudo phpenmod curl
    systemctl restart httpd
    systemctl enable httpd
    systemctl restart sshd
    sudo timedatectl set-timezone Asia/Tehran
    sudo systemctl restart apache2
}

installNethogs(){
    bash <(curl -Ls $nethogsLink --ipv4)
}

configDatabase(){
    dbName="RocketSSH"
    dbPrefix="cp_"
    appVersion=$(getAppVersion)
    mysql -e "create database $dbName;" &
    wait
    mysql -e "CREATE USER '${username}'@'localhost' IDENTIFIED BY '${password}';" &
    wait
    mysql -e "GRANT ALL ON *.* TO '${username}'@'localhost';" &
    wait

    # Dump and remove the old database
    if mysql -u root -e "USE RokcetSSH" 2>/dev/null; then
        # Dump and restore the old database to the new database
        mysqldump -u root --force RokcetSSH | mysql -u root $dbName
        echo "Data has been dumped from 'RokcetSSH' to '$dbName'."

        # Remove the old database
        mysql -u root -e "DROP DATABASE RokcetSSH;"
        echo "Old database 'RokcetSSH' has been removed."
    else
        echo "Database 'RokcetSSH' does not exist."
    fi

    sed -i "s/DB_DATABASE=rocket_ssh/DB_DATABASE=${dbName}/" /var/www/html/panel/.env
    sed -i "s/DB_USERNAME=root/DB_USERNAME=$username/" /var/www/html/panel/.env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=$password/" /var/www/html/panel/.env
    sed -i "s/PORT_SSH=22/PORT_SSH=$sshPort/" /var/www/html/panel/.env
    sed -i "s/PORT_UDP=7302/PORT_UDP=$udpPort/" /var/www/html/panel/.env
    sed -i "s/PORT_PANEL=8081/PORT_PANEL=$panelPort/" /var/www/html/panel/.env

    hashedPassword=$(php -r "echo password_hash('$password', PASSWORD_BCRYPT);")
    nowTime=$(php -r "echo time();")
    #Insert or update

    adminTblName=${dbPrefix}admins
    mysqlCmd="mysql -u'$username' -p'$password' -e 'USE $dbName; SHOW TABLES LIKE \"$adminTblName\";'"

    if eval "$mysqlCmd" | grep -q "$adminTblName"; then 
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}admins      SET username = '${username}' where id='1';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}admins      SET password = '${hashedPassword}' where id='1';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${sshPort}' where name='ssh_port';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${udpPort}' where name='udp_port';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${appVersion}' where name='app_version';"
    else
        mysql -u ${username} --password=${password} ${dbName} < /var/www/html/panel/assets/backup/db.sql
        wait
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}admins  (username, password, fullname, role, credit, is_active, ctime, utime) VALUES ('${username}', '${hashedPassword}', 'modir', 'admin', '0', '1', '${nowTime}','0');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}settings (name, value) VALUES ('ssh_port','${sshPort}');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}settings (name, value) VALUES ('udp_port','${udpPort}');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}settings (name, value) VALUES ('app_version','${appVersion}');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}settings (name, value) VALUES ('calc_traffic','1');"
    fi
}

configCronMaster(){

    crontab -r
    wait

    cronUrl="$httpProtcol://$ipv4:$panelPort/cron/master"

    # Define the file path to check
    killFilePath="/var/www/html/kill.sh"

    # Check if the file exists
    if [ -e "$killFilePath" ]; then
        # Remove the file
        pkill -f kill.sh
        rm "$killFilePath"
    else
        echo "File $killFilePath does not exist."
    fi

    rm /tmp/call_url.lock

    cat > /var/www/html/cronjob.sh << ENDOFFILE
    #!/bin/bash

    curlUrl="tmpCurl"
    lockfile="/tmp/call_url.lock"

    # Check if the lock file exists
    if [ -e "\$lockfile" ]; then
        echo "Previous instance still running. Exiting."
        exit 1
    fi

    # Create the lock file
    touch "\$lockfile"

    # Function to remove the lock file
    cleanup() {
        rm -f "\$lockfile"
        exit
    }
    trap cleanup EXIT

    while true; do
        # Use curl to call the URL
        curl -s -o -v -H /dev/null \$curlUrl &
        sleep 5
    done
ENDOFFILE
    wait
    chmod +x /var/www/html/cronjob.sh
    wait
    sed -i "s|curlUrl=\"tmpCurl\"|curlUrl=\"$cronUrl\"|" /var/www/html/cronjob.sh
    wait
    (crontab -l | grep . ; echo -e "* * * * * /var/www/html/cronjob.sh") | crontab -
}

installationInfo(){
    clear
    echo -e "\n"
    bannerText=$(curl -s https://raw.githubusercontent.com/mahmoud-ap/rocket-ssh/master/rocket-banner.txt)
    printf "%s" "$bannerText"
    echo -e "\n"
    printf "Panel Link : $httpProtcol://${ipv4}:$panelPort/login"
    printf "\nUsername : \e[31m${username}\e[0m "
    printf "\nPassword : \e[31m${password}\e[0m "
    printf "\nSSH Port : \e[31m${sshPort}\e[0m "
    printf "\nUDP Port : \e[31m${udpPort}\e[0m \n\n"
}

runSystemServices(){
    sudo systemctl restart apache2
    sudo systemctl restart sshd
}

runMigrataion(){
    migrateUrl=$(echo "$httpProtcol://$ipv4:$panelPort/migrate")
    curl -s $migrateUrl
    rm /var/www/html/index.html
}

ipv4=$(getServerIpV4)
appVersion=1.2
username="admin"
password="123456"
udpPort=7300
sshPort=$(getSshPort)
panelPort=$(getPanelPort)
httpProtcol="http"
panelPath=$(getPanelPath)
nethogsLink=https://raw.githubusercontent.com/mahmoud-ap/nethogs-json/master/install.sh

checkRoot
userInputs
updateShhConfig
installPackages
copyPanelRepo
configAppache
installNethogs
installSshCall
configDatabase
configCronMaster
runSystemServices
runMigrataion
installationInfo
