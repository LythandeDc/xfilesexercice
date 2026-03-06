<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

function getCurrentLang(): string
{
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS, true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }

    return $_SESSION['lang'] ?? DEFAULT_LANG;
}

function loadTranslations(string $lang): array
{
    $file = __DIR__ . '/../lang/' . $lang . '.php';
    if (file_exists($file)) {
        return require $file;
    }
    return require __DIR__ . '/../lang/en.php';
}

function t(string $key): string
{
    static $translations = null;

    if ($translations === null) {
        $lang = getCurrentLang();
        $translations = loadTranslations($lang);
    }

    return $translations[$key] ?? $key;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }
}

function redirect(string $url, int $delay = 0): void
{
    if ($delay > 0) {
        header("Refresh:{$delay};url={$url}");
    } else {
        header("Location: {$url}");
        exit;
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function langSwitcher(): string
{
    $current = getCurrentLang();
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');
    $html = '';

    foreach (SUPPORTED_LANGS as $lang) {
        $active = ($lang === $current) ? ' active' : '';
        $label = strtoupper($lang);
        $html .= "<a href=\"{$currentUrl}?lang={$lang}\" class=\"btn btn-sm btn-outline-light{$active} mx-1\">{$label}</a>";
    }

    return $html;
}

function slugify(string $text): string
{
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text) ?: strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function requireRole(string ...$roles): void
{
    requireLogin();
    $db = getDatabase();
    $stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        echo t('access_denied');
        exit;
    }
}

function canPublish(): bool
{
    if (!isLoggedIn()) return false;
    $db = getDatabase();
    $stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user && in_array($user['role'], ['editor', 'admin'], true);
}

function getUserRole(): string
{
    if (!isLoggedIn()) return '';
    $db = getDatabase();
    $stmt = $db->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user['role'] ?? '';
}

function getCategories(PDO $db): array
{
    return $db->query('SELECT * FROM categories ORDER BY parent_id IS NULL DESC, parent_id, name')->fetchAll();
}

function getCategoryTree(PDO $db): array
{
    $cats = getCategories($db);
    $tree = [];
    $byId = [];
    foreach ($cats as &$cat) {
        $cat['children'] = [];
        $byId[$cat['id']] = &$cat;
    }
    unset($cat);
    foreach ($byId as &$cat) {
        if ($cat['parent_id'] && isset($byId[$cat['parent_id']])) {
            $byId[$cat['parent_id']]['children'][] = &$cat;
        } else {
            $tree[] = &$cat;
        }
    }
    return $tree;
}

function categoryOptions(PDO $db, ?int $selected = null, ?int $excludeId = null): string
{
    $tree = getCategoryTree($db);
    $html = '<option value="">' . t('select_category') . '</option>';
    $renderLevel = function(array $cats, int $depth) use (&$renderLevel, $selected, $excludeId): string {
        $out = '';
        foreach ($cats as $cat) {
            if ($excludeId && (int)$cat['id'] === $excludeId) continue;
            $indent = str_repeat('&mdash; ', $depth);
            $sel = ($selected && (int)$cat['id'] === $selected) ? ' selected' : '';
            $out .= "<option value=\"{$cat['id']}\"{$sel}>{$indent}{$cat['name']}</option>";
            if (!empty($cat['children'])) {
                $out .= $renderLevel($cat['children'], $depth + 1);
            }
        }
        return $out;
    };
    return $html . $renderLevel($tree, 0);
}

function featuredImageUrl(string $image): string
{
    if (str_starts_with($image, 'http')) return $image;
    if (str_starts_with($image, 'uploads/')) return APP_URL . '/' . $image;
    return APP_URL . '/assets/img/' . $image;
}
