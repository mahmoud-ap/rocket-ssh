<?php
$sshPort    = getArrayValue($settings, "ssh_port");
$udpPort    = getArrayValue($settings, "udp_port");
$multiuser  = getArrayValue($settings, "multiuser", 0);
$fakeUrl    = getArrayValue($settings, "fake_url");


?>

<form id="settings-form" method="post" action="<?= baseUrl("ajax/settings") ?>">
    <div class="form-group mb-2">
        <label class="form-label">پورت ssh</label>
        <input value="<?= $sshPort ?>" type="number" minlength="2" name="ssh_port" class="form-control" placeholder="پورت ssh" required />
    </div>
    <div class="alert alert-warning py-2">
        در صورت تغییر پورت ssh حتما دستور systemctl restart sshd را اجرا کنید.
    </div>
    <div class="form-group mb-2">
        <label class="form-label">پورت udp</label>
        <input value="<?= $udpPort ?>" type="number" minlength="4" name="udp_port" class="form-control" placeholder="پورت udp" required />
    </div>
    <div class="form-group mb-3">
        <label for="expiry_type" class="form-label">چند کاربری</label>
        <div class="form-control">
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="multiuser" value="1" required <?= $multiuser ? "checked" : "" ?>>
                <label class="form-check-label  mb-0">فعال</label>
            </div>
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="multiuser" value="0" required <?= !$multiuser ? "checked" : "" ?>>
                <label class="form-check-label  mb-0">غیر فعال</label>
            </div>
        </div>
    </div>
    <div class="form-group mb-3">
        <label class="form-label">آدرس سایت جعلی</label>
        <input value="<?= $fakeUrl ?>" type="text" name="fake_url" class="form-control" placeholder="آدرس سایت جعلی را وارد کنید" />
    </div>

    <div class="form-group mb-2">
        <button type="submit" class="btn btn-primary btn-float-icon">
            <?= inlineIcon("save") ?>
            ذخیره
        </button>
    </div>

</form>
