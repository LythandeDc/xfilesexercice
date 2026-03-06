<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link text-success" href="<?= APP_URL ?>/admin/"><i class="bi bi-speedometer2"></i> <?= t('admin_dashboard') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/admin/articles.php"><i class="bi bi-file-earmark-text"></i> <?= t('articles') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/admin/article-edit.php"><i class="bi bi-plus-circle"></i> <?= t('new_article') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/admin/categories.php"><i class="bi bi-folder"></i> <?= t('manage_categories') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/admin/tags.php"><i class="bi bi-tags"></i> <?= t('manage_tags') ?></a>
    </li>
    <hr class="border-success">
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/blog.php"><i class="bi bi-box-arrow-up-right"></i> <?= t('blog') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-light" href="<?= APP_URL ?>/views/dashboard.php"><i class="bi bi-speedometer2"></i> <?= t('nav_dashboard') ?></a>
    </li>
</ul>
