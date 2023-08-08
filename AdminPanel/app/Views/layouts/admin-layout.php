<?php include "sections/head.php"; ?>

<?php include "sections/sidemenu.php"; ?>
<div class="main-content-wrap m-0">
    <div class="container-fluid">
        <?php include "sections/navbar.php"; ?>
        <div class="main-content pt-4">
            <?php include viewContentPath($viewContent); ?>
        </div>
    </div>
</div>

<?php include "sections/footer.php"; ?>