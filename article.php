<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$db = getDatabase();
$slug = $_GET['slug'] ?? '';
$lang = getCurrentLang();

$stmt = $db->prepare('SELECT a.*, c.name AS category_name, c.slug AS category_slug, u.full_name AS author_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE a.slug = :slug AND a.lang = :lang AND a.status = :status LIMIT 1');
$stmt->execute(['slug' => $slug, 'lang' => $lang, 'status' => 'published']);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    $pageTitle = '404';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h1 class="text-danger">404</h1><p class="text-light">' . t('no_articles') . '</p></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Get tags
$stmt = $db->prepare('SELECT t.* FROM tags t JOIN article_tags at ON t.id = at.tag_id WHERE at.article_id = :id');
$stmt->execute(['id' => $article['id']]);
$articleTags = $stmt->fetchAll();

// Get translations
$translations = [];
if ($article['translation_group']) {
    $stmt = $db->prepare('SELECT lang, slug FROM articles WHERE translation_group = :tg AND id != :id AND status = :status');
    $stmt->execute(['tg' => $article['translation_group'], 'id' => $article['id'], 'status' => 'published']);
    $translations = $stmt->fetchAll();
}

// Related articles
$stmt = $db->prepare('SELECT a.title, a.slug, a.featured_image, a.excerpt FROM articles a WHERE a.category_id = :cat AND a.id != :id AND a.lang = :lang AND a.status = :status ORDER BY a.published_at DESC LIMIT 3');
$stmt->execute(['cat' => $article['category_id'], 'id' => $article['id'], 'lang' => $lang, 'status' => 'published']);
$related = $stmt->fetchAll();

$pageTitle = $article['seo_title'] ?: $article['title'];
$metaDesc = $article['meta_description'] ?: $article['excerpt'];
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($metaDesc): ?>
<script>document.querySelector('meta[name="description"]')?.remove();document.head.insertAdjacentHTML('beforeend','<meta name="description" content="<?= escape($metaDesc) ?>">');</script>
<?php endif; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>/blog.php?lang=<?= $lang ?>" class="text-success"><?= t('blog') ?></a></li>
                    <?php if ($article['category_name']): ?>
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>/blog.php?category=<?= urlencode($article['category_slug']) ?>&lang=<?= $lang ?>" class="text-success"><?= escape($article['category_name']) ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active text-muted"><?= escape($article['title']) ?></li>
                </ol>
            </nav>

            <article class="card bg-dark text-light border-success">
                <?php if ($article['featured_image']): ?>
                <img src="<?= featuredImageUrl($article['featured_image']) ?>" class="card-img-top" alt="<?= escape($article['title']) ?>" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="text-success mb-3"><?= escape($article['title']) ?></h1>
                    <div class="mb-3 text-muted">
                        <i class="bi bi-calendar"></i> <?= t('published_on') ?> <?= date('F j, Y', strtotime($article['published_at'])) ?>
                        <?= t('by_author') ?> <strong class="text-warning"><?= escape($article['author_name'] ?? '') ?></strong>
                        <?php if ($article['category_name']): ?>
                        | <i class="bi bi-folder"></i> <a href="<?= APP_URL ?>/blog.php?category=<?= urlencode($article['category_slug']) ?>&lang=<?= $lang ?>" class="text-success"><?= escape($article['category_name']) ?></a>
                        <?php endif; ?>
                    </div>

                    <?php if ($translations): ?>
                    <div class="mb-3">
                        <?php foreach ($translations as $tr): ?>
                        <a href="<?= APP_URL ?>/article.php?slug=<?= urlencode($tr['slug']) ?>&lang=<?= $tr['lang'] ?>" class="btn btn-sm btn-outline-warning me-1"><?= strtoupper($tr['lang']) ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="article-content fs-5 lh-lg">
                        <?= $article['content'] ?>
                    </div>

                    <?php if ($articleTags): ?>
                    <div class="mt-4 pt-3 border-top border-success">
                        <i class="bi bi-tags text-success"></i>
                        <?php foreach ($articleTags as $tag): ?>
                        <a href="<?= APP_URL ?>/blog.php?tag=<?= urlencode($tag['slug']) ?>&lang=<?= $lang ?>" class="btn btn-sm btn-outline-success m-1"><?= escape($tag['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </article>

            <?php if ($related): ?>
            <h3 class="text-success mt-5 mb-3"><i class="bi bi-link-45deg"></i> <?= t('related_articles') ?></h3>
            <div class="row g-3">
                <?php foreach ($related as $rel): ?>
                <div class="col-md-4">
                    <div class="card bg-dark text-light border-success h-100 xfiles-card">
                        <?php if ($rel['featured_image']): ?>
                        <img src="<?= featuredImageUrl($rel['featured_image']) ?>" class="card-img-top" style="height: 120px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6><a href="<?= APP_URL ?>/article.php?slug=<?= urlencode($rel['slug']) ?>&lang=<?= $lang ?>" class="text-success text-decoration-none"><?= escape($rel['title']) ?></a></h6>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card bg-dark text-light border-success mb-4">
                <div class="card-header bg-black border-success text-center">
                    <i class="bi bi-person-circle display-4 text-success"></i>
                    <h5 class="text-success mt-2"><?= escape($article['author_name'] ?? '') ?></h5>
                    <small class="text-muted">X-Files Division Agent</small>
                </div>
            </div>

            <div class="card bg-dark text-light border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-eye-fill display-4 text-warning"></i>
                    <p class="text-warning mt-2 mb-0 fst-italic">"<?= t('tagline') ?>"</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
