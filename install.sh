#!/bin/bash

userInputs(){

    echo -e "****** Welecome to installation of the Cyber Panel ******"
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
    version=$(sudo curl -Ls "https://api.github.com/repos/mahmoud-ap/cyber-panel/releases/latest" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
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
        apt-get install npm -y
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

    folder_path="/var/www/html/panel"

    if [ ! -d "$folder_path" ]; then
        mkdir -p "$folder_path"
    else
        rm -rf /var/www/html/panel
    fi

   link=https://github.com/mahmoud-ap/cyber-panel/raw/master/AdminPanel/1.zip

    if [[ -n "$link" ]]; then
        rm -fr /var/www/html/update.zip
        wait
        sudo wget -O /var/www/html/update.zip $link
        wait
        sudo unzip -o /var/www/html/update.zip -d /var/www/html/panel &
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
    sudo chown -R www-data:www-data /var/www/html/panel
    wait
    chown www-data:www-data /var/www/html/panel/index.php
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

configAppche(){
    serverPort=${panelPort##*=}
    ##Remove the "" marks from the variable as they will not be needed
    serverPort=${panelPort//'"'}
    echo "<VirtualHost *:80>
            ServerAdmin webmaster@localhost
            DocumentRoot /var/www/html/example
            ErrorLog /error.log
            CustomLog /access.log combined
            <Directory '/var/www/html/example'>
            AllowOverride All
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
    echo '#CyberPanel' > /var/www/cyberpanelport
    sudo sed -i -e '$a\'$'\n''cyberpanelport '$serverPort /var/www/cyberpanelport
    wait
    ##Restart the apache server to use new port
    sudo /etc/init.d/apache2 reload
    sudo service apache2 restart
    chown www-data:www-data /var/www/html/panel/* &
    wait
    systemctl restart mariadb &
    wait
    systemctl enable mariadb &
    wait
    sudo phpenmod curl
    PHP_INI=$(php -i | grep /.+/php.ini -oE)
    sed -i 's/extension=intl/;extension=intl/' ${PHP_INI}
    wait

    systemctl restart httpd
    systemctl enable httpd
    systemctl enable stunnel4
    systemctl restart stunnel4wait
    systemctl restart sshd
    sudo timedatectl set-timezone Asia/Tehran
    sudo systemctl restart apache2
}

installNethogs(){
    bash <(curl -Ls $nethogsLink --ipv4)
}

configDatabase(){
    dbName="CyberPanel"
    dbPrefix="cp_"
    appVersion=$(getAppVersion)
    mysql -e "create database CyberPanel;" &
    wait
    mysql -e "CREATE USER '${username}'@'localhost' IDENTIFIED BY '${password}';" &
    wait
    mysql -e "GRANT ALL ON *.* TO '${username}'@'localhost';" &
    wait
    mysql -u ${username} --password=${password} ${dbName} < /var/www/html/panel/assets/backup/db.sql
    sed -i "s/DB_DATABASE=cyber_panel/DB_DATABASE=${dbName}/" /var/www/html/panel/.env
    sed -i "s/DB_USERNAME=root/DB_USERNAME=$username/" /var/www/html/panel/.env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=$password/" /var/www/html/panel/.env
    sed -i "s/PORT_SSH=22/PORT_SSH=$sshPort/" /var/www/html/panel/.env
    sed -i "s/PORT_UDP=7302/PORT_UDP=$udpPort/" /var/www/html/panel/.env

    #Insert or update
    if [ -n "$username" -a "$username" != "NULL" ]
        then 
            hashedPassword=$(php -r "echo password_hash('$password', PASSWORD_BCRYPT);")
            nowTime=$(php -r "echo time();")
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}admins      SET username = '${username}' where id='1';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}admins      SET password = '${hashedPassword}' where id='1';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${sshPort}' where name='ssh_port';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${udpPort}' where name='udp_port';"
            mysql -e "USE ${dbName}; UPDATE  ${dbPrefix}settings    SET value = '${appVersion}' where name='app_version';"
    else
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}admins  (username, password, fullname, role, credit, is_active, ctime, utime) VALUES ('${username}', '${password}', 'modir', 'admin', '0', '1', '${nowTime}','0');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}setting (name, value) VALUES ('ssh_port','${sshPort}');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}setting (name, value) VALUES ('udp_port','${udpPort}');"
        mysql -e "USE ${dbName}; INSERT INTO ${dbPrefix}setting (name, value) VALUES ('app_version','${appVersion}');"
    fi
}

configCronMaster(){

    crontab -r
    wait
    cronUrl=$(echo "$httpProtcol://${ipv4}:$panelPort/cron/master")
    cat > /var/www/html/kill.sh << ENDOFFILE
            #!/bin/bash
            #By Mahmoud
            i=0
            while [ 1i -lt 20 ]; do
            cmd=(bbh '$cronUrl')
            echo cmd &
            sleep 6
            i=(( i + 1 ))
            done
ENDOFFILE

    wait
    sudo sed -i 's/(bbh/$(curl -v -H "A: B"/' /var/www/html/kill.sh
    wait
    sudo sed -i 's/cmd/$cmd/' /var/www/html/kill.sh
    wait
    sudo sed -i 's/1i/$i/' /var/www/html/kill.sh
    wait
    sudo sed -i 's/((/$((/' /var/www/html/kill.sh
    wait
    chmod +x /var/www/html/kill.sh
    wait
    (crontab -l | grep . ; echo -e "* * * * * /var/www/html/kill.sh") | crontab -
    (crontab -l ; echo "* * * * * wget -q -O /dev/null '$cronUrl' > /dev/null 2>&1") | crontab -
    
}

installationInfo(){
    echo -e "\n **** Cyber Paenl **** \n"
    echo -e "Cyber Panel Link : $httpProtcol://${ipv4}:$panelPort/login"
    echo -e "Username : ${username}"
    echo -e "Password : ${password}"
}


runSystemServices(){
    sudo systemctl restart apache2
    sudo systemctl restart sshd
}

ipv4=$(getServerIpV4)
appVersion=1.0
username="admin"
password="123456"
udpPort=7300
sshPort=22
panelPort=8081
httpProtcol="http"
panelPath=$(getPanelPath)
panelPortPath="/var/www/html/panelport"
nethogsLink=https://raw.githubusercontent.com/mahmoud-ap/nethogs-json/master/install.sh

checkRoot
userInputs
updateShhConfig
installPackages
copyPanelRepo
configAppche
installNethogs
installSshCall
configDatabase
configCronMaster
runSystemServices
installationInfo