<style>
    .table td {
        min-width: 80px;
    }
</style>
<div class="custome-breadcrumb">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= baseUrl("dashboard") ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?= baseUrl("users") ?>">کاربران</a></li>
            <li class="breadcrumb-item active">لیست آنلاین ها</li>
        </ol>
    </nav>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table id="online-users-table" class="table table-striped" style="width: 100%;">
            <thead>
                <tr>
                    <th width="10%">ردیف</th>
                    <th>نام کاربری</th>
                    <th>تعداد</th>
                    <th>آی پی</th>
                    <th width="30%">عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $num = 0;
                foreach ($onlineUsers as $username => $userData) {
                    $num++;
                    $pids = [];
                ?>
                    <tr>
                        <td><?= $num ?></td>
                        <td><?= $username ?></td>
                        <td>
                            <span class="badge p-2 text-bg-<?= count($userData) > 1 ? "danger" : "primary" ?>">
                                <?= count($userData) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex  flex-column m-auto" style="max-width: 150px;">
                                <?php
                                foreach ($userData as $data) {
                                    $pids[] = $data["pid"];
                                ?>
                                    <div class="d-flex justify-content-between mb-1 align-items-center">
                                        <span><?= $data["ip"] ?></span>
                                        <button class="btn btn-danger btn-sm btn-kill-user" data-pid="<?= $data["pid"] ?>">
                                            <?= inlineIcon("user-slash") ?>
                                        </button>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-warning btn-kill-user" data-pid="<?= implode(",", $pids) ?>">
                                <?= inlineIcon("users-slash") ?>
                                Kill All
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
