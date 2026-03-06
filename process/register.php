<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/register.php');
}

$fullName = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';
$secretCode = $_POST['secret_code'] ?? '';

if ($fullName === '' || $username === '' || $email === '' || $password === '' || $secretCode === '') {
    $_SESSION['register_error'] = t('fields_required');
    redirect(APP_URL . '/register.php');
}

if ($secretCode !== SECRET_CODE) {
    $_SESSION['register_error'] = t('invalid_secret');
    redirect(APP_URL . '/register.php');
}

if ($password !== $passwordConfirm) {
    $_SESSION['register_error'] = t('passwords_mismatch');
    redirect(APP_URL . '/register.php');
}

try {
    $db = getDatabase();

    // Check if username exists
    $stmt = $db->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        $_SESSION['register_error'] = t('username_exists');
        redirect(APP_URL . '/register.php');
    }

    // Check if email exists
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $_SESSION['register_error'] = t('email_exists');
        redirect(APP_URL . '/register.php');
    }

    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare(
        'INSERT INTO users (full_name, username, email, password, created_at) VALUES (:full_name, :username, :email, :password, NOW())'
    );
    $stmt->execute([
        'full_name' => $fullName,
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
    ]);

    // Auto-login after registration
    $userId = (int) $db->lastInsertId();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;

    redirect(APP_URL . '/views/dashboard.php');

} catch (RuntimeException $e) {
    $_SESSION['register_error'] = t('register_error');
    redirect(APP_URL . '/register.php');
}
