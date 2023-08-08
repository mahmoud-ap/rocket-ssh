<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
        padding: 20px;
    }

    .signin-page {
        width: 100%;
        max-width: 450px;
        padding: 15px;
        margin: auto;
    }
</style>
<div class="card signin-page  shadow-lg">
    <div class="card-body">

        <div class="text-center">
            <h3 class="logo-text">Cyber Panel</h3>
            <h6 class="mb-4">لطفا وارد حساب کاربری خود شوید</h6>
        </div>

        <form id="login-form" class="mb-3" action="<?= baseUrl("ajax/login") ?>" method="POST">
            <div class="form-group mb-3">
                <label for="email" class="form-label">نام کاربری</label>
                <input type="text" class="form-control" id="email" name="username" placeholder="نام کاربری را وارد کنید..." autofocus required>
            </div>
            <div class="form-group form-password-toggle  mb-3">
                <label class="form-label required">رمز عبور</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" placeholder="رمز عبور" required>
                    <span class="input-group-text cursor-pointer">
                        <?= inlineIcon("eye-slash", "icon toggle-icon") ?>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" name="remember" type="checkbox" value="" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        مرا به خاطر بسپار
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary ripple w-100" type="submit">
                    <?= inlineIcon("arrow-right-to-bracket", "align-middle") ?>
                    وارد شوید
                </button>
            </div>
        </form>

    </div>
</div>