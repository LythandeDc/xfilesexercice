<?php

$pageTitle = null;
require_once __DIR__ . '/includes/header.php';
?>

<div class="hero-section text-center text-light py-5">
    <div class="container">
        <div class="py-5">
            <h1 class="display-3 fw-bold text-success glow-text"><?= t('hero_title') ?></h1>
            <p class="lead text-warning"><?= t('hero_subtitle') ?></p>
            <p class="fs-5 text-light opacity-75 mb-4"><?= t('hero_description') ?></p>
            <div class="d-flex justify-content-center gap-3">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/views/dashboard.php" class="btn btn-success btn-lg"><?= t('explore_cases') ?></a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login.php" class="btn btn-success btn-lg"><?= t('explore_cases') ?></a>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/secret.php" class="btn btn-outline-warning btn-lg"><?= t('classified_access') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <h2 class="text-center text-success mb-5"><i class="bi bi-folder-fill"></i> <?= t('featured_cases') ?></h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card bg-dark text-light border-success h-100 xfiles-card">
                <img src="<?= APP_URL ?>/assets/img/roswell.jpg" class="card-img-top" alt="Roswell" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title text-success"><?= t('case_1_title') ?></h5>
                    <p class="card-text"><?= t('case_1_desc') ?></p>
                </div>
                <div class="card-footer border-success">
                    <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> <?= t('deny_everything') ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-light border-success h-100 xfiles-card">
                <img src="<?= APP_URL ?>/assets/img/ufo-nimitz.jpg" class="card-img-top" alt="UFO Sighting" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title text-success"><?= t('case_2_title') ?></h5>
                    <p class="card-text"><?= t('case_2_desc') ?></p>
                </div>
                <div class="card-footer border-success">
                    <small class="text-warning"><i class="bi bi-shield-lock"></i> <?= t('trust_no_one') ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-light border-success h-100 xfiles-card">
                <img src="<?= APP_URL ?>/assets/img/alien-roswell.jpg" class="card-img-top" alt="Alien Evidence" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title text-success"><?= t('case_3_title') ?></h5>
                    <p class="card-text"><?= t('case_3_desc') ?></p>
                </div>
                <div class="card-footer border-success">
                    <small class="text-danger"><i class="bi bi-radioactive"></i> <?= t('i_want_to_believe') ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-dark py-5 border-top border-bottom border-success">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="text-success"><i class="bi bi-info-circle"></i> <?= t('about_division') ?></h3>
                <p class="text-light"><?= t('about_text') ?></p>
            </div>
            <div class="col-md-6 text-center">
                <img src="<?= APP_URL ?>/assets/img/ufo-passaic.jpg" class="img-fluid rounded shadow" alt="UFO" style="max-height: 300px;">
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
