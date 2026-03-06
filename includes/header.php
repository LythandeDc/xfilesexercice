<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$currentLang = getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? escape($pageTitle) . ' - ' : '' ?><?= t('site_name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="xfiles-body">

<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-success">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-success" href="<?= APP_URL ?>/">
            <i class="bi bi-eye-fill"></i> <?= t('site_name') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/"><i class="bi bi-house"></i> <?= t('home') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/blog.php"><i class="bi bi-journal-text"></i> <?= t('blog') ?></a>
                </li>
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/views/dashboard.php"><i class="bi bi-speedometer2"></i> <?= t('nav_dashboard') ?></a>
                </li>
                <?php if (in_array(getUserRole(), ['editor', 'admin'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/admin/"><i class="bi bi-gear"></i> <?= t('admin_panel') ?></a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="me-3"><?= langSwitcher() ?></span>
                <?php if (isLoggedIn()): ?>
                    <span class="text-success me-3"><i class="bi bi-person-badge"></i> <?= escape($_SESSION['username']) ?></span>
                    <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline-danger btn-sm"><?= t('logout') ?></a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login.php" class="btn btn-outline-success btn-sm me-2"><?= t('login') ?></a>
                    <a href="<?= APP_URL ?>/register.php" class="btn btn-success btn-sm"><?= t('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
