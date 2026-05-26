<?php

require_once 'models/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city'] ?? '');

    if ($name && $email && $pass) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($pass) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($pass !== $confirm) {
            
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            $db = getDB();
            $check = $db->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'Cette adresse email est déjà utilisée.';
            } else {
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                
                $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, address, city) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$name, $email, $hashedPass, $phone, $address, $city]);

                $userId = $db->lastInsertId();

                $_SESSION['user'] = [
                    'id'      => $userId,
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'address' => $address,
                    'city'    => $city,
                    'role'    => 'user',
                    'joined_at' => date('Y-m-d H:i:s')
                ];

                header('Location: account.php');
                exit;
            }
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}

$pageTitle = 'Inscription';
require_once 'views/register.php';
