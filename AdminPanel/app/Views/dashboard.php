<div class="row mb-3">
    <div class="col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6>تعداد کل کاربران</h6>
                        <h5 class="fw-bold text-primary"><?= $totalData["users"]["all"] ?></h5>
                    </div>
                    <span class="widget-icon fs-1 text-primary">
                        <?= inlineIcon("users") ?>
                    </span>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="stretched-link" href="<?= baseUrl("users") ?>">مشاهده کاربران</a>
                <div class="fs-6">
                    <?= inlineIcon("chevron-left") ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6>تعداد کاربران آنلاین</h6>
                        <h5 class="fw-bold text-success"><?= $totalData["users"]["online"] ?></h5>
                    </div>
                    <span class="widget-icon fs-1 text-success">
                        <?= inlineIcon("earth-americas") ?>
                    </span>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="stretched-link" href="<?= baseUrl("users/online") ?>">مشاهده کاربران</a>
                <div class="fs-6">
                    <?= inlineIcon("chevron-left") ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6>تعداد کاربران فعال</h6>
                        <h5 class="fw-bold text-info"><?= $totalData["users"]["active"] ?></h5>
                    </div>
                    <span class="widget-icon fs-1 text-info">
                        <?= inlineIcon("users-line") ?>
                    </span>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="stretched-link" href="<?= baseUrl("users") ?>">مشاهده کاربران</a>
                <div class="fs-6">
                    <?= inlineIcon("chevron-left") ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6>تعداد کاربران غیر فعال</h6>
                        <h5 class="fw-bold text-warning"><?= $totalData["users"]["inActive"] ?></h5>
                    </div>
                    <span class="widget-icon fs-1 text-warning">
                        <?= inlineIcon("users-slash") ?>
                    </span>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="stretched-link" href="<?= baseUrl("users") ?>">مشاهده کاربران</a>
                <div class="fs-6">
                    <?= inlineIcon("chevron-left") ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6 class="mb-0">مقدار مصرف RAM</h6>
                    </div>
                    <span class="widget-icon fs-2 text-primary">
                        <?= inlineIcon("memory") ?>
                    </span>
                </div>


                <div class="progress" style="height: 25px;">
                    <div class="progress-bar" style="background-color:<?= $ramData["usage_color"] ?>;color:<?= $ramData["usage_text_color"] ?>; width: <?= $ramData["usage_percent"] ?>%"><?= $ramData["usage_percent"] ?>%</div>
                </div>
                <div class="d-flex justify-content-between flex-row-reverse mt-1">
                    <div><span class="text-muted">Total:</span> <b><?= $ramData["total"] ?></b></div>
                    <div><span class="text-muted">Used:</span> <b><?= $ramData["used"] ?></b></div>
                    <div><span class="text-muted">Available: </span> <b><?= $ramData["available"] ?></b></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6 class="mb-0">مقدار مصرف CPU</h6>
                    </div>
                    <span class="widget-icon fs-2 text-info">
                        <?= inlineIcon("microchip") ?>
                    </span>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar" style="color:<?= $cpuData["usage_text_color"] ?>;background-color:<?= $cpuData["usage_color"] ?>;width: <?= $cpuData['loadAvg'] ?>%"><?= $cpuData['loadAvg'] ?>%</div>
                </div>
                <div class="d-flex justify-content-between flex-row-reverse mt-1">
                    <div><span class="text-muted">Toal Cores:</span> <b><?= $cpuData["totalCores"] ?></b></div>
                    <div style="unicode-bidi:plaintext"><span class="text-muted">Name:</span>
                        <b>
                            <small class="cursor-pointer" title="<?= $cpuData["name"] ?>">
                                <?= truncateStr($cpuData["name"], 20) ?>
                            </small>
                        </b>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6 class="mb-0">مقدار مصرف هارد</h6>
                    </div>
                    <span class="widget-icon fs-2 text-success">
                        <?= inlineIcon("hard-drive") ?>
                    </span>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar" style="color:<?= $diskData["usage_text_color"] ?>;background-color:<?= $diskData["usage_color"] ?>;width: <?= $diskData['usage_percent'] ?>%"><?= $diskData['usage_percent'] ?>%</div>
                </div>
                <div class="d-flex justify-content-between flex-row-reverse mt-1">
                    <div><span class="text-muted">Total:</span> <b><?= $diskData["total"] ?></b></div>
                    <div><span class="text-muted">Free:</span> <b><?= $diskData["free"] ?></b></div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h6 class="mb-0">ترافیک مصرفی</h6>
                    </div>
                    <span class="widget-icon fs-2 text-warning">
                        <?= inlineIcon("tachometer-alt") ?>
                    </span>
                </div>
                <div class="d-flex1 ">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex ">
                            <span class="text-muted me-2">سرور:</span>
                            <span><?= $serverTraffic["total"] ?> </span>
                        </div>
                        <small>[دانلود:<?= $serverTraffic["download"] ?> -  آپلود: <?= $serverTraffic["upload"] ?> ]</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div class="d-flex ">
                            <span class="text-muted me-2">کاربران:</span>
                            <span><?= $userTraffic["total"] ?> </span>
                        </div>
                        <small>[دانلود:<?= $userTraffic["download"] ?>  -  آپلود: <?= $userTraffic["upload"] ?> ]</small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "users/last-table.php" ?>