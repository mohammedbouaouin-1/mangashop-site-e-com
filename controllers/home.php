<?php

require_once 'models/functions.php';

$heroProducts = getProducts(['featured' => 1], 'p.rating DESC', 5);
$bestSellers  = getProducts(['featured' => 1], 'p.id ASC', 12);
$newReleases  = getProducts(['is_new' => 1], 'p.id ASC', 12);
$bundles      = getAllBundles(3);

$pageTitle = 'Accueil';
$pageDesc  = 'MangaShop – Vos mangas imprimés à la demande au Maroc.';

require_once 'views/home.php';
