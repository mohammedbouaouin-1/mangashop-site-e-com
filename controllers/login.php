<?php

require_once 'models/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'      => $user['id'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'phone'   => $user['phone'],
                'address' => $user['address'],
                'city'    => $user['city'],
                'role'    => $user['role'] ?? 'user', 
                'joined_at' => $user['created_at']
            ];

            
            $savedCart = $db->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
            $savedCart->execute([$user['id']]);
            foreach ($savedCart->fetchAll() as $ci) {
                
                $pid = $ci['product_id'];
                $existing = $_SESSION['cart'][$pid]['qty'] ?? 0;
                $_SESSION['cart'][$pid] = ['qty' => max($existing, (int)$ci['quantity'])];
            }

            
            if (($user['role'] ?? 'user') === 'admin') {
                $_SESSION['admin'] = true;
                $_SESSION['admin_user'] = $user;
                header('Location: admin/index.php');
                exit;
            }

            $redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
            // Sécurité : n'accepter que les redirections internes simples
            $allowed = ['checkout.php', 'cart.php', 'account.php'];
            if ($redirect && in_array($redirect, $allowed)) {
                header('Location: ' . $redirect);
            } else {
                header('Location: account.php');
            }
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}

$pageTitle = 'Connexion';
require_once 'views/login.php';
