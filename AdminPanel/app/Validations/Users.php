<?php

namespace App\Validations;

class Users
{

    public static function save($pdata, $editId = null)
    {
        $result     = array("status" => "success", "messages" => []);
        $uModel     = new \App\Models\Users();

        $userInfo  = null;
        if ($editId) {
            $userInfo =  $uModel->getInfo($editId);
            if (!$userInfo) {
                $result["status"] = "error";
                $result["messages"][] = "اطلاعات کاربر یافت نشد";
                return $result;
            }
        }

        $validatorRules = [
            'password'          => ['required'],
            'email'             => ['nullable', 'email'],
            'mobile'            => ['nullable'],
            'desc'              => ['nullable'],
            'limit_users'  => ['required', 'numeric', 'min:1'],
            'expiry_type'       => ['required', 'in:days,date'],
            'exp_date'          => ['required_if:expiry_type,date', 'string'],
            'exp_days'          => ['required_if:expiry_type,days', 'numeric', 'min:1'],
            'traffic'           => ['required', 'numeric', 'min:1'],
        ];

        if (!$editId) {
            $validatorRules['username'] = [
                'required',
                function ($attribute, $value,  $fail) use ($uModel, $editId) {
                    if ($value) {
                        if ($uModel->isExistUsername($value, $editId)) {
                            $fail("لطفا از نام کاربری دیگری استفاده کنید");
                        }
                    }
                },
            ];
        } else {

            $startDate = $userInfo->start_date;
            if ($startDate) {
                unset($validatorRules["expiry_type"]);
                unset($validatorRules["exp_date"]);
                unset($validatorRules["exp_days"]);
                $validatorRules["exp_date"] = ['required'];
            }
        }

        $validatorMsg = [
            'username.required'             => 'نام کاربری را وارد کنید',
            'password.required'             => 'رمز عبور را وارد کنید',
            'password.email'                => 'ایمیل را به صورت صحیح وارد کنید',

            'limit_users.required'     => 'وارد کردن تعداد کاربران همزمان الزامی است',
            'limit_users.numeric'      => 'تعداد کاربران همزمان به صورت عدد وارد کنید',
            'limit_users.min'          => 'حداقل تعداد کاربران همزمان 1 است',

            'expiration_time.required'      => 'زمان انقضاء را وارد کنید',
            'expiration_time.in'            => 'زمان انقضاء را به صورت صحیح وارد کنید',

            'exp_date.required_if'          => 'زمان انقضاء را وارد کنید',
            'exp_date.numeric'              => 'زمان انقضاء را به صورت صحیح وارد کنید',
            'exp_date.min'                  => 'زمان انقضاء را به صورت صحیح وارد کنید',

            'exp_days.required_if'          => 'زمان انقضاء را وارد کنید',
            'exp_days.numeric'              => 'زمان انقضاء را به صورت صحیح وارد کنید',
            'exp_days.min'                  => 'زمان انقضاء را به صورت صحیح وارد کنید',

            'traffic.required'              => 'وارد کردن ترافیک الزامی است',
            'traffic.numeric'               => 'مقدار ترافیک را به صورت عددی وارد کنید',
            'traffic.min'                   => 'حداقل مقدار ترافیک 1 گیگابایت است',
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

        $uModel = new \App\Models\Users();
        $hasExist = $uModel->checkExist($userId);

        if (!$hasExist) {
            $result["status"] = "error";
            $result["messages"][] = "اطلاعات کاربر یافت نشد";
        }
        return $result;
    }

    public static function delete($manId, $uid)
    {
        $result = array("status" => "success", "messages" => []);

        $uModel = new \App\Models\Users();
        $hasExist = $uModel->checkExist($manId);

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
