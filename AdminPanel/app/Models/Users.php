<?php

namespace App\Models;

use Morilog\Jalali\Jalalian;
use \App\Libraries\UserShell;

class Users extends \App\Models\BaseModel
{

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'admin_uname',
        'password',
        'email',
        'mobile',
        'desc',
        'limit_users',
        'start_date',
        'end_date',
        'expiry_days',
        'expiry_type',
        'is_active',
        'traffic',
        'ctime',
        'utime',
    ];

    public function saveUsers($pdata, $uid, $editId = null)
    {

        $columnsArr    = [];
        $pdata          = trimArrayValues($pdata);

        $userInfo       = $editId ? $this->getInfo($editId) : null;
        $userStartDate  = $userInfo ? $userInfo->start_date : "";

        $startDate      = 0;
        $endDate        = 0;
        $expiryType     = getArrayValue($pdata, "expiry_type", $userStartDate ? "date" : "days");

        $columnsArr["expiry_days"] = getArrayValue($pdata, "exp_days", 0);

        if ($expiryType == "date") {
            $expDate    = $pdata["exp_date"];
            $expDateST  = strtotime(Jalalian::fromFormat('Y/m/d', $expDate)->toCarbon());
            $startDate  = time();
            $endDate    = strtotime("tomorrow", $expDateST) - 1;
            $columnsArr["expiry_days"]  = floor(($endDate - $startDate) / 86400);
        }

        $columnsArr["password"]         = $pdata["password"];
        $columnsArr["email"]            = getArrayValue($pdata, "email");
        $columnsArr["mobile"]           = getArrayValue($pdata, "mobile");
        $columnsArr["desc"]             = getArrayValue($pdata, "desc");
        $columnsArr["start_date"]       = $startDate;
        $columnsArr["end_date"]         = $endDate;
        $columnsArr["traffic"]          = $pdata["traffic"] * 1024;
        $columnsArr["limit_users"] = $pdata["limit_users"];

        if (!$editId) {
            $columnsArr["username"]         = $pdata["username"];
            $columnsArr["admin_uname"]      = getAdminUsername();
            $columnsArr["ctime"]            = time();
            $columnsArr["utime"]            = 0;
            $columnsArr["is_active"]        = 1;
        } else {
            $columnsArr["utime"]            = time();
        }

        try {
            db()::transaction(function () use ($columnsArr, $editId, $userInfo) {

                $this->updateOrCreate(['id' => $editId], $columnsArr);
                if (!$editId) {
                    $trafficCols["username"]    = $columnsArr["username"];
                    $trafficCols["download"]    = 0;
                    $trafficCols["upload"]      = 0;
                    $trafficCols["total"]       = 0;
                    $trafficCols["ctime"]       = time();
                    $trafficCols["utime"]       = 0;

                    \App\Models\Traffics::insert($trafficCols);
                }

                $username = $columnsArr["username"];
                $password = $columnsArr["password"];

                //server shells
                if (!$userInfo) {
                    UserShell::createUser($username, $password);
                } else {
                    $oldPass  = $userInfo->password;
                    $username = $userInfo->username;
                    $newPasss = $columnsArr["password"];

                    if ($oldPass != $newPasss) {
                        UserShell::updateUserPassword($username, $newPasss);
                    }
                }
            });
        } catch (\Exception $err) {
            db()::rollback();
            echo $err->getMessage();

            throw "Error";
        }
    }

    public function dataTableList($pdata, $uid)
    {
        $select = [
            "users.id",
            "users.start_date",
            "users.admin_uname",
            "users.end_date",
            "users.ctime",
            "users.utime",
            "users.username",
            "users.password",
            "users.mobile",
            "users.limit_users",
            "users.is_active",
            "users.traffic",
            "admins.fullname as admin_name",
            "traffics.total as consumer_traffic"
        ];

        $adminRole = getAdminRole();

        $query = db($this->table)->select($select)
            ->join('admins', 'admins.username', '=', 'users.admin_uname')
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->orderBy("id", "DESC");

        if ($adminRole !== "admin") {
            $query->where("admins.role", $adminRole);
        }

        if (!empty($pdata["search"]["value"])) {
            $search = $pdata["search"]["value"];
            $search = trim($search);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where("users.username", "LIKE", "%$search%")
                        ->orWhere("users.mobile",   $search)
                        ->orWhere("users.email",    "%$search%")
                        ->orWhere("users.desc",     "%$search%")
                        ->orWhere("admins.fullname",  "LIKE", "%$search%");
                });
            }
        }
        $pdata["search"]["value"] = "";

        $DataTable      = new \App\Libraries\DataTable($query, $pdata);
        $users          = $DataTable->query()->toArray();

        $resUsers = array();
        $num = (!empty($pdata['start'])) ? $pdata['start'] : 0;

        $currentTime = time();
        foreach ($users as $user) {
            $user = (array) $user;

            $utime          = 0;
            $startDate      = 0;
            $endDate        = 0;
            $remainingDays  = 0;

            if ($user["utime"]) {
                $utime = Jalalian::forge($user["ctime"])->format('Y/m/d');
            }

            if ($user["start_date"]) {
                $startDate = Jalalian::forge($user["start_date"])->format('Y/m/d');
            }

            if ($user["end_date"]) {
                $endDate = Jalalian::forge($user["end_date"])->format('Y/m/d');
            }

            if ($user["end_date"] && $user["start_date"]) {

                if ($user["end_date"] > $currentTime) {
                    $remainingDays  = floor(($user["end_date"]  - $currentTime) / 86400);
                } else {
                    $remainingDays  = -1;
                }
            }


            $num = $num + 1;
            $row = array();

            $row['id']                  = $user["id"];
            $row['idx']                 = $num;
            $row['username']            = $user["username"];
            $row['admin_name']          = $user["admin_name"];
            $row['password']            = $user["password"];
            $row['limit_users']         = $user["limit_users"];
            $row['mobile']              = $user["mobile"];
            $row['is_active']           = $user["is_active"];
            $row['start_date']          = $startDate;
            $row['end_date']            = $endDate;
            $row['ctime']               = Jalalian::forge($user["ctime"])->format('Y/m/d');
            $row['utime']               = $utime;
            $row['traffic']             = trafficToGB($user["traffic"]);
            $row['consumer_traffic']    = trafficToGB($user["consumer_traffic"]);
            $row['remaining_days']      = $remainingDays;

            $resUsers[] = $row;
        }
        $result = $DataTable->make($resUsers);
        return $result;
    }

    public function isExistUsername($value, $uid = null)
    {
        $query = $this->where("username", $value);
        if ($uid != null) {
            $query->where('id', '!=', $uid);
        }
        return $query->count();
    }

    public function getInfo($userId)
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
            "admins.fullname as admin_name"
        ];

        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->join('admins', 'admins.username', '=', 'users.admin_uname')
            ->where("users.id", $userId)
            ->get();
        if ($query->count()) {
            $row                        = $query->first();

            $remainingDays              = 0;
            $currentTime                = time();
            $row->traffic               = trafficToGB($row->traffic);
            $row->consumer_traffic      = trafficToGB($row->consumer_traffic);

            if ($row->end_date) {
                $row->end_date_jd = Jalalian::forge($row->end_date)->format('Y/m/d');
            }
            if ($row->start_date) {
                $row->start_date_jd = Jalalian::forge($row->start_date)->format('Y/m/d');
            }

            if ($row->end_date && $row->start_date) {
                if ($currentTime + 1 >  $row->start_date) {
                    if ($currentTime < $row->end_date) {
                        $remainingDays  = floor(($row->end_date  - $currentTime) / 86400);
                    } else {
                        $remainingDays  = -1;
                    }
                }
            }
            $row->remaining_days    = $remainingDays;
            $row->netmod_qr_url     = generateNetmodQR($row);

            return  $row;
        }
        return false;
    }

    public function getByUsername($username)
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
        ];

        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->where("users.username", $username)
            ->get();
        if ($query->count()) {
            $row  = $query->first();
            return $row;
        }
        return false;
    }

    public function checkExist($id)
    {
        return $this->where("id", $id)->count();
    }

    public function toggleActive($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {

            $username = $userInfo->username;
            $password = $userInfo->password;

            $this->where("id", $userId)->update([
                "is_active" => !$userInfo->is_active
            ]);


            //exec shell
            if ($userInfo->is_active) {
                UserShell::deActivateUser($username);
            } else {
                UserShell::activateUser($username, $password);
            }
        }
    }

    public function resetTraffic($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {
            \App\Models\Traffics::where("username", $userInfo->username)->update([
                "download"  => 0,
                "upload"    => 0,
                "total"     => 0,
                "utime"     => time(),
            ]);
        }
    }

    public function deleteUser($userId, $uid)
    {
        $userInfo = $this->getInfo($userId);
        if ($userInfo) {
            $username = $userInfo->username;

            $this->where("id", $userId)->delete();
            \App\Models\Traffics::where("username", $userInfo->username)->delete();

            //delete from server
            UserShell::deleteUser($username);
        }
    }

    public function totalUsers($status = null)
    {
        $query = $this->where("id", ">", 0);
        if ($status == "active") {
            $query->where("is_active", 1);
        } else if ($status == "in_active") {
            $query->where("is_active", 0);
        }

        return  $query->count();
    }

    public function updateExpirydates($username, $startDate, $endDate)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $this->where("username", $username)
            ->update(["start_date", $startDate, "end_date" => $endDate]);
    }

    public function activeUsers()
    {
        $select = [
            "users.*",  "traffics.total as consumer_traffic",
        ];
        $query = db($this->table)->select($select)
            ->join('traffics', 'traffics.username', '=', 'users.username')
            ->where("users.is_active", 1)
            ->get();
        if ($query->count()) {
            return $query->toArray();
        }

        return false;
    }
}
