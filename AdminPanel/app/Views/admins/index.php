<style>
    .hidden-password {
        filter: blur(2px);
        cursor: pointer;
    }
</style>
<div class="custome-breadcrumb has-actions">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= baseUrl("dashboard") ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?= baseUrl("admins") ?>">کاربران مدیر</a></li>
            <li class="breadcrumb-item active">لیست</li>
        </ol>
    </nav>
    <div class="actions">
        <button class="btn btn-primary btn-ajax-views btn-float-icon" data-url="admins/add">
            <?= inlineIcon("add", "icon") ?>
            افزودن
        </button>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table id="admins-table" class="table " style="width: 100%;">
            <tbody>

            </tbody>
        </table>
    </div>
</div>
