<?php

namespace App\Models;

class Settings extends \App\Models\BaseModel
{

    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'value'];


    public function saveMainSettings($pdata, $uid)
    {
        $validKeys = ["ssh_port", "udp_port", "multiuser", "fake_url"];

        if (is_array($pdata)) {
            foreach ($pdata as $key => $value) {
                if (in_array($key, $validKeys)) {

                    $this->updateOrCreate(['name' => $key], ["name" => $key, "value" => $value]);
                }
            }
        }
    }


    public function getSettings()
    {
        $result = [];

        $query = db($this->table)->get();
        if ($query->count()) {
            $rows   = $query->toArray();

            foreach ($rows as $row) {
                $result[$row->name] = $row->value;
            }
        }

        return $result;
    }

    public static function getSetting($name)
    {
        $query = db("settings")->where("name", $name)->get();

        if ($query->count()) {
            $row = $query->first();
            return $row->value;
        }

        return false;
    }
}
