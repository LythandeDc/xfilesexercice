<?php

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark text-light border-success shadow">
                <div class="card-header bg-black text-center border-success">
                    <h3 class="text-success mb-0"><i class="bi bi-shield-lock"></i> <?= t('login') ?></h3>
                    <small class="text-warning"><?= t('tagline') ?></small>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= escape($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= APP_URL ?>/process/login.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label text-success"><?= t('username') ?></label>
                            <input type="text" class="form-control bg-dark text-light border-success" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-success"><?= t('password') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-success" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="secret_code" class="form-label text-success"><?= t('secret_code') ?></label>
                            <input type="password" class="form-control bg-dark text-light border-success" id="secret_code" name="secret_code" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><?= t('login') ?></button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        <a href="<?= APP_URL ?>/register.php" class="text-warning"><?= t('register') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
