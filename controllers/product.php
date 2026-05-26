<?php

require_once 'models/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: catalogue.php'); exit; }

$p = getProductBySlug($slug);
if (!$p) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }

$related = getRelatedProducts($p['category_id'], $p['id'], 8);

$pageTitle = $p['title'] . ' — ' . $p['author'];
require_once 'views/product.php';
