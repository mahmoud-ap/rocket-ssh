<?php
$userValues         = isset($userValues) ? $userValues : null;
$userId             = getArrayValue($userValues, "id");
$username           = getArrayValue($userValues, "username");
$fullname           = getArrayValue($userValues, "fullname");
$isActive           = getArrayValue($userValues, "is_active", 1);
$role               = getArrayValue($userValues, "role");

$formMethod = $userId ? "put" : "post";
$formAction = $userId ? baseUrl("ajax/admins/$userId") : baseUrl("ajax/admins");
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form id="user-form" method="<?= $formMethod ?>" action="<?= $formAction ?>">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?= $userId  ? "ویرایش کاربر مدیر" : "افزودن کاربر ادمین" ?>
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
                                <input type="text" value="" name="password" class="form-control" placeholder="رمز عبور را وارد کنید" <?= !$userId ? "required" : "" ?>>
                                <button class="btn btn-outline-primary" type="button" id="btn-generate-pass">
                                    <?= inlineIcon("key") ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-2">
                        <div class="form-group ">
                            <label for="username" class="form-label">نام کامل</label>
                            <input type="text" value="<?= $fullname ?>" name="fullname" class="form-control" placeholder="نام کامل را وارد کنید" required>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-2">
                        <div class="form-group">
                            <label for="mobile" class="form-label">نقش</label>
                            <select class="form-select" name="role">
                                <option value="admin" <?= $role == "admin" ? "selected" : "" ?>>مدیر</option>
                                <option value="employee" <?= $role == "employee" ? "selected" : "" ?>>کارمند</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="is_active" class="form-label">وضعیت</label>
                        <select class="form-select" name="is_active" required>
                            <option value="1" <?= $isActive ? "selected" : "" ?>>فعال</option>
                            <option value="0" <?= !$isActive  ? "selected" : "" ?>>غیر فعال</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class=" modal-footer">
                <button class="btn btn-primary btn-float-icon" type="submit">
                    <?= inlineIcon("save") ?>
                    <?= $userId ? " ویرایش کاربر" : " افزودن کاربر" ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    var formMode = "<?= !$userId ? "add" : "edit" ?>";
    window.initAdminForm(formMode);
</script>