<?php

$pageTitle = 'Tags';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
requireRole('editor', 'admin');

$db = getDatabase();
$msg = '';
$editTag = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);

        if ($name !== '') {
            if ($action === 'add') {
                $stmt = $db->prepare('INSERT INTO tags (name, slug) VALUES (:name, :slug)');
                $stmt->execute(['name' => $name, 'slug' => $slug]);
            } else {
                $editId = (int)$_POST['edit_id'];
                $stmt = $db->prepare('UPDATE tags SET name=:name, slug=:slug WHERE id=:id');
                $stmt->execute(['name' => $name, 'slug' => $slug, 'id' => $editId]);
            }
            $msg = t('article_saved');
        }
    } elseif ($action === 'delete') {
        $delId = (int)$_POST['delete_id'];
        $db->prepare('DELETE FROM article_tags WHERE tag_id = :id')->execute(['id' => $delId]);
        $db->prepare('DELETE FROM tags WHERE id = :id')->execute(['id' => $delId]);
        $msg = t('article_deleted');
    }

    header('Location: ' . APP_URL . '/admin/tags.php' . ($msg ? '?msg=' . urlencode($msg) : ''));
    exit;
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM tags WHERE id = :id');
    $stmt->execute(['id' => (int)$_GET['edit']]);
    $editTag = $stmt->fetch();
}

$msg = $_GET['msg'] ?? '';
$tags = $db->query('SELECT t.*, COUNT(at.article_id) AS article_count FROM tags t LEFT JOIN article_tags at ON t.id = at.tag_id GROUP BY t.id ORDER BY t.name')->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <?php include __DIR__ . '/_sidebar.php'; ?>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <h1 class="text-success border-bottom border-success pb-2 mb-4"><i class="bi bi-tags"></i> <?= t('manage_tags') ?></h1>

            <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= escape($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-dark border-success">
                        <div class="card-header bg-black border-success">
                            <h6 class="mb-0 text-success"><?= $editTag ? t('edit') : t('add') ?> <?= t('tag') ?></h6>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="action" value="<?= $editTag ? 'update' : 'add' ?>">
                                <?php if ($editTag): ?><input type="hidden" name="edit_id" value="<?= $editTag['id'] ?>"><?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('tag_name') ?></label>
                                    <input type="text" name="name" class="form-control bg-dark text-light border-success" value="<?= escape($editTag['name'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('slug') ?></label>
                                    <input type="text" name="slug" class="form-control bg-dark text-light border-success" value="<?= escape($editTag['slug'] ?? '') ?>" placeholder="auto-generated">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success"><?= $editTag ? t('update') : t('add') ?></button>
                                    <?php if ($editTag): ?>
                                    <a href="<?= APP_URL ?>/admin/tags.php" class="btn btn-outline-secondary"><?= t('cancel') ?></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover border-success">
                            <thead><tr class="text-success">
                                <th>ID</th><th><?= t('tag_name') ?></th><th><?= t('slug') ?></th><th><?= t('articles') ?></th><th><?= t('actions') ?></th>
                            </tr></thead>
                            <tbody>
                            <?php foreach ($tags as $tag): ?>
                                <tr>
                                    <td><?= $tag['id'] ?></td>
                                    <td><?= escape($tag['name']) ?></td>
                                    <td><code><?= escape($tag['slug']) ?></code></td>
                                    <td><span class="badge bg-success"><?= $tag['article_count'] ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= APP_URL ?>/admin/tags.php?edit=<?= $tag['id'] ?>" class="btn btn-outline-success"><i class="bi bi-pencil"></i></a>
                                            <form method="post" class="d-inline" onsubmit="return confirm('<?= t('confirm_delete') ?>')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="delete_id" value="<?= $tag['id'] ?>">
                                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
