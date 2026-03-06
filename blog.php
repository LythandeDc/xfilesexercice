<?php

$pageTitle = 'Blog';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

$db = getDatabase();
$lang = getCurrentLang();
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

$where = 'WHERE a.status = :status AND a.lang = :lang';
$params = ['status' => 'published', 'lang' => $lang];

if (!empty($_GET['category'])) {
    $where .= ' AND c.slug = :cat_slug';
    $params['cat_slug'] = $_GET['category'];
}
if (!empty($_GET['tag'])) {
    $where .= ' AND a.id IN (SELECT article_id FROM article_tags at2 JOIN tags t2 ON at2.tag_id = t2.id WHERE t2.slug = :tag_slug)';
    $params['tag_slug'] = $_GET['tag'];
}

$countSql = "SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON a.category_id = c.id {$where}";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$sql = "SELECT a.*, c.name AS category_name, c.slug AS category_slug, u.full_name AS author_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        {$where}
        ORDER BY a.published_at DESC
        LIMIT {$perPage} OFFSET {$offset}";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

$cats = $db->query("SELECT c.*, COUNT(a.id) AS cnt FROM categories c LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published' AND a.lang = '{$lang}' GROUP BY c.id ORDER BY c.name")->fetchAll();
$tags = $db->query("SELECT t.*, COUNT(at.article_id) AS cnt FROM tags t LEFT JOIN article_tags at ON at.tag_id = t.id LEFT JOIN articles a ON at.article_id = a.id AND a.status = 'published' AND a.lang = '{$lang}' GROUP BY t.id HAVING cnt > 0 ORDER BY t.name")->fetchAll();
?>

<div class="container py-5">
    <div class="row">
        <!-- Articles -->
        <div class="col-lg-8">
            <h1 class="text-success mb-4"><i class="bi bi-journal-text"></i> <?= t('blog') ?></h1>

            <?php if (empty($articles)): ?>
                <div class="alert alert-dark border-success"><?= t('no_articles') ?></div>
            <?php endif; ?>

            <div class="row g-4">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6">
                    <div class="card bg-dark text-light border-success h-100 xfiles-card">
                        <?php if ($article['featured_image']): ?>
                            <img src="<?= featuredImageUrl($article['featured_image']) ?>" class="card-img-top" alt="<?= escape($article['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <?php if ($article['category_name']): ?>
                                <a href="<?= APP_URL ?>/blog.php?category=<?= urlencode($article['category_slug']) ?>" class="badge bg-success text-decoration-none mb-2 align-self-start"><?= escape($article['category_name']) ?></a>
                            <?php endif; ?>
                            <h5 class="card-title text-success">
                                <a href="<?= APP_URL ?>/article.php?slug=<?= urlencode($article['slug']) ?>&lang=<?= $lang ?>" class="text-success text-decoration-none"><?= escape($article['title']) ?></a>
                            </h5>
                            <p class="card-text small flex-grow-1"><?= escape($article['excerpt'] ?? '') ?></p>
                            <div class="mt-auto">
                                <small class="text-muted">
                                    <?= t('published_on') ?> <?= date('M j, Y', strtotime($article['published_at'])) ?>
                                    <?= t('by_author') ?> <?= escape($article['author_name'] ?? '') ?>
                                </small>
                            </div>
                        </div>
                        <div class="card-footer border-success">
                            <a href="<?= APP_URL ?>/article.php?slug=<?= urlencode($article['slug']) ?>&lang=<?= $lang ?>" class="btn btn-outline-success btn-sm"><?= t('read_more') ?> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link bg-dark border-success <?= $i === $page ? 'bg-success text-dark' : 'text-success' ?>" href="?page=<?= $i ?><?= !empty($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '' ?><?= !empty($_GET['tag']) ? '&tag=' . urlencode($_GET['tag']) : '' ?>&lang=<?= $lang ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card bg-dark text-light border-success mb-4">
                <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-folder"></i> <?= t('categories') ?></h6></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-dark border-success">
                        <a href="<?= APP_URL ?>/blog.php?lang=<?= $lang ?>" class="text-light text-decoration-none"><?= t('all_articles') ?> <span class="badge bg-success float-end"><?= $total ?></span></a>
                    </li>
                    <?php foreach ($cats as $cat): ?>
                    <?php if ($cat['cnt'] > 0): ?>
                    <li class="list-group-item bg-dark border-success">
                        <a href="<?= APP_URL ?>/blog.php?category=<?= urlencode($cat['slug']) ?>&lang=<?= $lang ?>" class="text-light text-decoration-none">
                            <?= escape($cat['name']) ?> <span class="badge bg-success float-end"><?= $cat['cnt'] ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card bg-dark text-light border-success">
                <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-tags"></i> <?= t('tags') ?></h6></div>
                <div class="card-body">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?= APP_URL ?>/blog.php?tag=<?= urlencode($tag['slug']) ?>&lang=<?= $lang ?>" class="btn btn-sm btn-outline-success m-1"><?= escape($tag['name']) ?> (<?= $tag['cnt'] ?>)</a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
