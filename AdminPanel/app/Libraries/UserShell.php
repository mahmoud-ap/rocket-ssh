<?php

/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

namespace App\Libraries;

if (!defined('PATH')) die();


class UserShell
{
    public static function totalOnlineUsers()
    {
        $sshPort = getenv("PORT_SSH");
        $online = 0;

        $output = shell_exec("sudo lsof -i :$sshPort -n | grep -v root | grep ESTABLISHED |awk '{print $3}' |sort -u");

        if (!empty($output)) {
            $list = preg_split("/\r\n|\n|\r/", $output);
            $list = array_filter($list);
            $online = count($list);
        }
        return   $online;
    }

    public static function onlineUsers()
    {
        $onlineUsers = [];
        $sshPort = getenv("PORT_SSH");

        $output  = shell_exec("sudo lsof -i :$sshPort  -n | grep -v root | grep ESTABLISHED");

        $invalidUses = ["sshd"];
        if (!empty($output)) {
            $usersList = preg_split("/\r\n|\n|\r/", $output);

            foreach ($usersList as $user) {
                $user = preg_replace("/\\s+/", " ", $user);
                $userArr = [];
                if (strpos($user, ":AAAA") !== false) {
                    $userArr = explode(":", $user);
                } else {
                    $userArr = explode(" ", $user);
                }
                if (count($userArr) == 10) {
                    $pid = $userArr[1];
                    $username = $userArr[2];
                    if (!in_array($username, $invalidUses)) {
                        $ipText = $userArr[8];
                        $ipParts = explode(":", $ipText);
                        if (!empty($ipParts[1])) {
                            $userIp     = $ipParts[1];
                            $pattern = '/((http|https|ssh|' . $sshPort . ')->(\d+\.\d+\.\d+\.\d+))/';
                            if (preg_match($pattern, $userIp, $matches)) {
                                $userIp     = preg_replace("/(http|https|ssh|$sshPort)->/", "", $matches[0]);
                                $userData = [
                                    "ip"        => $userIp,
                                    "pid"       => $pid
                                ];

                                $onlineUsers[$username][] = $userData;
                            }
                        }
                    }
                }
            }
        }

        return $onlineUsers;
    }

    public static function ramData()
    {
        $output = shell_exec("free | grep Mem");

        $result = [
            "total" => 0,
            "free" => 0,
            "available" => 0,
        ];

        if (!empty($output)) {
            $parts = preg_split('/\s+/', $output);

            if (!empty($parts) && count($parts) == 8) {
                $total      = intval($parts[1]) * 1024;
                $used       = intval($parts[2]) * 1024;
                $available  = intval($parts[6]) * 1024;

                $usagePercent = round(($used / $total) * 100);

                $result["total"]            = convertToPrettyUnit($total);
                $result["used"]             = convertToPrettyUnit($used);
                $result["available"]        = convertToPrettyUnit($available);
                $result["usage_percent"]    = $usagePercent;
                $result["usage_color"]      = getUsageColor($usagePercent);
                $result["usage_text_color"] = getContrastTextColor($result["usage_color"]);
            }
        }

        return $result;
    }

    public static function cpuData()
    {

        $result = [];
        $totalCores = self::cpuCores();

        $loadAvg    = sys_getloadavg();
        $cpuLoadAvg = round($loadAvg[1] / ($totalCores + 1) * 100, 0);


        $result["totalCores"]       = $totalCores;
        $result["loadAvg"]          = $cpuLoadAvg;
        $result["name"]             = self::cpuName();
        $result["usage_color"]      = getUsageColor($cpuLoadAvg);
        $result["usage_text_color"] = getContrastTextColor($result["usage_color"]);

        return $result;
    }

    public static function cpuName()
    {
        $cpuName = shell_exec('grep "model name" /proc/cpuinfo | uniq');
        $cpuName = trim(str_replace("model name\t: ", "", $cpuName));
        return $cpuName;
    }

    public static function cpuCores()
    {
        $cpuCores = shell_exec('grep -P "^processor" /proc/cpuinfo | wc -l');
        return intval($cpuCores);
    }

    public static function diskData()
    {
        $freeSpace  = disk_free_space('/');
        $totalSpace = disk_total_space('/');

        $usagePercent = round((1 - $freeSpace / $totalSpace) * 100);

        $color          = getUsageColor($usagePercent);
        $textColor      = getContrastTextColor($color);

        $result = [
            "free"              => convertToPrettyUnit($freeSpace),
            "total"             => convertToPrettyUnit($totalSpace),
            "usage_percent"     => $usagePercent,
            "usage_color"       => $color,
            "usage_text_color"  => $textColor
        ];

        return $result;
    }

    public static function serverTraffic()
    {
        $download   = self::traffixRx();
        $upload     = self::traffixTx();

        return [
            "download"  => convertToPrettyUnit($download),
            "upload"    => convertToPrettyUnit($upload),
            "total"     => convertToPrettyUnit($download + $upload),
        ];
    }

    public static function traffixRx($convert = false)
    {
        $download = 0;
        $output = shell_exec("netstat -e -n -i |  grep \"RX packets\" | grep -v \"RX packets 0\" | grep -v \" B)\"");

        if (!empty($output)) {
            $output = preg_split("/\r\n|\n|\r/", $output);
            foreach ($output as $parts) {
                $partsArr = explode(" ", $parts);
                if (!isset($parts[13])) {
                    $partsArr[13] = null;
                }
                if (is_numeric($partsArr[13])) {
                    $download += $partsArr[13];
                }
            }
        }
        if ($convert) {
            return convertToPrettyUnit($download);
        }
        return ($download);
    }

    public static function traffixTx($convert = false)
    {
        $upload = 0;
        $output = shell_exec("netstat -e -n -i |  grep \"TX packets\" | grep -v \"TX packets 0\" | grep -v \" B)\"");
        if (!empty($output)) {
            $output = preg_split("/\r\n|\n|\r/", $output);
            foreach ($output as $parts) {
                $partsArr = explode(" ", $parts);
                if (!isset($parts[13])) {
                    $partsArr[13] = null;
                }
                if (is_numeric($partsArr[13])) {
                    $upload += $partsArr[13];
                }
            }
        }
        if ($convert) {
            return convertToPrettyUnit($upload);
        }
        return ($upload);
    }

    public static function killUsers($pids = [])
    {
        foreach ($pids as $pid) {
            self::killUser($pid);
        }
    }

    public static function killUser($pid)
    {
        shell_exec("sudo kill -9 {$pid}");
    }

    public static function createMysqlBackup($filePath = "")
    {

        $dbUsername = getenv("DB_USERNAME");
        $dbPassword = getenv("DB_PASSWORD");
        $dbName     = getenv("DB_DATABASE");

        shell_exec("mysqldump -u '$dbUsername' --password='$dbPassword' $dbName > $filePath");
    }

    public static function restoreMysqlBackup($filePath = "")
    {

        $dbUsername = getenv("DB_USERNAME");
        $dbPassword = getenv("DB_PASSWORD");
        $dbName     = getenv("DB_DATABASE");

        shell_exec("mysql -u '$dbUsername' --password='$dbPassword' $dbName < $filePath");
    }

    public static function createTrfficsLogFile($filePath)
    {
        shell_exec("sudo rm -rf $filePath");
        shell_exec("sudo nethogs -j -v3 -c6 > $filePath");
        shell_exec("sudo pkill nethogs");
    }

    public static function allUsers()
    {
        $usersList  = [];
        $output     = shell_exec("ls /home");
        if (!empty($output)) {
            $usersList = preg_split("/\r\n|\n|\r/", $output);
            $usersList = array_filter($usersList);
        }

        return $usersList;
    }

    public static function createUser($username, $password)
    {
        $addUserCommand = "sudo adduser $username --shell /usr/sbin/nologin &";
        $setPasswordCommand = "sudo passwd $username <<!\n$password\n$password\n!";
        $fullCommand = "$addUserCommand\nwait\n$setPasswordCommand";
        $addUserOutput = shell_exec($fullCommand);
    }

    public static function updateUserPassword($username, $password)
    {
        $setPasswordCommand = "sudo passwd $username <<!\n$password\n$password\n!";
        shell_exec($setPasswordCommand);
        self::userKill($username);
    }

    public static function deleteUser($username, $permanentDel = true)
    {
        self::userKill($username);
        shell_exec("sudo userdel -r {$username}");
    }

    public static function userKill($username)
    {
        shell_exec("sudo killall -u {$username}");
        shell_exec("sudo pkill -u {$username}");
        shell_exec("sudo timeout 10 pkill -u {$username}");
        shell_exec("sudo timeout 10 killall -u {$username}");
    }

    public static function activateUser($username, $password)
    {
        self::createUser($username, $password);
    }

    public static function deactivateUser($username)
    {
        self::deleteUser($username);
    }

    public static function disableMultiUser($username)
    {
        self::userKill($username);
    }

    public static function updateSshPort($newPort)
    {
        $command = "sudo sed -i 's/Port [0-9]*/Port $newPort/' /etc/ssh/sshd_config";
        shell_exec($command);

        $command = "sudo sed -i 's/PORT_SSH=[0-9]*/PORT_SSH=$newPort/' /var/www/html/panel/.env";
        shell_exec($command);
    }


    public static function updateUdpPort($newPort)
    {
        $command = "sudo sed -E -i 's/(127.0.0.1:[0-9]+)/127.0.0.1:$newPort/' /etc/systemd/system/videocall.service";
        shell_exec($command);
        shell_exec("sudo systemctl restart videocall");

        $command = "sudo sed -i 's/PORT_UDP=[0-9]*/PORT_UDP=$newPort/' /var/www/html/panel/.env";
        shell_exec($command);
    }


    public static function updateConnectedText($text)
    {
        $filePath = PATH  . DS . "banner.txt";
        $command = "echo $text > $filePath";
        shell_exec($command);

        $filePath = preg_quote($filePath, '/');
        $command = "sudo sed -i 's/^\(Banner\s*\).*$/\\1$filePath/' /etc/ssh/sshd_config";
        shell_exec($command);
    }
}
