<?php

namespace App\Validations;



class Settings
{

    public function saveMainSettings($pdata)
    {
        $result     = array("status" => "success", "messages" => []);

        $validatorRules = [
            'ssh_port'        => ['required', 'numeric', 'min:2'],
            'udp_port'        => ['required', 'numeric', 'min:4'],
            'multiuser'       => ['required', 'numeric', 'in:1,0'],
            'fake_url'        => ['url'],
        ];

        $validatorMsg = [
            'ssh_port.required' => 'پورت ssh را وارد کنید',
            'ssh_port.numeric'  => 'پورت ssh را به صورت عددی وارد کنید',
            'ssh_port.min'      => 'پورت ssh حداقل باید از دورقم تشکیل شده باشد',

            'udp_port.required' => 'پورت udp را وارد کنید',
            'udp_port.numeric'  => 'پورت udp را به صورت عددی وارد کنید',
            'udp_port.min'      => 'پورت udp حداقل باید از چهار رقم تشکیل شده باشد',

            'multiuser.required' => 'وضعیت چند کاربری را مشخص کنید',
            'multiuser.numeric'  => 'وضعیت چند کاربری را به صورت صحیح ارسال کنید',
            'multiuser.min'      => 'وضعیت چند کاربری را به صورت صحیح ارسال کنید',

            'fake_url.url'      => 'آدرس جعلی را به صورت صحیح وارد کنید'

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


    public function addPublicApi($pdata, $editId = null)
    {
        $result     = array("status" => "success", "messages" => []);

        if ($editId) {
            $result = $this->publicApiInfo($editId);
            if ($result["status"] === "error") {
                return $result;
            }
        }

        $validatorRules = [
            'name'          => ["required"],
            'token'         => ["required", "min:20"],
            'is_disabled'   => ["numeric", "in:0,1"],
        ];

        $validatorMsg = [
            'name.required' => 'نام کاربری را وارد کنید',
            'token.required' => 'نام کاربری را وارد کنید',
            'token.min' => 'حداقل 20 کاراکتر برای توکن وارد کنید',
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

    public function publicApiInfo($id)
    {
        $result     = array("status" => "success", "messages" => []);

        $pModel   = new \App\Models\PublicApis();
        $apiInfo  = $pModel->getInfo($id);

        if (!$apiInfo) {
            $result["status"] = "error";
            $result["messages"][] = "Invalid Api id";
        }
        return $result;
    }

    public function importBackup($pdata)
    {

        $result     = array("status" => "success", "messages" => []);

        $validatorRules = [
            'file' => [
                'required', function ($attribute, $file,  $fail) {
                    if ($file && !empty($file->getClientFilename())) {
                        $extension = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
                        $hasErro = $file->getError();
                        if ($hasErro ||  $extension !== "sql") {
                            $fail("لطفا یک فایل sql صحیح ارسال کنید");
                        }
                    }
                },
            ],
            "import_from" => ["required", "in:current,shahan,xpanel"]
        ];


        $validatorMsg = [
            'file.required'         => 'فایلی جهت ایمپورت ارسال کنید',
            'import_from.required'  => 'نوع پنل را انتخاب کنید',
            'import_from.in'        => 'نوع پنل انتخابی صحیح نیست',
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
}
