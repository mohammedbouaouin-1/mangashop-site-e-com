<?php

require_once 'models/functions.php';

$q = trim($_GET['q'] ?? '');
$results  = $q ? getProducts(['q' => $q], 'p.id ASC', 24) : [];
$bundles  = $q ? getFilteredBundles(['q' => $q], 'id DESC', 6) : []; 
$popular  = getProducts([], 'p.rating DESC', 12);

$pageTitle = $q ? "Recherche : $q" : 'Recherche';
require_once 'views/search.php';
