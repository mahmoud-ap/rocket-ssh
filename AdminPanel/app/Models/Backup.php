<?php

namespace App\Models;

use Morilog\Jalali\Jalalian;

class Backup extends \App\Models\BaseModel
{

    public function getUserBackups()
    {
        $backupPath = PATH_STORAGE . DS . "backup";
        $sqlFiles = array();

        $folderName = str_replace(PATH, "", $backupPath);


        $files = glob($backupPath . '/*.sql');
        foreach ($files as $file) {
            $fileTime   = filectime($file);
            $jdate      = Jalalian::forge($fileTime)->format('Y/m/d - H:i');
            $filename   =  basename($file);
            $sqlFiles[$fileTime] = [
                "name"  =>  $filename,
                "url"   =>  baseUrl("$folderName/$filename"),
                "date"  =>  $jdate,
            ];
        }
        arsort($sqlFiles);

        return array_values($sqlFiles);
    }

    public function importBackupFromShahan($values)
    {
        $usersValues    = !empty($values["users"])      ? $values["users"] : [];
        $traficValues   = !empty($values["Traffic"])    ? $values["Traffic"] : [];

        $insetUsers     = [];
        $insetTraffics  = [];
        $adminUsername  = getAdminUsername();


        foreach ($usersValues  as $user) {
            if (count($user) == 14) {
                $username   = !empty($user[1]) ? trim($user[1]) : "";
                $password   = !empty($user[2]) ? trim($user[2]) : "";
                $email      = !empty($user[3]) ? $user[3] : "";
                $mobile     = !empty($user[4]) ? $user[4] : "";
                $multiuser  = !empty($user[5]) ? $user[5] : 1;
                $startDate  = !empty($user[6]) ? $user[6] : 0;
                $endDate    = !empty($user[7]) ? $user[7] : 0;
                $enable     = !empty($user[8]) ? $user[8] : 1;
                $traffic    = !empty($user[9]) ? $user[9] : "";
                $info       = !empty($user[11]) ? $user[11] : "";
                $days       = !empty($user[12]) ? $user[12] : "";

                if (!empty($username) && !empty($password)) {

                    $isActive = $enable && $enable == "true" ? 1 : 0;

                    $endDate     = $endDate && strtotime($endDate) ? strtotime($endDate) : 0;
                    $startDate   = $startDate && strtotime($startDate) ? strtotime($startDate) : 0;

                    $insetUsers[$username] = [
                        "username"          => $username,
                        "admin_uname"       => $adminUsername,
                        "password"          => $password,
                        "email"             => $email,
                        "mobile"            => $mobile,
                        "desc"              => $info,
                        "start_date"        => $startDate,
                        "end_date"          => $endDate,
                        "is_active"         => $isActive,
                        "expiry_days"       => convertToEnNum($days),
                        "traffic"           => $traffic ? $traffic * 1024 : $traffic,
                        "limit_users"  => $multiuser,
                        "ctime"             => time(),
                        "utime"             => 0,
                    ];
                }
            }
        }

        foreach ($traficValues as $traffic) {
            if (count($traffic) == 4) {
                $username   = !empty($traffic[0]) ? $traffic[0] : "";
                $download   = !empty($traffic[1]) ? $traffic[1] : 0;
                $upload     = !empty($traffic[2]) ? $traffic[2] : 0;
                $total      = !empty($traffic[3]) ? $traffic[3] : 0;
                if ($username) {
                    $insetTraffics[$username] = [
                        "username"  => $username,
                        "download"  => $download,
                        "upload"    => $upload,
                        "total"     => $total,
                        "ctime"     => time(),
                        "utime"     => 0,
                    ];
                }
            }
        }

        try {
            $totalInsert =  db()::transaction(function () use ($insetUsers, $insetTraffics) {

                $insetUsers = array_values($insetUsers);
                $totalInsert = 0;
                foreach ($insetUsers as $key => $user) {
                    $username =  $user["username"];
                    $checkExistUser =  db("users")->where("username",  $username)->count();
                    if (!$checkExistUser) {
                        db("users")->insert($user);
                        $totalInsert++;

                        $userTraffic = isset($insetTraffics[$username]) ? $insetTraffics[$username] : false;
                        if ($userTraffic) {
                            db("traffics")->insert($userTraffic);
                        } else {
                            db("traffics")->insert([
                                "username"  => $username,
                                "download"  => 0,
                                "upload"    => 0,
                                "total"     => 0,
                                "ctime"     => time(),
                                "utime"     => 0,
                            ]);
                        }
                    }
                }

                return $totalInsert;
            });

            return $totalInsert;
        } catch (\Exception $err) {
            db()::rollback();
            throw "Error";
        }
    }

    public function importBackupFromXpanel($values)
    {
        $usersValues    = !empty($values["users"])      ? $values["users"] : [];
        $traficValues   = !empty($values["traffic"])    ? $values["traffic"] : [];

        $insetUsers     = [];
        $insetTraffics  = [];
        $adminUsername  = getAdminUsername();

        foreach ($usersValues  as $user) {
            if (count($user) == 16) {
                $username       = !empty($user[1]) ? trim($user[1]) : "";
                $password       = !empty($user[2]) ? trim($user[2]) : "";
                $email          = !empty($user[3]) ? $user[3] : "";
                $mobile         = !empty($user[4]) ? $user[4] : "";
                $multiuser      = !empty($user[5]) ? $user[5] : 1;
                $startDate      = !empty($user[6]) ? $user[6] : 0;
                $endDate        = !empty($user[7]) ? $user[7] : 0;
                $days           = !empty($user[8]) ? $user[8] : 0;
                $status         = !empty($user[10]) ? $user[10] : "active";
                $traffic        = !empty($user[11]) ? $user[11] : "";
                $desc           = !empty($user[13]) ? $user[13] : "";

                if ($username && $password) {

                    $isActive = $status == "active"  ? 1 : 0;

                    $endDate     = $endDate && strtotime($endDate) ? strtotime($endDate) : 0;
                    $startDate   = $startDate && strtotime($startDate) ? strtotime($startDate) : 0;

                    $insetUsers[$username] = [
                        "username"          => $username,
                        "admin_uname"       => $adminUsername,
                        "password"          => $password,
                        "email"             => $email != "NULL" ? $email : "",
                        "mobile"            => $mobile != "NULL" ? $mobile : "",
                        "desc"              => $desc != "NULL" ? $desc : "",
                        "start_date"        => $startDate,
                        "end_date"          => $endDate,
                        "is_active"         => $isActive,
                        "expiry_days"       => $days && $days != "NULL" ? convertToEnNum($days) : 0,
                        "traffic"           => $traffic,
                        "limit_users"  => $multiuser,
                        "ctime"             => time(),
                        "utime"             => 0,
                    ];
                }
            }
        }

        foreach ($traficValues as $traffic) {
            if (count($traffic) == 7) {
                $username   = !empty($traffic[0]) ? $traffic[0] : "";
                $download   = !empty($traffic[1]) ? $traffic[1] : 0;
                $upload     = !empty($traffic[2]) ? $traffic[2] : 0;
                $total      = !empty($traffic[3]) ? $traffic[3] : 0;

                if ($username) {
                    $insetTraffics[$username] = [
                        "username"  => $username,
                        "download"  => $download,
                        "upload"    => $upload,
                        "total"     => $total,
                        "ctime"     => time(),
                        "utime"     => 0,
                    ];
                }
            }
        }

        try {
            $totalInsert =  db()::transaction(function () use ($insetUsers, $insetTraffics) {

                $insetUsers = array_values($insetUsers);
                $totalInsert = 0;
                foreach ($insetUsers as $key => $user) {
                    $username =  $user["username"];
                    $checkExistUser =  db("users")->where("username",  $username)->count();
                    if (!$checkExistUser) {
                        db("users")->insert($user);
                        $totalInsert++;

                        $userTraffic = isset($insetTraffics[$username]) ? $insetTraffics[$username] : false;
                        if ($userTraffic) {
                            db("traffics")->insert($userTraffic);
                        } else {
                            db("traffics")->insert([
                                "username"  => $username,
                                "download"  => 0,
                                "upload"    => 0,
                                "total"     => 0,
                                "ctime"     => time(),
                                "utime"     => 0,
                            ]);
                        }
                    }
                }

                return $totalInsert;
            });

            return $totalInsert;
        } catch (\Exception $err) {
            echo $err->getMessage();
            db()::rollback();
            throw "Error";
        }
    }


    public function importSelfBackup($sqlContent)
    {

        $values = parseSQLFileForTables($sqlContent, ["traffics", "users"]);

        $usersValues    = !empty($values["users"])          ? $values["users"] : [];
        $traficValues   = !empty($values["traffics"])       ? $values["traffics"] : [];

        if (!empty($usersValues) && !empty($traficValues)) {
            $backupPath     = PATH_STORAGE . DS . "backup";
            $backupFilePath = $backupPath . DS . "temp.sql";
            //    create temp file
            @file_put_contents($backupFilePath, $sqlContent);

            \App\Libraries\UserShell::restoreMysqlBackup($backupFilePath);
            @unlink($backupFilePath);

            return count($usersValues);
        }

        return 0;
    }
}
