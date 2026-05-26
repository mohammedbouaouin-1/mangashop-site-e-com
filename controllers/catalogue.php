<?php

require_once 'models/functions.php';

$filters = [
    'cat'       => trim($_GET['cat'] ?? ''),
    'q'         => trim($_GET['q'] ?? ''),
    'promo'     => !empty($_GET['promo']) ? 1 : 0,
    'author'    => trim($_GET['author'] ?? ''),
    'max_price' => trim($_GET['max_price'] ?? ''),
];
$sort = $_GET['sort'] ?? '';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 16;
$offset  = ($page - 1) * $perPage;


$sortMap = [
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'new'        => 'p.is_new DESC, p.id DESC',
    'rating'     => 'p.rating DESC',
    'best'       => 'p.featured DESC, p.id ASC'
];
$orderBy = $sortMap[$sort] ?? 'p.id ASC';

$products   = getProducts($filters, $orderBy, $perPage, $offset);
$total      = countProducts($filters);
$pages      = (int)ceil($total / $perPage);
$cats       = getAllCategories();
$catCounts  = getCategoryCounts();


$pageTitle  = $filters['cat'] ? ucfirst($filters['cat']) : ($filters['q'] ? "\"{$filters['q']}\"" : 'Catalogue');
$currentCat = null;
if ($filters['cat']) {
    foreach ($cats as $c) {
        if ($c['slug'] === $filters['cat']) { $currentCat = $c; break; }
    }
}


if (isset($_GET['ajax'])) {
    if ($products) {
        echo '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:24px;">';
        foreach ($products as $p) {
            include 'includes/product_card.php';
        }
        echo '</div>';

        
        if ($pages > 1) {
            echo '<div style="display:flex; justify-content:center; gap:8px; margin-top:64px;">';
            if ($page > 1) {
                $prevUrl = '?' . http_build_query(array_merge($_GET, ['page' => $page - 1]));
                echo '<a href="' . e($prevUrl) . '" class="pagination-link" data-page="' . ($page - 1) . '" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid var(--border); background:var(--white); color:var(--ink); font-weight:600; transition:all 0.2s; box-shadow:var(--shadow-sm);" onmouseover="this.style.borderColor=\'var(--primary)\'; this.style.color=\'var(--primary)\';">&#8249;</a>';
            }
            
            for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++) {
                if ($i === $page) {
                    echo '<span style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-weight:700; font-size:14px; box-shadow:0 4px 10px rgba(162,79,43,0.3);">' . $i . '</span>';
                } else {
                    $pageUrl = '?' . http_build_query(array_merge($_GET, ['page' => $i]));
                    echo '<a href="' . e($pageUrl) . '" class="pagination-link" data-page="' . $i . '" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid transparent; color:var(--ink-soft); font-weight:600; font-size:14px; transition:all 0.2s;" onmouseover="this.style.background=\'var(--bg)\'; this.style.color=\'var(--ink)\';">' . $i . '</a>';
                }
            }

            if ($page < $pages) {
                $nextUrl = '?' . http_build_query(array_merge($_GET, ['page' => $page + 1]));
                echo '<a href="' . e($nextUrl) . '" class="pagination-link" data-page="' . ($page + 1) . '" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid var(--border); background:var(--white); color:var(--ink); font-weight:600; transition:all 0.2s; box-shadow:var(--shadow-sm);" onmouseover="this.style.borderColor=\'var(--primary)\'; this.style.color=\'var(--primary)\';">&#8250;</a>';
            }
            echo '</div>';
        }
    } else {
        
        echo '<div style="text-align:center; padding:100px 20px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">';
        echo '  <div style="font-size:48px; margin-bottom:16px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V5a2.5 2.5 0 0 1 2.5-2.5H20M12 6v6m0 0l-2-2m2 2l2-2"/></svg></div>';
        echo '  <h3 style="font-size:24px; font-weight:800; margin-bottom:12px; color:var(--ink); letter-spacing:-0.02em;">Aucune donnée trouvée</h3>';
        echo '  <p style="color:var(--ink-soft); line-height:1.6; max-width:400px; margin:0 auto 24px;">La requête spécifiée n\'a renvoyé aucun résultat. Veuillez modifier vos paramètres.</p>';
        echo '  <a href="catalogue.php" class="btn-saas" style="padding:12px 24px;">Remise à zéro</a>';
        echo '</div>';
    }
    exit;
}

require_once 'views/catalogue.php';

