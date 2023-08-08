<?php

namespace App\Validations;

class Admins
{

    public static function login($pdata)
    {
        $result = array("status" => "success", "messages" => []);

        $validatorRules = [
            'username'        => ['required'],
            'password'        => ['required'],
        ];

        $validatorMsg = [
            'username.required' => 'نام کاربری را وارد کنید',
            'password.required' => 'رمز عبور را وارد کنید',
        ];

        $validation = validator()->make(
            $pdata,
            $validatorRules,
            $validatorMsg
        );

        if ($validation->fails()) {
            $messages = $validation->errors()->getMessages();
            $messages[]                 = "لطفا ورودی های خود را کنترل کنید";
            $result["messages"]         = array_reverse($messages);
            $result["error_fields"]     = array_keys($messages);
            $result["status"]           = "error";
        }

        return $result;
    }

    public static function save($pdata, $editId = null)
    {
        $result     = array("status" => "success", "messages" => []);
        $aModel     = new \App\Models\Admins();

        $userInfo  = null;
        if ($editId) {
            $userInfo =  $aModel->getInfo($editId);
            if (!$userInfo) {
                $result["status"] = "error";
                $result["messages"][] = "اطلاعات کاربر یافت نشد";
                return $result;
            }
        }

        $validatorRules = [
            'fullname'          => ['required'],
            'role'              => ['required', 'in:admin,employee'],
            'is_active'         => ['required', 'in:0,1'],
        ];

        $passValidations =  ['regex:|[0-9]|', 'regex:|[a-zA-Z]|', 'min:8'];

        if (!$editId) {
            $validatorRules['username'] = [
                'required',
                function ($attribute, $value,  $fail) use ($aModel, $editId) {
                    if ($value) {
                        if ($aModel->isExistUsername($value, $editId)) {
                            $fail("لطفا از نام کاربری دیگری استفاده کنید");
                        }
                    }
                },
            ];
            $validatorRules['password'] = $passValidations;
        } else {
            if (!empty($pdata["password"])) {
                $validatorRules['password'] = $passValidations;
            }
        }


        $validatorMsg = [
            'username.required'             => 'نام کاربری را وارد کنید',
            'password.required'             => 'رمز عبور را وارد کنید',
            'fullname.required'             => 'نام کامل را وارد کنید',
            'role.required'                 => 'نقش کاربر را وارد کنید',
        ];

        $validation = validator()->make(
            $pdata,
            $validatorRules,
            $validatorMsg
        );

        if ($validation->fails()) {
            $messages = $validation->errors()->getMessages();
            $messages[]                 = "لطفا ورودی های خود را کنترل کنید";
            $result["messages"]         = array_reverse($messages);
            $result["error_fields"]     = array_keys($messages);
            $result["status"]           = "error";
        }

        return $result;
    }

    public static function hasExist($userId)
    {
        $result = array("status" => "success", "messages" => []);

        $aModel = new \App\Models\Admins();
        $hasExist = $aModel->checkExist($userId);

        if (!$hasExist) {
            $result["status"] = "error";
            $result["messages"][] = "اطلاعات کاربر یافت نشد";
        }
        return $result;
    }

    public static function delete($manId, $uid)
    {
        $result = array("status" => "success", "messages" => []);

        $aModel = new \App\Models\Admins();
        $hasExist = $aModel->checkExist($manId);

        if (!$hasExist) {
            $result["status"] = "error";
            $result["messages"][] = "اطلاعات کاربر یافت نشد";
        } else {
            if ($manId  == $uid) {
                $result["status"] = "error";
                $result["messages"][] = "کاربر انتخابی قابل حذف نیست";
            }
        }

        return $result;
    }
}
