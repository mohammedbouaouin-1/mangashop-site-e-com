<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../account.php?tab=profile');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$city     = trim($_POST['city'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['password_confirm'] ?? '';

if (!$name || !$email) {
    $_SESSION['profile_error'] = 'Le nom et l\'email sont obligatoires.';
    header('Location: ../account.php?tab=profile');
    exit;
}

if ($password && $password !== $confirm) {
    $_SESSION['profile_error'] = 'Les mots de passe ne correspondent pas.';
    header('Location: ../account.php?tab=profile');
    exit;
}

$db = getDB();


$check = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$check->execute([$email, (int)$_SESSION['user']['id']]);
if ($check->fetch()) {
    $_SESSION['profile_error'] = 'Cet email est déjà utilisé par un autre compte.';
    header('Location: ../account.php?tab=profile');
    exit;
}

if ($password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, city=?, password=? WHERE id=?");
    $stmt->execute([$name, $email, $phone, $address, $city, $hash, (int)$_SESSION['user']['id']]);
} else {
    $stmt = $db->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, city=? WHERE id=?");
    $stmt->execute([$name, $email, $phone, $address, $city, (int)$_SESSION['user']['id']]);
}


$_SESSION['user']['name']    = $name;
$_SESSION['user']['email']   = $email;
$_SESSION['user']['phone']   = $phone;
$_SESSION['user']['address'] = $address;
$_SESSION['user']['city']    = $city;

$_SESSION['profile_success'] = 'Profil mis à jour avec succès !';
header('Location: ../account.php?tab=profile');
exit;
