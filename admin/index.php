<?php

$pageTitle = 'Admin';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
requireRole('editor', 'admin');

$db = getDatabase();
$totalArticles = (int)$db->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$totalPublished = (int)$db->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$totalDrafts = (int)$db->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
$totalScheduled = (int)$db->query("SELECT COUNT(*) FROM articles WHERE status = 'scheduled'")->fetchColumn();
$totalCats = (int)$db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalTags = (int)$db->query('SELECT COUNT(*) FROM tags')->fetchColumn();
$totalUsers = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();

$recent = $db->query('SELECT a.*, u.full_name AS author_name FROM articles a LEFT JOIN users u ON a.author_id = u.id ORDER BY a.updated_at DESC LIMIT 10')->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Admin Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <?php include __DIR__ . '/_sidebar.php'; ?>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <h1 class="text-success border-bottom border-success pb-2 mb-4"><i class="bi bi-speedometer2"></i> <?= t('admin_dashboard') ?></h1>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success text-center p-3">
                        <i class="bi bi-file-earmark-text display-4 text-success"></i>
                        <h2 class="text-success"><?= $totalArticles ?></h2>
                        <p class="mb-0"><?= t('total_articles') ?></p>
                        <small class="text-muted"><?= $totalPublished ?> <?= t('status_published') ?> / <?= $totalDrafts ?> <?= t('status_draft') ?> / <?= $totalScheduled ?> <?= t('status_scheduled') ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success text-center p-3">
                        <i class="bi bi-folder display-4 text-warning"></i>
                        <h2 class="text-warning"><?= $totalCats ?></h2>
                        <p class="mb-0"><?= t('total_categories') ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success text-center p-3">
                        <i class="bi bi-tags display-4 text-info"></i>
                        <h2 class="text-info"><?= $totalTags ?></h2>
                        <p class="mb-0"><?= t('total_tags') ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-light border-success text-center p-3">
                        <i class="bi bi-people display-4 text-danger"></i>
                        <h2 class="text-danger"><?= $totalUsers ?></h2>
                        <p class="mb-0"><?= t('total_users') ?></p>
                    </div>
                </div>
            </div>

            <h2 class="text-success"><i class="bi bi-clock-history"></i> <?= t('recent_articles') ?></h2>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover border-success">
                    <thead><tr class="text-success">
                        <th>ID</th><th><?= t('article_title') ?></th><th><?= t('article_language') ?></th><th><?= t('article_status') ?></th><th><?= t('by_author') ?></th><th><?= t('case_date') ?></th><th><?= t('actions') ?></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($recent as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td><?= escape($a['title']) ?></td>
                            <td><span class="badge bg-secondary"><?= strtoupper($a['lang']) ?></span></td>
                            <td>
                                <?php $statusColors = ['draft' => 'warning', 'published' => 'success', 'scheduled' => 'info', 'archived' => 'secondary']; ?>
                                <span class="badge bg-<?= $statusColors[$a['status']] ?? 'secondary' ?>"><?= t('status_' . $a['status']) ?></span>
                            </td>
                            <td><?= escape($a['author_name'] ?? '') ?></td>
                            <td><?= date('Y-m-d', strtotime($a['updated_at'])) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/article-edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
