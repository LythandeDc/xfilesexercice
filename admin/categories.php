<?php

$pageTitle = 'Categories';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';
requireRole('editor', 'admin');

$db = getDatabase();
$msg = '';
$editCat = null;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
        $description = trim($_POST['description'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if ($name !== '') {
            if ($action === 'add') {
                $stmt = $db->prepare('INSERT INTO categories (name, slug, description, parent_id) VALUES (:name, :slug, :desc, :pid)');
                $stmt->execute(['name' => $name, 'slug' => $slug, 'desc' => $description, 'pid' => $parentId]);
            } else {
                $editId = (int)$_POST['edit_id'];
                $stmt = $db->prepare('UPDATE categories SET name=:name, slug=:slug, description=:desc, parent_id=:pid WHERE id=:id');
                $stmt->execute(['name' => $name, 'slug' => $slug, 'desc' => $description, 'pid' => $parentId, 'id' => $editId]);
            }
            $msg = t('article_saved');
        }
    } elseif ($action === 'delete') {
        $delId = (int)$_POST['delete_id'];
        $db->prepare('UPDATE categories SET parent_id = NULL WHERE parent_id = :id')->execute(['id' => $delId]);
        $db->prepare('UPDATE articles SET category_id = NULL WHERE category_id = :id')->execute(['id' => $delId]);
        $db->prepare('DELETE FROM categories WHERE id = :id')->execute(['id' => $delId]);
        $msg = t('article_deleted');
    }

    header('Location: ' . APP_URL . '/admin/categories.php' . ($msg ? '?msg=' . urlencode($msg) : ''));
    exit;
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute(['id' => (int)$_GET['edit']]);
    $editCat = $stmt->fetch();
}

$msg = $_GET['msg'] ?? '';
$tree = getCategoryTree($db);

function renderCategoryRow(array $cat, int $depth, string $appUrl): void {
    $indent = str_repeat('&mdash; ', $depth);
    $articleCount = 0; // Could query but keeping simple
    echo "<tr>
        <td>{$cat['id']}</td>
        <td>{$indent}" . escape($cat['name']) . "</td>
        <td><code>" . escape($cat['slug']) . "</code></td>
        <td>" . escape($cat['description'] ?? '') . "</td>
        <td>
            <div class='btn-group btn-group-sm'>
                <a href='{$appUrl}/admin/categories.php?edit={$cat['id']}' class='btn btn-outline-success'><i class='bi bi-pencil'></i></a>
                <form method='post' class='d-inline' onsubmit=\"return confirm('" . t('confirm_delete') . "')\">
                    <input type='hidden' name='action' value='delete'>
                    <input type='hidden' name='delete_id' value='{$cat['id']}'>
                    <button class='btn btn-outline-danger btn-sm'><i class='bi bi-trash'></i></button>
                </form>
            </div>
        </td>
    </tr>";
    foreach ($cat['children'] ?? [] as $child) {
        renderCategoryRow($child, $depth + 1, $appUrl);
    }
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar bg-dark border-end border-success p-3">
            <?php include __DIR__ . '/_sidebar.php'; ?>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <h1 class="text-success border-bottom border-success pb-2 mb-4"><i class="bi bi-folder"></i> <?= t('manage_categories') ?></h1>

            <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= escape($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="row">
                <!-- Form -->
                <div class="col-md-4">
                    <div class="card bg-dark border-success">
                        <div class="card-header bg-black border-success">
                            <h6 class="mb-0 text-success"><?= $editCat ? t('edit') : t('add') ?> <?= t('category') ?></h6>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="action" value="<?= $editCat ? 'update' : 'add' ?>">
                                <?php if ($editCat): ?><input type="hidden" name="edit_id" value="<?= $editCat['id'] ?>"><?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('category_name') ?></label>
                                    <input type="text" name="name" class="form-control bg-dark text-light border-success" value="<?= escape($editCat['name'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('slug') ?></label>
                                    <input type="text" name="slug" class="form-control bg-dark text-light border-success" value="<?= escape($editCat['slug'] ?? '') ?>" placeholder="auto-generated">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('parent_category') ?></label>
                                    <select name="parent_id" class="form-select bg-dark text-light border-success">
                                        <option value=""><?= t('no_parent') ?></option>
                                        <?= categoryOptions($db, $editCat['parent_id'] ?? null, $editCat['id'] ?? null) ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success"><?= t('category_description') ?></label>
                                    <textarea name="description" class="form-control bg-dark text-light border-success" rows="3"><?= escape($editCat['description'] ?? '') ?></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success"><?= $editCat ? t('update') : t('add') ?></button>
                                    <?php if ($editCat): ?>
                                    <a href="<?= APP_URL ?>/admin/categories.php" class="btn btn-outline-secondary"><?= t('cancel') ?></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List -->
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover border-success">
                            <thead><tr class="text-success">
                                <th>ID</th><th><?= t('category_name') ?></th><th><?= t('slug') ?></th><th><?= t('category_description') ?></th><th><?= t('actions') ?></th>
                            </tr></thead>
                            <tbody>
                            <?php foreach ($tree as $cat): renderCategoryRow($cat, 0, APP_URL); endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
