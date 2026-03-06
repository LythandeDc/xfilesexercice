<?php

$pageTitle = 'Article Editor';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
requireRole('editor', 'admin');

$db = getDatabase();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;
$articleTags = [];
$msg = $_SESSION['admin_msg'] ?? null;
unset($_SESSION['admin_msg']);

// Load article if editing
if ($id) {
    $stmt = $db->prepare('SELECT * FROM articles WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if ($article) {
        $stmt = $db->prepare('SELECT tag_id FROM article_tags WHERE article_id = :id');
        $stmt->execute(['id' => $id]);
        $articleTags = array_column($stmt->fetchAll(), 'tag_id');
    }
}

// Pre-fill for translation
$translateFrom = null;
if (!$id && isset($_GET['translate_from'])) {
    $stmt = $db->prepare('SELECT * FROM articles WHERE id = :id');
    $stmt->execute(['id' => (int)$_GET['translate_from']]);
    $translateFrom = $stmt->fetch();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'lang' => $_POST['lang'] ?? 'en',
        'translation_group' => !empty($_POST['translation_group']) ? (int)$_POST['translation_group'] : null,
        'seo_title' => trim($_POST['seo_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'og_title' => trim($_POST['og_title'] ?? ''),
        'og_description' => trim($_POST['og_description'] ?? ''),
        'featured_image' => trim($_POST['featured_image'] ?? ''),
    ];

    if ($data['slug'] === '') {
        $data['slug'] = slugify($data['title']);
    }

    // Handle image upload
    if (!empty($_FILES['image_upload']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $filename = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['image_upload']['tmp_name'], $uploadDir . $filename);
            $data['featured_image'] = 'uploads/' . $filename;
        }
    }

    // Determine status
    $action = $_POST['action'] ?? 'draft';
    if ($action === 'publish') {
        $data['status'] = 'published';
        $data['published_at'] = date('Y-m-d H:i:s');
        $data['scheduled_at'] = null;
    } elseif ($action === 'schedule') {
        $data['status'] = 'scheduled';
        $data['scheduled_at'] = $_POST['scheduled_at'] ?? null;
        $data['published_at'] = null;
    } elseif ($action === 'archive') {
        $data['status'] = 'archived';
        $data['scheduled_at'] = null;
    } else {
        $data['status'] = 'draft';
        $data['scheduled_at'] = null;
        $data['published_at'] = null;
    }

    $data['author_id'] = $_SESSION['user_id'];

    if ($id && $article) {
        // Update
        $sql = 'UPDATE articles SET title=:title, slug=:slug, excerpt=:excerpt, content=:content, featured_image=:featured_image, category_id=:category_id, author_id=:author_id, lang=:lang, translation_group=:translation_group, status=:status, seo_title=:seo_title, meta_description=:meta_description, og_title=:og_title, og_description=:og_description, published_at=:published_at, scheduled_at=:scheduled_at WHERE id=:id';
        $data['id'] = $id;
        // Keep existing dates if not changing status
        if ($action !== 'publish' && $article['published_at'] && $data['status'] === 'published') {
            $data['published_at'] = $article['published_at'];
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
    } else {
        // Insert
        $sql = 'INSERT INTO articles (title, slug, excerpt, content, featured_image, category_id, author_id, lang, translation_group, status, seo_title, meta_description, og_title, og_description, published_at, scheduled_at) VALUES (:title, :slug, :excerpt, :content, :featured_image, :category_id, :author_id, :lang, :translation_group, :status, :seo_title, :meta_description, :og_title, :og_description, :published_at, :scheduled_at)';
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        $id = (int)$db->lastInsertId();

        // Set translation_group to own id if not set
        if (!$data['translation_group']) {
            $db->prepare('UPDATE articles SET translation_group = :id WHERE id = :id')->execute(['id' => $id]);
        }
    }

    // Handle tags
    $db->prepare('DELETE FROM article_tags WHERE article_id = :id')->execute(['id' => $id]);
    $tagNames = array_filter(array_map('trim', explode(',', $_POST['tags_input'] ?? '')));
    foreach ($tagNames as $tagName) {
        if ($tagName === '') continue;
        $tagSlug = slugify($tagName);
        $stmt = $db->prepare('SELECT id FROM tags WHERE slug = :slug');
        $stmt->execute(['slug' => $tagSlug]);
        $tag = $stmt->fetch();
        if (!$tag) {
            $db->prepare('INSERT INTO tags (name, slug) VALUES (:name, :slug)')->execute(['name' => $tagName, 'slug' => $tagSlug]);
            $tagId = (int)$db->lastInsertId();
        } else {
            $tagId = (int)$tag['id'];
        }
        $db->prepare('INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (:aid, :tid)')->execute(['aid' => $id, 'tid' => $tagId]);
    }

    $_SESSION['admin_msg'] = t('article_saved');
    header('Location: ' . APP_URL . '/admin/article-edit.php?id=' . $id);
    exit;
}

// Load all tags for autocomplete
$allTags = $db->query('SELECT name FROM tags ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);

// Get tag names for this article
$tagNames = [];
if ($articleTags) {
    $placeholders = implode(',', array_fill(0, count($articleTags), '?'));
    $stmt = $db->prepare("SELECT name FROM tags WHERE id IN ({$placeholders})");
    $stmt->execute($articleTags);
    $tagNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Asset images for picker
$assetImages = glob(__DIR__ . '/../assets/img/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
$assetImages = array_map(fn($f) => basename($f), $assetImages ?: []);

// Translation info
$translations = [];
if ($article && $article['translation_group']) {
    $stmt = $db->prepare('SELECT id, lang, title FROM articles WHERE translation_group = :tg AND id != :id');
    $stmt->execute(['tg' => $article['translation_group'], 'id' => $article['id']]);
    $translations = $stmt->fetchAll();
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <?php include __DIR__ . '/_sidebar.php'; ?>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <h1 class="text-success border-bottom border-success pb-2 mb-4">
                <i class="bi bi-pencil-square"></i> <?= $id ? t('edit_article') : t('new_article') ?>
            </h1>

            <?php if ($msg): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= escape($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <?php if ($translations): ?>
            <div class="alert alert-info bg-dark border-info">
                <strong><?= t('translation_for') ?>:</strong>
                <?php foreach ($translations as $tr): ?>
                    <a href="<?= APP_URL ?>/admin/article-edit.php?id=<?= $tr['id'] ?>" class="btn btn-sm btn-outline-info"><?= strtoupper($tr['lang']) ?> - <?= escape($tr['title']) ?></a>
                <?php endforeach; ?>
                <?php
                $existingLangs = array_column($translations, 'lang');
                $existingLangs[] = $article['lang'];
                $missingLangs = array_diff(SUPPORTED_LANGS, $existingLangs);
                foreach ($missingLangs as $ml): ?>
                    <a href="<?= APP_URL ?>/admin/article-edit.php?translate_from=<?= $article['id'] ?>&lang=<?= $ml ?>" class="btn btn-sm btn-success"><i class="bi bi-plus"></i> <?= strtoupper($ml) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="articleForm">
                <div class="row">
                    <!-- Main content -->
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label text-success"><?= t('article_title') ?></label>
                            <input type="text" name="title" class="form-control form-control-lg bg-dark text-light border-success" value="<?= escape($article['title'] ?? $translateFrom['title'] ?? '') ?>" required id="titleInput">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-success"><?= t('slug') ?></label>
                            <input type="text" name="slug" class="form-control bg-dark text-light border-success" value="<?= escape($article['slug'] ?? '') ?>" placeholder="auto-generated-from-title" id="slugInput">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-success"><?= t('article_excerpt') ?></label>
                            <textarea name="excerpt" class="form-control bg-dark text-light border-success" rows="3"><?= escape($article['excerpt'] ?? $translateFrom['excerpt'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-success"><?= t('article_content') ?></label>
                            <textarea name="content" id="tinymce-editor"><?= $article['content'] ?? $translateFrom['content'] ?? '' ?></textarea>
                        </div>

                        <!-- SEO Section -->
                        <div class="card bg-dark border-success mb-3">
                            <div class="card-header bg-black border-success">
                                <h5 class="mb-0 text-success" data-bs-toggle="collapse" data-bs-target="#seoSection" role="button">
                                    <i class="bi bi-search"></i> <?= t('seo_settings') ?> <i class="bi bi-chevron-down float-end"></i>
                                </h5>
                            </div>
                            <div class="collapse show" id="seoSection">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-success"><?= t('seo_title') ?></label>
                                        <input type="text" name="seo_title" class="form-control bg-dark text-light border-success" value="<?= escape($article['seo_title'] ?? '') ?>" maxlength="255">
                                        <small class="text-muted">Max 60 characters recommended</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-success"><?= t('meta_description') ?></label>
                                        <textarea name="meta_description" class="form-control bg-dark text-light border-success" rows="2" maxlength="320"><?= escape($article['meta_description'] ?? '') ?></textarea>
                                        <small class="text-muted">Max 160 characters recommended</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-success"><?= t('og_title') ?></label>
                                        <input type="text" name="og_title" class="form-control bg-dark text-light border-success" value="<?= escape($article['og_title'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-success"><?= t('og_description') ?></label>
                                        <textarea name="og_description" class="form-control bg-dark text-light border-success" rows="2"><?= escape($article['og_description'] ?? '') ?></textarea>
                                    </div>

                                    <!-- SEO Preview -->
                                    <div class="border border-secondary rounded p-3 mt-3">
                                        <p class="text-muted small mb-1">Google Preview:</p>
                                        <p class="text-primary mb-0" id="seoPreviewTitle" style="font-size: 18px;"><?= escape($article['seo_title'] ?? $article['title'] ?? 'Article Title') ?></p>
                                        <p class="text-success small mb-0" id="seoPreviewUrl">localhost/xfilesexercice/article/<?= escape($article['slug'] ?? 'slug') ?></p>
                                        <p class="text-muted small mb-0" id="seoPreviewDesc"><?= escape($article['meta_description'] ?? 'Meta description will appear here...') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right sidebar -->
                    <div class="col-lg-4">
                        <!-- Publish box -->
                        <div class="card bg-dark border-success mb-3">
                            <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-send"></i> <?= t('publish') ?></h6></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('article_status') ?></label>
                                    <select name="status_display" class="form-select bg-dark text-light border-success" id="statusSelect">
                                        <?php $cs = $article['status'] ?? 'draft'; ?>
                                        <option value="draft" <?= $cs === 'draft' ? 'selected' : '' ?>><?= t('status_draft') ?></option>
                                        <option value="published" <?= $cs === 'published' ? 'selected' : '' ?>><?= t('status_published') ?></option>
                                        <option value="scheduled" <?= $cs === 'scheduled' ? 'selected' : '' ?>><?= t('status_scheduled') ?></option>
                                        <option value="archived" <?= $cs === 'archived' ? 'selected' : '' ?>><?= t('status_archived') ?></option>
                                    </select>
                                </div>

                                <div class="mb-3" id="scheduleBox" style="display:<?= ($article['status'] ?? '') === 'scheduled' ? 'block' : 'none' ?>;">
                                    <label class="form-label text-success"><?= t('schedule_date') ?></label>
                                    <input type="datetime-local" name="scheduled_at" class="form-control bg-dark text-light border-success" value="<?= $article['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($article['scheduled_at'])) : '' ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('article_language') ?></label>
                                    <select name="lang" class="form-select bg-dark text-light border-success">
                                        <?php foreach (SUPPORTED_LANGS as $l): ?>
                                        <option value="<?= $l ?>" <?= ($article['lang'] ?? $_GET['lang'] ?? 'en') === $l ? 'selected' : '' ?>><?= strtoupper($l) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php if ($translateFrom): ?>
                                <input type="hidden" name="translation_group" value="<?= $translateFrom['translation_group'] ?: $translateFrom['id'] ?>">
                                <?php elseif ($article): ?>
                                <input type="hidden" name="translation_group" value="<?= $article['translation_group'] ?>">
                                <?php endif; ?>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-warning" id="btnDraft"><i class="bi bi-file-earmark"></i> <?= t('save_draft') ?></button>
                                    <button type="submit" name="action" value="publish" class="btn btn-success" id="btnPublish"><i class="bi bi-send-check"></i> <?= t('publish') ?></button>
                                    <button type="submit" name="action" value="schedule" class="btn btn-outline-info" id="btnSchedule" style="display:none;"><i class="bi bi-clock"></i> <?= t('schedule') ?></button>
                                    <button type="submit" name="action" value="archive" class="btn btn-outline-secondary" id="btnArchive" style="display:none;"><i class="bi bi-archive"></i> <?= t('status_archived') ?></button>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="card bg-dark border-success mb-3">
                            <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-folder"></i> <?= t('category') ?></h6></div>
                            <div class="card-body">
                                <select name="category_id" class="form-select bg-dark text-light border-success">
                                    <?= categoryOptions($db, $article['category_id'] ?? ($translateFrom['category_id'] ?? null)) ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="card bg-dark border-success mb-3">
                            <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-tags"></i> <?= t('tags') ?></h6></div>
                            <div class="card-body">
                                <input type="text" name="tags_input" id="tagsInput" class="form-control bg-dark text-light border-success" value="<?= escape(implode(', ', $tagNames)) ?>" placeholder="Tag1, Tag2, Tag3...">
                                <small class="text-muted">Comma-separated. New tags are created automatically.</small>
                                <div class="mt-2" id="tagSuggestions">
                                    <?php foreach (array_slice($allTags, 0, 20) as $tn): ?>
                                    <span class="badge bg-secondary me-1 mb-1 tag-suggest" role="button"><?= escape($tn) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="card bg-dark border-success mb-3">
                            <div class="card-header bg-black border-success"><h6 class="mb-0 text-success"><i class="bi bi-image"></i> <?= t('featured_image') ?></h6></div>
                            <div class="card-body">
                                <?php if ($article && $article['featured_image']): ?>
                                <img src="<?= featuredImageUrl($article['featured_image']) ?>" class="img-fluid rounded mb-2" id="imagePreview">
                                <?php else: ?>
                                <img src="" class="img-fluid rounded mb-2 d-none" id="imagePreview">
                                <?php endif; ?>

                                <div class="mb-2">
                                    <label class="form-label text-success small"><?= t('upload_image') ?></label>
                                    <input type="file" name="image_upload" class="form-control form-control-sm bg-dark text-light border-success" accept="image/*">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label text-success small"><?= t('or_image_url') ?></label>
                                    <input type="text" name="featured_image" class="form-control form-control-sm bg-dark text-light border-success" value="<?= escape($article['featured_image'] ?? '') ?>" id="imageUrlInput">
                                </div>

                                <?php if ($assetImages): ?>
                                <label class="form-label text-success small"><?= t('image_from_assets') ?></label>
                                <div class="row g-1">
                                    <?php foreach ($assetImages as $ai): ?>
                                    <div class="col-4">
                                        <img src="<?= APP_URL ?>/assets/img/<?= $ai ?>" class="img-fluid rounded asset-pick" role="button" data-name="<?= $ai ?>" style="height:60px;width:100%;object-fit:cover;cursor:pointer;border:2px solid transparent;" title="<?= $ai ?>">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#tinymce-editor',
    skin: 'oxide-dark',
    content_css: 'dark',
    height: 500,
    menubar: 'file edit view insert format tools table',
    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor | removeformat | code fullscreen',
    content_style: 'body { background: #1a1a1a; color: #e0e0e0; font-family: sans-serif; }',
    promotion: false,
    branding: false
});

// Slug auto-generation
const titleInput = document.getElementById('titleInput');
const slugInput = document.getElementById('slugInput');
let slugManuallyEdited = slugInput.value !== '';
slugInput.addEventListener('input', () => slugManuallyEdited = true);
titleInput.addEventListener('input', () => {
    if (!slugManuallyEdited) {
        slugInput.value = titleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
});

// Status toggle
const statusSelect = document.getElementById('statusSelect');
const scheduleBox = document.getElementById('scheduleBox');
const btnPublish = document.getElementById('btnPublish');
const btnSchedule = document.getElementById('btnSchedule');
const btnArchive = document.getElementById('btnArchive');

statusSelect.addEventListener('change', function() {
    scheduleBox.style.display = this.value === 'scheduled' ? 'block' : 'none';
    btnSchedule.style.display = this.value === 'scheduled' ? 'block' : 'none';
    btnArchive.style.display = this.value === 'archived' ? 'block' : 'none';
    btnPublish.style.display = ['draft', 'published'].includes(this.value) ? 'block' : 'none';
});
statusSelect.dispatchEvent(new Event('change'));

// Tag suggestions
document.querySelectorAll('.tag-suggest').forEach(el => {
    el.addEventListener('click', () => {
        const input = document.getElementById('tagsInput');
        const tags = input.value.split(',').map(t => t.trim()).filter(t => t);
        const name = el.textContent.trim();
        if (!tags.includes(name)) {
            tags.push(name);
            input.value = tags.join(', ');
        }
        el.classList.toggle('bg-success');
        el.classList.toggle('bg-secondary');
    });
});

// Asset image picker
document.querySelectorAll('.asset-pick').forEach(img => {
    img.addEventListener('click', () => {
        document.getElementById('imageUrlInput').value = img.dataset.name;
        const preview = document.getElementById('imagePreview');
        preview.src = img.src;
        preview.classList.remove('d-none');
        document.querySelectorAll('.asset-pick').forEach(i => i.style.borderColor = 'transparent');
        img.style.borderColor = '#00ff41';
    });
});

// SEO preview
const seoTitle = document.querySelector('[name="seo_title"]');
const metaDesc = document.querySelector('[name="meta_description"]');
const seoPreviewTitle = document.getElementById('seoPreviewTitle');
const seoPreviewDesc = document.getElementById('seoPreviewDesc');
const seoPreviewUrl = document.getElementById('seoPreviewUrl');

function updateSeoPreview() {
    seoPreviewTitle.textContent = seoTitle.value || titleInput.value || 'Article Title';
    seoPreviewDesc.textContent = metaDesc.value || 'Meta description...';
    seoPreviewUrl.textContent = 'localhost/xfilesexercice/article/' + slugInput.value;
}
seoTitle.addEventListener('input', updateSeoPreview);
metaDesc.addEventListener('input', updateSeoPreview);
titleInput.addEventListener('input', updateSeoPreview);
slugInput.addEventListener('input', updateSeoPreview);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
