<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">افزودن</h5>
            </div>
            <div class="card-body">
                <form id="api-form" method="POST" action="<?= baseUrl("ajax/settings/public-api") ?>">
                    <div class="form-group mb-2">
                        <label class="form-label">نام</label>
                        <input type="text" name="name" class="form-control" placeholder="نام را وارد کنید (مثال: ربات تلگرام)" required />
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">توکن</label>
                        <div class="input-group">
                            <input type="text" name="token" minlength="20" class="form-control" placeholder="توکن را وارد کنید" required>
                            <button class="btn btn-outline-primary" type="button" id="btn-generate-token">
                                <?= inlineIcon("key") ?>
                            </button>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" name="is_disabled" type="checkbox" value="">
                        <label class="form-check-label" for="is_disabled">
                            غیر فعال باشد
                        </label>
                    </div>
                    <div class="mb-2 text-center">
                        <button class="btn btn-primary btn-icon" type="submit">
                            <?= inlineIcon("save") ?>
                            ذخیره
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table id="apis-table" class="table " style="width: 100%;">

                </table>
            </div>
        </div>
    </div>
</div>

