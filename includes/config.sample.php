<?php

// Détection automatique de l'environnement (local vs production)
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'mangashop');
} else {
    define('DB_HOST', 'VOTRE_MYSQL_HOSTNAME_INFINITYFREE'); // Exemple: sql213.infinityfree.com
    define('DB_USER', 'VOTRE_MYSQL_USERNAME_INFINITYFREE'); // Exemple: epiz_34123456
    define('DB_PASS', 'VOTRE_MOT_DE_PASSE_INFINITYFREE');
    define('DB_NAME', 'VOTRE_NOM_DE_BDD_INFINITYFREE');    // Exemple: epiz_34123456_mangashop
}

define('SITE_NAME',  'MangaShop');
define('SITE_EMAIL', 'contact@mangashop.ma');
define('CURRENCY',   'MAD');
define('WHATSAPP_NUMBER', '212600000000'); 


define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_placeholder');
define('STRIPE_SECRET_KEY',      getenv('STRIPE_SECRET_KEY')      ?: 'sk_test_placeholder');


function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            // Tentative de connexion directe à la base de données (idéal pour la production / InfinityFree)
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            autoInstallTables($pdo);
        } catch (PDOException $e) {
            // Si la connexion échoue et que nous sommes en local, on tente de créer la base de données
            if (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1') {
                try {
                    $pdo = new PDO(
                        'mysql:host='.DB_HOST.';charset=utf8mb4',
                        DB_USER, DB_PASS,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                        ]
                    );
                    $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.DB_NAME.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                    $pdo->exec('USE `'.DB_NAME.'`');
                    autoInstallTables($pdo);
                } catch (PDOException $innerE) {
                    die('<div style="padding:20px;background:#fee;border-left:4px solid #e63946;font-family:monospace">
                        <strong>Erreur de création de la base de données locale:</strong> '.htmlspecialchars($innerE->getMessage()).'<br>
                        Veuillez vérifier les identifiants DB_HOST, DB_USER, DB_PASS dans includes/config.php
                        </div>');
                }
            } else {
                die('<div style="padding:20px;background:#fee;border-left:4px solid #e63946;font-family:monospace">
                    <strong>Erreur de connexion à la base de données:</strong> '.htmlspecialchars($e->getMessage()).'<br>
                    Veuillez vérifier les identifiants DB_HOST, DB_USER, DB_PASS et DB_NAME dans includes/config.php
                    </div>');
            }
        }
    }
    return $pdo;
}

function autoInstallTables(PDO $pdo): void {
    $sqlFile = __DIR__ . '/../database.sql';
    if (!file_exists($sqlFile)) return;

    $existing = [];
    foreach ($pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN) as $t) {
        $existing[$t] = true;
    }

    $required = ['categories','users','products','bundles','bundle_products',
                 'orders','order_items','wishlist','reviews','promo_codes',
                 'newsletter','devis','login_attempts','admin_activity_logs','cart_items'];

    $missing = array_filter($required, fn($t) => !isset($existing[$t]));
    if (empty($missing)) return;

    $sql = file_get_contents($sqlFile);

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt === '') continue;
        $up = strtoupper(ltrim($stmt));
        if (str_starts_with($up, 'DROP') || str_starts_with($up, 'CREATE DATABASE') || str_starts_with($up, 'USE ') || str_starts_with($up, 'SET ')) continue;
        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            $code = $e->getCode();
            if ($code === '42S01') continue;
            if ($code === '23000') continue;
        }
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}


function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function discount(float $old, float $new): int { return (int) round((1 - $new / $old) * 100); }


function asset(string $path): string {
    if (empty($path)) return 'assets/img/placeholder.jpg';
    if (str_starts_with($path, 'http')) return $path;
    
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    
    $is_subdir = (str_contains($script, '/admin/') || str_contains($script, '/actions/'));
    $prefix = $is_subdir ? '../' : '';
    
    return $prefix . ltrim($path, '/');
}


if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
function cartCount(): int { return (int)array_sum(array_column($_SESSION['cart'] ?? [], 'qty')); }
