<?php include "sections/head.php"; ?>


<div class="d-flex align-items-center justify-content-center vh-100 ">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-primary">404</h1>

        <p class="lead fw-bold">
            <span class="text-danger">خطا!</span> صفحه مورد نظر شما یافت نشد.
        </p>
        <a href="<?= baseUrl("/") ?>" class="btn btn-primary btn-float-icon">
            <?= inlineIcon("home") ?>
            خانه
        </a>
    </div>
</div>

<?php include "sections/footer.php"; ?>