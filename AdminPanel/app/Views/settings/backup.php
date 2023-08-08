<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">بارگذاری فایل پشتیبان</h6>
            </div>
            <div class="card-body">
                <form id="import-form" method="post" action="<?= baseUrl("ajax/settings/backup/import") ?>">
                    <div class="form-group mb-2">
                        <label>انتخاب از پنل</label>
                        <select class="form-select" name="import_from" required>
                            <option value="current">پنل فعلی</option>
                            <option value="shahan">پنل شاهان</option>
                            <option value="xpanel">ایکس پنل</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>انتخاب فایل دیتابیس <span class="unicode-bidi-plain">(*.sql)</span></label>
                        <input type="file" accept=".sql" name="sql_file" class="form-control" required />
                    </div>
                    <div class="alert alert-warning mb-3">
                        <p class="mb-0 fw-bold">به نکات زیر توجه کنید</p>
                        <ul>
                            <li>
                                در صورتی که فایل پشتیبان از پنل فعلی باشد تمام جدوال جایگزین میشوند.
                                و اگر اطلاعاتی از قبل در پنل داشته باشید تماما حذف میشوند
                            </li>
                            <li>
                                در صورتی که فایل پشتیبان از پنل های دیگری باشد مثل شاهان یا ایکس پنل باشد فقط محتوای جدوال users , traffic اضافه میشود
                                <br />
                                در صورتی که که کاربری از قبل وجود داشته باشد کاربر جدید جایگزین آن نمی شود.
                            </li>
                        </ul>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-float-icon">
                            <?= inlineIcon("download") ?>
                            ایمپورت
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">پشتیبان گیری</h6>
            </div>
            <div class="card-body">
                <button id="create-bkp-btn" class="ps-5 btn btn-secondary btn-float-icon">
                    <?= inlineIcon("upload") ?>
                    پیشتبان گیری جدید
                </button>
                <div class="border-bottom my-2"></div>
                <table class="table table-stripe" id="backup-table">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($backupFiles as $key => $file) {
                        ?>
                            <tr class="bkp-row" data-name="<?= $file["name"] ?>">
                                <td><?= $key + 1 ?></td>
                                <td><?= $file["name"] ?></td>
                                <td><?= $file["date"] ?></td>
                                <td>
                                    <a href="<?= $file["url"] ?>" target="_blank" class="btn btn-primary">
                                        <?= inlineIcon("download") ?>
                                        دانلود
                                    </a>
                                    <button class="btn btn-danger btn-delete-bkp" data-name="<?= $file["name"] ?>">
                                        <?= inlineIcon("trash") ?>
                                        حذف
                                    </button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>

        </div>
    </div>
    <div class="col-md-4"></div>
</div>

