<?php

namespace App\Models;

use Morilog\Jalali\Jalalian;


class Admins extends BaseModel
{

    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $fillable = ['username', 'password', 'fullname', 'role', 'credit', 'is_active', 'ctime', 'utime'];


    public function checkAdminLogin($pdata)
    {
        $username = $pdata["username"];
        $password = $pdata["password"];
        $userInfo  = $this->where("username", $username)
            ->where("is_active", 1)
            ->first();
        if ($userInfo) {
            if (password_verify($password, $userInfo->password)) {
                return $userInfo;
            }
        }
        return false;
    }

    public function getInfo($uid)
    {

        $query = db($this->table)->where("id", $uid)
            ->get();
        if ($query->count()) {
            return $query->first();
        }
        return false;
    }

    public function checkExist($id)
    {
        return $this->where("id", $id)->count();
    }

    public function saveUsers($pdata, $editId = null)
    {
        $columnsArr    = [];
        $pdata          = trimArrayValues($pdata);

        if (!empty($pdata["password"])) {
            $columnsArr["password"]         = password_hash($pdata["password"], PASSWORD_BCRYPT);
        }
        $columnsArr["fullname"]             = $pdata["fullname"];
        $columnsArr["role"]                 = $pdata["role"];
        $columnsArr["is_active"]            = $pdata["is_active"];

        if (!$editId) {
            $columnsArr["username"]         = $pdata["username"];
            $columnsArr["ctime"]            = time();
            $columnsArr["utime"]            = 0;
            $columnsArr["credit"]           = 0;
        } else {
            $columnsArr["utime"]            = time();
        }

        $this->updateOrCreate(['id' => $editId], $columnsArr);
    }


    public function deleteUser($userId, $uid)
    {
        $this->where("id", $userId)->delete();
    }

    public function dataTableList($pdata, $uid)
    {
        $select = ["admins.*"];

        $query = $this->select($select);
        $DataTable      = new \App\Libraries\DataTable($query, $pdata);
        $users          = $DataTable->query()->toArray();

        $resUsers = array();
        $num = (!empty($pdata['start'])) ? $pdata['start'] : 0;

        foreach ($users as $user) {
            $num = $num + 1;
            $row = array();

            $row['id']                  = $user["id"];
            $row['idx']                 = $num;
            $row['username']            = $user["username"];
            $row['fullname']            = $user["fullname"];
            $row['role']                = $user["role"];
            $row['is_active']           = $user["is_active"];
            $row['ctime']               = Jalalian::forge($user["ctime"])->format('Y/m/d');
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
}
