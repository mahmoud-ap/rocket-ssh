<?php
$userValues         = isset($userValues) ? $userValues : null;
$userId             = getArrayValue($userValues, "id");
$username           = getArrayValue($userValues, "username");
$password           = getArrayValue($userValues, "password");
$email              = getArrayValue($userValues, "email");
$mobile             = getArrayValue($userValues, "mobile");
$desc               = getArrayValue($userValues, "desc");
$traffic            = getArrayValue($userValues, "traffic");
$startDate          = getArrayValue($userValues, "start_date");
$endDate            = getArrayValue($userValues, "end_date");
$expiryDays         = getArrayValue($userValues, "expiry_days");
$expiryType         = getArrayValue($userValues, "expiry_type");
$concurrentUsers    = getArrayValue($userValues, "limit_users");
$endDateJD          = getArrayValue($userValues, "end_date_jd"); //jalali date


$formMethod = $userId ? "put" : "post";
$formAction = $userId ? baseUrl("ajax/users/$userId") : baseUrl("ajax/users");
$expiryType = !$userId ? "days" : (!$startDate ? "days" : "date");

$detailsUrl = "users/$userId/info";
?>

<div class="modal-dialog modal-lg">
    <form id="user-form" method="<?= $formMethod ?>" action="<?= $formAction ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php if (!empty($refrence)) { ?>
                        <?php if ($refrence == "details") { ?>
                            <button type="button" class="btn btn-secondary rounded-circle btn-icon btn-ajax-views" data-url="<?= $detailsUrl ?>">
                                <?= inlineIcon("arrow-right") ?>
                            </button>
                        <?php } ?>
                    <?php } ?>
                    <?= $userId  ? "ویرایش کاربر" : "افزودن کاربر" ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label for="username" class="form-label">نام کاربری (فقط حروف انگلیسی و اعداد)</label>
                            <input type="text" <?= $userId ? "disabled" : "" ?> value="<?= $username ?>" name="username" class="form-control" placeholder="نام کاربری را وارد کنید" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label for="password" class="form-label">رمز عبور</label>
                            <div class="input-group">
                                <input type="text" value="<?= $password ?>" name="password" class="form-control" placeholder="رمز عبور را وارد کنید" required>
                                <button class="btn btn-outline-primary" type="button" id="btn-generate-pass">
                                    <?= inlineIcon("key") ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label for="username" class="form-label">ایمیل</label>
                            <input type="email" value="<?= $email ?>" name="email" class="form-control text-end dir-ltr" dir="ltr" placeholder="example@example.com">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label for="mobile" class="form-label">شماره تلفن</label>
                            <input type="text" value="<?= $mobile ?>" name="mobile" class="form-control" placeholder="شماره تلفن کاربر را وارد کنید">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">

                        <div class="form-group mb-2">
                            <label for="limit_users" class="form-label">تعداد کاربران همزمان</label>
                            <input type="number" min="1" value="<?= $concurrentUsers ?>" name="limit_users" class="form-control" placeholder="تعداد کاربر را وارد کنید" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-2">
                            <label fotn class="form-label">مقدار ترافیک (0 نامحدود)</label>
                            <div class="input-group">
                                <input type="number" value="<?= $traffic ?>" name="traffic" min="0" class="form-control" placeholder="مقدار ترافیک قابل مصرف را وارد کنید" required>
                                <span class="input-group-text">گیگابایت</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <?php if (!$startDate) { ?>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group mb-3">
                                <label for="expiry_type" class="form-label">زمان انقضاء</label>
                                <div class="form-control">
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="expiry_type" value="days" required <?= $expiryType == "days" ? "checked" : "" ?>>
                                        <label class="form-check-label  mb-0"> براساس روز (از اولین اتصال)</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="expiry_type" value="date" required <?= $expiryType == "date" ? "checked" : "" ?>>
                                        <label class="form-check-label  mb-0">بر اساس تاریخ</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-lg-12 mb-2" id="expiry-by-days" style="display: <?= $expiryType == "days" ? "block" : "none" ?>;">
                        <div class="mb-2">
                            <div class="form-group mb-0">
                                <label for="exp_days" class="form-label">زمان انقضاء (از اولین اتصال)</label>
                                <div class="input-group">
                                    <input type="number" min="1" name="exp_days" value="<?= $expiryDays ?>" class="form-control" placeholder="تعداد روز" required>
                                </div>
                            </div>
                            <small class="text-warning">اگر می خواهید تاریخ انقضا را در اولین اتصال تنظیم کنید، تعداد روزهای اعتبار را در قسمت بالا وارد کنید</small>
                        </div>
                    </div>

                    <div class="col-lg-12 mb-2" id="expiry-by-date" style="display: <?= $expiryType == "date" ? "block" : "none" ?>">
                        <div class="form-group mb-0">
                            <label for="exp_date" class="form-label">تاریخ انقضاء</label>
                            <input type="text" class="form-control datepicker" name="exp_date" value="<?= $endDateJD ?>" placeholder="تاریخ را انتخاب کنید" required>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label fotn class="form-label">توضیحات</label>
                    <textarea name="desc" class="form-control" placeholder="متن توضیحات را وارد کنید"><?= $desc ?></textarea>
                </div>
            </div>
            <div class=" modal-footer">
                <button class="btn btn-primary btn-float-icon" type="submit">
                    <?= inlineIcon("save") ?>
                    <?= $userId ? " ویرایش کاربر" : " افزودن کاربر" ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    var formMode = "<?= !$userId ? "add" : "edit" ?>";
    window.initUsersForm(formMode);
</script>