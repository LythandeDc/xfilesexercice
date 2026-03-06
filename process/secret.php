<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/secret.php');
}

$secretPassword = $_POST['secret_password'] ?? '';
$pageTitle = 'Classified';
require_once __DIR__ . '/../includes/header.php';

if ($secretPassword === SECRET_CODE):
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-light border-warning shadow">
                <div class="card-header bg-black text-center border-warning">
                    <h3 class="text-warning"><i class="bi bi-unlock-fill"></i> <?= t('welcome_secret') ?></h3>
                </div>
                <div class="card-body">
                    <h4 class="text-success"><?= t('access_codes') ?></h4>
                    <div class="alert alert-success bg-dark border-success">
                        <code class="fs-4 text-success">CRD5-GTFT-CK65-JOPM-V29N-24G1-HH28-LLFV</code>
                    </div>
                    <p class="text-light"><?= t('codes_notice') ?></p>

                    <div class="d-flex gap-2 mt-4">
                        <a href="<?= APP_URL ?>/login.php" class="btn btn-success"><?= t('login') ?></a>
                        <a href="<?= APP_URL ?>/register.php" class="btn btn-outline-success"><?= t('register') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card bg-dark text-light border-danger shadow">
                <div class="card-body py-5">
                    <i class="bi bi-x-octagon-fill display-1 text-danger"></i>
                    <h3 class="text-danger mt-3"><?= t('try_again') ?></h3>
                    <a href="<?= APP_URL ?>/secret.php" class="btn btn-warning mt-3"><?= t('back_home') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endif;

require_once __DIR__ . '/../includes/footer.php';
?>
