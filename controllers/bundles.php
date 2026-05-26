<?php

require_once 'models/functions.php';

$filters = [
    'q' => $_GET['q'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
    'promo' => isset($_GET['promo']) ? 1 : 0
];
$sort = $_GET['sort'] ?? 'id DESC';

$bundles = getFilteredBundles($filters, $sort);
$allProducts = getProducts([], 'p.title ASC', 100);
$pageTitle = 'Nos Packs Exceptionnels';


require_once 'views/bundles.php';
