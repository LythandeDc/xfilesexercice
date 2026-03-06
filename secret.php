<?php

$pageTitle = 'Classified Access';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark text-light border-warning shadow">
                <div class="card-header bg-black text-center border-warning">
                    <h3 class="text-warning mb-0"><i class="bi bi-lock-fill"></i> <?= t('classified_access') ?></h3>
                </div>
                <div class="card-body">
                    <p class="text-center text-light"><?= t('enter_secret_password') ?></p>
                    <form action="<?= APP_URL ?>/process/secret.php" method="post">
                        <div class="mb-3">
                            <label for="secret_password" class="form-label text-warning"><?= t('secret_password') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-warning" id="secret_password" name="secret_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100"><?= t('submit') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
