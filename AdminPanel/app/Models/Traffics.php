<?php

namespace App\Models;
use \App\Libraries\UserShell;

class Traffics extends BaseModel
{

    protected $table        = 'traffics';
    protected $primaryKey   = 'id';
    protected $fillable     = ['username', 'download', 'upload', 'total', 'ctime', 'utime'];



    public function totalData()
    {

        $query = db($this->table)->select([
            db()::raw("SUM(download) as download"),
            db()::raw("SUM(upload) as upload"),
            db()::raw("SUM(total) as total"),
        ])->get();

        $row =  $query->first();

        $result = [
            "download" => 0,
            "upload" => 0,
            "total" => 0,
        ];

        if (!empty($row)) {
            $result["download"] = !empty($row->download) ? convertToPrettyUnit($row->download) : 0;
            $result["upload"] = !empty($row->upload) ? convertToPrettyUnit($row->upload) : 0;
            $result["total"] = !empty($row->upload) ? convertToPrettyUnit($row->total) : 0;
        }
        return  $result;
    }


    public function getUserTraffic($username)
    {
        $query = db($this->table)->where("username", $username)
            ->get();
        if ($query->count()) {
            return $query->first();
        }
        return false;
    }



}
