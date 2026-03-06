<?php

$pageTitle = 'Articles';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
requireRole('editor', 'admin');

$db = getDatabase();

// Filters
$filterStatus = $_GET['status'] ?? '';
$filterLang = $_GET['flang'] ?? '';
$filterCat = $_GET['cat'] ?? '';

$where = '1=1';
$params = [];
if ($filterStatus) { $where .= ' AND a.status = :status'; $params['status'] = $filterStatus; }
if ($filterLang) { $where .= ' AND a.lang = :lang'; $params['lang'] = $filterLang; }
if ($filterCat) { $where .= ' AND a.category_id = :cat'; $params['cat'] = $filterCat; }

// Agents can only see their own
if (getUserRole() === 'agent') {
    $where .= ' AND a.author_id = :uid';
    $params['uid'] = $_SESSION['user_id'];
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare("SELECT COUNT(*) FROM articles a WHERE {$where}");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT a.*, c.name AS category_name, u.full_name AS author_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN users u ON a.author_id = u.id WHERE {$where} ORDER BY a.updated_at DESC LIMIT {$perPage} OFFSET {$offset}");
$stmt->execute($params);
$articles = $stmt->fetchAll();
$cats = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = (int)$_POST['delete_id'];
    $stmt = $db->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(['id' => $delId]);
    header('Location: ' . APP_URL . '/admin/articles.php');
    exit;
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <?php include __DIR__ . '/_sidebar.php'; ?>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between align-items-center border-bottom border-success pb-2 mb-4">
                <h1 class="text-success"><i class="bi bi-file-earmark-text"></i> <?= t('articles') ?></h1>
                <a href="<?= APP_URL ?>/admin/article-edit.php" class="btn btn-success"><i class="bi bi-plus"></i> <?= t('new_article') ?></a>
            </div>

            <!-- Filters -->
            <form class="row g-2 mb-4">
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm bg-dark text-light border-success">
                        <option value="">-- <?= t('article_status') ?> --</option>
                        <?php foreach (['draft', 'published', 'scheduled', 'archived'] as $s): ?>
                        <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= t('status_' . $s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="flang" class="form-select form-select-sm bg-dark text-light border-success">
                        <option value="">-- <?= t('article_language') ?> --</option>
                        <?php foreach (SUPPORTED_LANGS as $l): ?>
                        <option value="<?= $l ?>" <?= $filterLang === $l ? 'selected' : '' ?>><?= strtoupper($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="cat" class="form-select form-select-sm bg-dark text-light border-success">
                        <option value="">-- <?= t('category') ?> --</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filterCat == $c['id'] ? 'selected' : '' ?>><?= escape($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-success" type="submit"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="<?= APP_URL ?>/admin/articles.php" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover border-success">
                    <thead><tr class="text-success">
                        <th>ID</th><th><?= t('article_title') ?></th><th><?= t('article_language') ?></th><th><?= t('category') ?></th><th><?= t('article_status') ?></th><th><?= t('by_author') ?></th><th><?= t('case_date') ?></th><th><?= t('actions') ?></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($articles as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/article-edit.php?id=<?= $a['id'] ?>" class="text-success text-decoration-none"><?= escape($a['title']) ?></a>
                                <?php if ($a['translation_group']): ?><small class="text-muted">(TG:<?= $a['translation_group'] ?>)</small><?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= strtoupper($a['lang']) ?></span></td>
                            <td><?= escape($a['category_name'] ?? '-') ?></td>
                            <td>
                                <?php $sc = ['draft' => 'warning', 'published' => 'success', 'scheduled' => 'info', 'archived' => 'secondary']; ?>
                                <span class="badge bg-<?= $sc[$a['status']] ?? 'secondary' ?>"><?= t('status_' . $a['status']) ?></span>
                            </td>
                            <td><?= escape($a['author_name'] ?? '') ?></td>
                            <td><?= date('Y-m-d', strtotime($a['updated_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= APP_URL ?>/admin/article-edit.php?id=<?= $a['id'] ?>" class="btn btn-outline-success" title="<?= t('edit') ?>"><i class="bi bi-pencil"></i></a>
                                    <?php if ($a['status'] === 'published'): ?>
                                    <a href="<?= APP_URL ?>/article.php?slug=<?= urlencode($a['slug']) ?>&lang=<?= $a['lang'] ?>" class="btn btn-outline-info" target="_blank" title="View"><i class="bi bi-eye"></i></a>
                                    <?php endif; ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('<?= t('confirm_delete') ?>')">
                                        <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm" title="<?= t('delete') ?>"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="text-muted"><?= $total ?> <?= t('total_articles') ?></p>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
