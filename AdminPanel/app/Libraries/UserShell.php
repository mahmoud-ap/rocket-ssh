<?php

namespace App\Libraries;

if (!defined('PATH')) die();

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UserShell
{
    public static function totalOnlineUsers()
    {
        $online = 0;
        $output = shell_exec("sudo lsof -i :443 -n | grep -v root | grep ESTABLISHED |awk '{print $3}' |sort -u");
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
        $output = shell_exec("sudo lsof -i :443 -n | grep -v root | grep ESTABLISHED");

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
                    $ipText = $userArr[8];

                    $ipParts = explode("https->", $ipText);
                    if (!empty($ipParts[1])) {

                        $userIp = $ipParts[1];
                        $userIp = preg_replace('/:\d+$/', '', $userIp);

                        $userData = [
                            "ip"        => $userIp,
                            "pid"       => $pid
                        ];

                        $onlineUsers[$username][] = $userData;
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
        }
        return $usersList;
    }

    public static function createUser($username, $password)
    {

        // $ptext = "sudo adduser --disabled-password --gecos '' --shell /usr/sbin/nologin $username";
        // $uProcess = new Process([$ptext]);
        // $uProcess->run();
        // if ($uProcess->isSuccessful()) {
        //     Process::input($password."\n".$password."\n")->timeout(120)->run("sudo passwd {$username}");
        // }

    }

    public static function updateUserPassword($username, $password)
    {
        // Process::input($request->password."\n".$request->password."\n")->timeout(120)->run("sudo passwd {$request->username}");
    }

    public static function deleteUser($username, $permanentDel = true)
    {
        self::userKill($username);
        // $userdelProcess = Process::run("sudo userdel -r {$username}");
    }

    public static function userKill($username)
    {
        // Process::run("sudo killall -u {$username}");
        // Process::run("sudo pkill -u {$username}");
        // Process::run("sudo timeout 10 pkill -u {$username}");
        // Process::run("sudo timeout 10 killall -u {$username}");
    }

    public static function activateUser($username, $password)
    {
        self::createUser($username, $password);
    }

    public static function deActivateUser($username)
    {
        self::deleteUser($username);
    }

    public static function disableMultiUser($username)
    {
        self::userKill($username);
    }
}
