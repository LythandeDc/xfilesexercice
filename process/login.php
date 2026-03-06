<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$secretCode = $_POST['secret_code'] ?? '';

if ($username === '' || $password === '' || $secretCode === '') {
    $_SESSION['login_error'] = t('fields_required');
    redirect(APP_URL . '/login.php');
}

if ($secretCode !== SECRET_CODE) {
    $_SESSION['login_error'] = t('invalid_secret');
    redirect(APP_URL . '/login.php');
}

try {
    $db = getDatabase();
    $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];

        redirect(APP_URL . '/views/dashboard.php');
    }

    $_SESSION['login_error'] = t('login_error');
    redirect(APP_URL . '/login.php');

} catch (RuntimeException $e) {
    $_SESSION['login_error'] = t('login_error');
    redirect(APP_URL . '/login.php');
}
