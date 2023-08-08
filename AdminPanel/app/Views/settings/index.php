<div class="custome-breadcrumb">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= baseUrl("dashboard") ?>">داشبورد</a></li>
            <li class="breadcrumb-item active">تنظیمات</li>
        </ol>
    </nav>
</div>


<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $activeTab == "main" ? "active" : "" ?>" href="<?= baseUrl("settings") ?>">
                    <?= inlineIcon("gear") ?>
                    تنظیمات اصلی
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab == "backup" ? "active" : "" ?>" href="<?= baseUrl("settings/backup") ?>">
                    <?= inlineIcon("database") ?>
                    پشتیبان گیری
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab == "api" ? "active" : "" ?>" href="<?= baseUrl("settings/api") ?>">
                    <?= inlineIcon("server") ?>
                    تنظیمات API
                </a>
            </li>
        </ul>

        <div class="tab-pane fade show active">
            <?php
            if (file_exists(__DIR__ . DS . "$activeTab.php")) {
                require_once "$activeTab.php";
            }
            ?>
        </div>
    </div>
</div>