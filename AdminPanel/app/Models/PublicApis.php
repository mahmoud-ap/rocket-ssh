<?php

namespace App\Models;

use Morilog\Jalali\Jalalian;

class PublicApis extends \App\Models\BaseModel
{

    protected $table = 'public_apis';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'api_key',  'is_disabled', 'ctime'];

    public function __construct()
    {
        parent::__construct();
    }

    public function saveApi($pdata, $uid)
    {

        $isDisabled = isset($pdata["is_disabled"]) ? 1 : 0;

        $columnsArr                     = [];
        $columnsArr["name"]             = trim($pdata["name"]);
        $columnsArr["is_disabled"]      = $isDisabled;
        $columnsArr["api_key"]          = $pdata["token"];
        $columnsArr["ctime"]            = time();

        $this->insert($columnsArr);
        return  $columnsArr;
    }

    public function dataTableList($pdata)
    {
        $query = $this->select("*");

        $DataTable      = new \App\Libraries\DataTable($query, $pdata);
        $data          = $DataTable->query()->toArray();

        $resData = array();
        $num = (!empty($pdata['start'])) ? $pdata['start'] : 0;


        foreach ($data as $item) {
            $num = $num + 1;
            $row = array();
            $row['id']                  = $item["id"];
            $row['idx']                 = $num;
            $row['name']                = $item["name"];
            $row['api_key']             = $item["api_key"];
            $row['is_disabled']         = $item["is_disabled"];
            $row['ctime']               = Jalalian::forge($item["ctime"])->format('Y/m/d');
            
            $resData[] = $row;
        }
        $result = $DataTable->make($resData);
        return $result;
    }

    public function deleteApi($apiId, $uid)
    {
        $this->where("id", $apiId)->delete();
    }


    public function checkApiKey($apiKey)
    {
        $query = $this->where("api_key", $apiKey);
        if ($query->count()) {
            $row = $query->first();
            return  $row;
        }
        return false;
    }

    public function getInfo($apiId)
    {
        $query = $this->where("id", $apiId);
        if ($query->count()) {
            $row = $query->first();
            return  $row;
        }
        return false;
    }
}
