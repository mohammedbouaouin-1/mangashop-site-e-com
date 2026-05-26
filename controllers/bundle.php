<?php

require_once 'models/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: bundles.php'); exit; }

$bundle = getBundleBySlug($slug);
if (!$bundle) { header('HTTP/1.0 404 Not Found'); include '404.php'; exit; }

$prods = getBundleProducts($bundle['id']);
$others = getRandomBundles(5);

$pageTitle = $bundle['name'];
require_once 'views/bundle.php';
