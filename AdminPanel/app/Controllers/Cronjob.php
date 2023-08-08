<?php

namespace App\Controllers;

use \App\Libraries\UserShell;
use \App\Models\Settings;
use \App\Models\Users;
use \App\Models\Traffics;

class Cronjob extends BaseController
{

    public function multiUser($request, $response, $args)
    {

        $multiuser      = Settings::getSetting("multiuser");
        $onlineUsers    = UserShell::onlineUsers();

        $uModel         = new Users();

        if (!empty($onlineUsers)) {
            foreach ($onlineUsers as $username => $users) {

                $userInfo =  $uModel->getByUsername($username);
                if ($userInfo) {;
                    $expiryDays   = $userInfo->expiry_days;
                    $limitUsers   = $userInfo->limit_users;

                    //set expiry date
                    if (!$userInfo->start_date) {
                        $startDate  = date("Y/m/d");
                        $endDate    = date('Y/m/d', strtotime($startDate . " + $expiryDays days"));
                        $uModel->updateExpirydates($username, $startDate, $endDate);
                    }

                    if ($multiuser && count($users) > $limitUsers) {
                        UserShell::disableMultiUser($username);
                    }
                }
            }
        }
    }

    public function expireUsers($request, $response, $args)
    {
        $this->syncServUsersWithDB();
        $this->expiryUsersByTraffic();
    }

    public function syncTraffic($request, $response, $args)
    {

        $trafficFilePath = PATH_STORAGE . DS . "traffics.json";
        $tModel          = new Traffics();

        if (file_exists($trafficFilePath)) {
            $fileContent    = file_get_contents($trafficFilePath);
            $fileLines      = explode("\n", $fileContent);

            $trafficItems   = [];
            foreach ($fileLines as $fileLine) {
                // Trim any extra whitespace
                $trimmedLine = trim($fileLine);
                if (!empty($trimmedLine)) {
                    $jsonData = json_decode($trimmedLine, true);
                    if ($jsonData !== null) {
                        $trafficItems = array_merge($trafficItems, $jsonData);
                    }
                }
            }

            //all server users 
            $serverUsers    = UserShell::allUsers();
            $userTraffics   = [];
            foreach ($trafficItems as $tItem) {
                $username = !empty($tItem["name"]) ? $tItem["name"] : "";
                if (in_array($username, $serverUsers)) {
                    $rx = !empty($tItem["RX"]) ? $tItem["RX"] : 0;
                    $tx = !empty($tItem["TX"]) ? $tItem["TX"] : 0;

                    $rx     = round(((round($rx) / 10) / 12) * 100);
                    $tx     = round(((round($tx) / 10) / 12) * 100);
                    $total  = $rx + $tx;

                    $userTraffics[$username] = [
                        "rx"    => $rx,
                        "tx"    => $tx,
                        "total" => $total
                    ];
                }
            }

            //update database
            foreach ($userTraffics as $username => $traffic) {
                $userTraffic = $tModel->getUserTraffic($username);

                $trafficColumn = [
                    "upload"    => $traffic["tx"],
                    "download"  => $traffic["rx"],
                    "total"     => $traffic["total"],
                ];
                if ($userTraffic) {
                    $trafficColumn["upload"]    += $userTraffic->upload;
                    $trafficColumn["download"]  += $userTraffic->download;
                    $trafficColumn["total"]     += $userTraffic->total;
                    $trafficColumn["utime"]     = time();
                } else {
                    $trafficColumn["ctime"]     = time();
                    $trafficColumn["utime"]     = 0;
                }

                Traffics::updateOrCreate(["username" => $username], $trafficColumn);
            }
        }


        UserShell::createTrfficsLogFile($trafficFilePath);
    }

    /** private methods */
    private function syncServUsersWithDB()
    {
        $usersList  = UserShell::allUsers();
        $uModel     = new Users();

        foreach ($usersList as $username) {
            $userInfo =  $uModel->getByUsername($username);
            if (!$userInfo) {
                UserShell::deleteUser($username);
            }
        }
    }

    private function expiryUsersByTraffic()
    {
        $uModel         = new Users();
        $activeUsers    = $uModel->activeUsers();

        if ($activeUsers) {
            foreach ($activeUsers as $user) {
                $username     = $user->username;
                $totalTraffic = $user->traffic;
                $cTraffic     = $user->consumer_traffic;
                $cTraffic     = $cTraffic ? $cTraffic : 0;

                if ($cTraffic >= $totalTraffic) {

                    UserShell::deActivateUser($username);
                    //decative user in model
                }
            }
        }
    }
}
