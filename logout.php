<?php

session_start();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

session_start();

$pageTitle = 'Logout';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card bg-dark text-light border-success shadow">
                <div class="card-body py-5">
                    <i class="bi bi-shield-x display-1 text-warning"></i>
                    <h3 class="text-success mt-3"><?= t('logged_out') ?></h3>
                    <a href="<?= APP_URL ?>/" class="btn btn-success mt-3"><?= t('back_home') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
