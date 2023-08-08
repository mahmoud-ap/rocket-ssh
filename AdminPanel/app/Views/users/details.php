<?php
$userValues         = isset($userValues) ? $userValues : null;
$userId             = getArrayValue($userValues, "id");
$username           = getArrayValue($userValues, "username");
$password           = getArrayValue($userValues, "password");
$email              = getArrayValue($userValues, "email");
$mobile             = getArrayValue($userValues, "mobile");
$traffic            = getArrayValue($userValues, "traffic");
$startDate          = getArrayValue($userValues, "start_date");
$endDate            = getArrayValue($userValues, "end_date");
$expiryDays         = getArrayValue($userValues, "expiry_days");
$expiryType         = getArrayValue($userValues, "expiry_type");
$concurrentUsers    = getArrayValue($userValues, "limit_users");
$consumerTraffic    = getArrayValue($userValues, "consumer_traffic");
$endDateJD          = getArrayValue($userValues, "end_date_jd"); //jalali date
$startJD            = getArrayValue($userValues, "start_date_jd"); //jalali date
$remainingDays      = getArrayValue($userValues, "remaining_days", 0);
$netmodQrUrl        = getArrayValue($userValues, "netmod_qr_url", "");
$isActive           = getArrayValue($userValues, "is_active", 1);
$adminName          = getArrayValue($userValues, "admin_name", "");
$desc               = getArrayValue($userValues, "desc", "");


$remainingText = "";
if ($remainingDays > 0) {
    $remainingText = "$remainingDays روز دیگر" ;
} else if ($remainingDays == -1) {
    $remainingText = "<span class='text-danger fw-bold'>منقضی شده</span>";
}

$editViewUrl = "users/$userId/edit?ref=details";

$values = [
    [
        "label" => "نام کاربری",
        "value" => $username,
    ],
    [
        "label" => "رمز عبور",
        "value" => "<span class='cursor-pointer' data-copy='true' data-text='$password' data-bs-toggle='tooltip' title='کپی'>$password</span>",
    ],
    [
        "label" =>  "ترافیک",
        "value" => $traffic . " گیگابایت",
    ],
    [
        "label" => "ترافیک مصرفی",
        "value" => $consumerTraffic ? "<span id='spn-user-traffic'>$consumerTraffic گیگابایت</span>" : 0
    ],
    [
        "label" =>  "تاریخ شروع",
        "value" => $startJD,
    ],
    [
        "label" => "تاریخ پایان",
        "value" => $endDateJD,
    ],
    [
        "label" =>  "زمان باقی مانده",
        "value" => $remainingText,
    ],
    [
        "label" =>  "تعداد کاربران",
        "value" => $concurrentUsers,
    ],
    [
        "label" => "ایمیل",
        "value" => $email,
    ],
    [
        "label" => "موبایل",
        "value" => $mobile,
    ],
    [
        "label" => "آی پی",
        "value" => getIPAddress(),
    ],
    [
        "label" => "ثبت کننده",
        "value" => $adminName,
    ],
    [
        "label" => "توضیحات",
        "value" => $desc,
    ],
];

?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">اطلاعات کاربر</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <div class="row">
                <div class="col-lg-8 border-end">
                    <?php
                    foreach ($values as  $item) {
                        $label = $item["label"];
                        $value = $item["value"];
                    ?>
                        <div class="d-flex flex-row align-items-center justify-content-between border-bottom py-2 px-3">
                            <span class="text-muted"><?= $label ?></span>
                            <span class=" fw-bold"><?= $value ? $value : "-" ?></span>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-lg-4">
                    <div class="p-2 h-100">
                        <div class="row justify-content-between align-items-center h-100">
                            <div class="col-6 col-sm-6 col-md-12">
                                <div class="actions">
                                    <button class="btn btn-primary w-100 mb-2 btn-float-icon btn-ajax-views" data-url="<?= $editViewUrl ?>">
                                        <?= inlineIcon("edit") ?>
                                        ویرایش
                                    </button>

                                    <button class="btn-chng-active btn mb-2 btn-<?= $isActive ? "warning" : "success" ?> w-100 mb-2 btn-float-icon" data-active="<?= $isActive ?>" data-id="<?= $userId ?>">
                                        <?= inlineIcon($isActive ? "pause" : "play") ?>
                                        <?= $isActive ? "غیر فعال کردن" : "فعال کردن" ?>
                                    </button>

                                    <?php if ($userRole == "admin") { ?>
                                        <button class="btn-reset-traffic btn btn-danger w-100 mb-2 btn-float-icon " data-id="<?= $userId ?>">
                                            <?= inlineIcon("rotate-left") ?>
                                            ریست ترافیک
                                        </button>
                                    <?php } ?>

                                    <button class="btn-copy-config btn btn-secondary w-100 mb-2 btn-float-icon">
                                        <?= inlineIcon("copy") ?>
                                        کپی کانفیگ
                                    </button>
                                </div>

                            </div>
                            <div class="col-6 col-sm-6 col-md-12">
                                <div class="border-md-top mt-2">
                                    <div class="my-2">
                                        <small>قابل استفاده در برنامه Netmod اندروید</small>
                                    </div>
                                    <img src="<?= $netmodQrUrl ?>" width="100%" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>