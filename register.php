<?php

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';

$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-light border-success shadow">
                <div class="card-header bg-black text-center border-success">
                    <h3 class="text-success mb-0"><i class="bi bi-person-plus"></i> <?= t('register') ?></h3>
                    <small class="text-warning"><?= t('tagline') ?></small>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= escape($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>/process/register.php" method="post">
                        <div class="mb-3">
                            <label for="full_name" class="form-label text-success"><?= t('full_name') ?></label>
                            <input type="text" class="form-control bg-dark text-light border-success" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label text-success"><?= t('username') ?></label>
                            <input type="text" class="form-control bg-dark text-light border-success" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-success"><?= t('email') ?></label>
                            <input type="email" class="form-control bg-dark text-light border-success" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-success"><?= t('password') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-success" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label text-success"><?= t('password_confirm') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-success" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="mb-3">
                            <label for="secret_code" class="form-label text-success"><?= t('secret_code') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-success" id="secret_code" name="secret_code" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><?= t('register') ?></button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        <a href="<?= APP_URL ?>/login.php" class="text-warning"><?= t('login') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
