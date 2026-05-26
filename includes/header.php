<?php
require_once __DIR__ . '/config.php';
$cartQty = cartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$cats = getDB()->query("SELECT * FROM categories ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
  <meta name="description"
    content="<?= isset($pageDesc) ? e($pageDesc) : 'MangaShop — Impression de mangas a la demande au Maroc.' ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/main.css?v=9.0_<?= filemtime(__DIR__.'/../assets/css/main.css') ?>">
  <script>
    (function() {
      const theme = localStorage.getItem('theme');
      if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark-mode');
      }
    })();
  </script>
</head>

<body style="padding-top: 80px;">
  
  <header style="position:fixed; top:16px; left:50%; transform:translateX(-50%); width:95%; max-width:1200px; background:var(--glass); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid var(--border); box-shadow:var(--shadow-md); border-radius:var(--radius-full); display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; padding:12px 24px; z-index:1000; transition:all 0.3s ease;" id="siteHeader">
    
    
    <a href="index.php" style="display:flex; align-items:center; gap:10px; font-weight:800; font-size:18px; color:var(--ink); letter-spacing:-0.03em; text-decoration:none;">
      <div style="width:32px; height:32px; background:var(--primary); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px;">M</div>
      MangaShop
    </a>

    
    <nav style="display:flex; align-items:center; gap:28px;">
      <a href="index.php" style="font-size:14px; font-weight:600; color:<?= $currentPage === 'index' ? 'var(--ink)' : 'var(--ink-soft)' ?>; transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='<?= $currentPage === 'index' ? 'var(--ink)' : 'var(--ink-soft)' ?>'">Accueil</a>
      <a href="catalogue.php" style="font-size:14px; font-weight:600; color:<?= $currentPage === 'catalogue' ? 'var(--ink)' : 'var(--ink-soft)' ?>; transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='<?= $currentPage === 'catalogue' ? 'var(--ink)' : 'var(--ink-soft)' ?>'">Catalogue</a>
      <a href="bundles.php" style="font-size:14px; font-weight:600; color:<?= $currentPage === 'bundles' ? 'var(--ink)' : 'var(--ink-soft)' ?>; transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='<?= $currentPage === 'bundles' ? 'var(--ink)' : 'var(--ink-soft)' ?>'">Bundles</a>
      <a href="imprimer.php" style="font-size:14px; font-weight:600; color:<?= $currentPage === 'imprimer' ? 'var(--ink)' : 'var(--ink-soft)' ?>; transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='<?= $currentPage === 'imprimer' ? 'var(--ink)' : 'var(--ink-soft)' ?>'">Impression</a>
      <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
        <a href="admin/index.php" style="font-size:14px; font-weight:700; color:var(--primary); transition:all 0.2s; border-left:1px solid var(--border); padding-left:20px; display:flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
      <?php elseif (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur'): ?>
        <a href="account.php?tab=deliveries" style="font-size:14px; font-weight:700; color:var(--primary); transition:all 0.2s; border-left:1px solid var(--border); padding-left:20px; display:flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12H3l9-9 9 9h-2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
          Mes Livraisons
        </a>
      <?php endif; ?>
    </nav>

    
    <div style="display:flex; align-items:center; gap:16px;">
      
      <form action="search.php" method="GET" style="position:relative; width:200px;" id="headerSearchForm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none;"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.35-4.35" /></svg>
        <input type="text" name="q" id="headerSearchInput" placeholder="Rechercher..." value="<?= isset($_GET['q']) ? e($_GET['q']) : '' ?>" autocomplete="off"
          style="width:100%; border:none; background:rgba(0,0,0,0.04); padding:8px 12px 8px 34px; border-radius:var(--radius-full); font-size:13px; font-weight:500; font-family:'Inter', sans-serif; color:var(--ink); outline:none; transition:background 0.2s;">
        
        
        <div id="headerSearchSuggestions" style="position:absolute; top:calc(100% + 8px); right:0; width:320px; background:var(--white); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow-float); display:none; flex-direction:column; overflow:hidden; z-index:9999;"></div>
      </form>

      <div style="width:1px; height:16px; background:var(--border);" class="header-divider"></div>

      
      <a href="account.php" class="header-account" style="color:var(--ink); padding:6px; transition:color 0.2s; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--ink)'" title="Mon compte">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
      </a>

      
      <button onclick="toggleDarkMode()" id="darkModeToggle" class="header-dark-toggle" style="color:var(--ink); padding:6px; transition:color 0.2s; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--ink)'" title="Basculer le thème">
        <svg class="sun-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        <svg class="moon-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
      </button>

      
      <button onclick="toggleCart()" style="display:flex; align-items:center; gap:8px; padding:8px 16px; background:var(--ink); color:#fff; border-radius:var(--radius-full); font-size:13px; font-weight:700; transition:all 0.2s; box-shadow:var(--shadow-sm); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='var(--shadow-md)'; this.style.background='var(--primary)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'; this.style.background='var(--ink)';">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <span id="cartBadge"><?= $cartQty ?></span>
      </button>

      
      <button onclick="toggleMobileMenu()" id="mobileMenuToggle" style="display:none; color:var(--ink); padding:6px; align-items:center; justify-content:center;" title="Menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
    </div>

    
    <div id="mobileMenuLinks" style="display:none; width:100%; flex-direction:column; gap:16px; padding:16px 8px 8px; border-top:1px solid var(--border); margin-top:12px;">
      <a href="index.php" style="font-size:15px; font-weight:700; color:var(--ink);">Accueil</a>
      <a href="catalogue.php" style="font-size:15px; font-weight:700; color:var(--ink);">Catalogue</a>
      <a href="bundles.php" style="font-size:15px; font-weight:700; color:var(--ink);">Bundles</a>
      <a href="imprimer.php" style="font-size:15px; font-weight:700; color:var(--ink);">Impression</a>
      <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
        <a href="admin/index.php" style="font-size:15px; font-weight:700; color:var(--primary);">Dashboard</a>
      <?php elseif (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur'): ?>
        <a href="account.php?tab=deliveries" style="font-size:15px; font-weight:700; color:var(--primary);">Mes Livraisons</a>
      <?php endif; ?>
      
      <div style="display:flex; flex-direction:column; gap:12px; margin-top:4px; padding-top:12px; border-top:1px dashed var(--border);">
        <a href="account.php" style="font-size:15px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
          Mon Compte
        </a>
        <button onclick="toggleDarkMode()" style="font-size:15px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:8px; width:100%; text-align:left;">
          <svg class="sun-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
          <svg class="moon-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
          Changer de Theme
        </button>
      </div>

      <form action="search.php" method="GET" style="position:relative; width:100%; margin-top:8px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none;"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.35-4.35" /></svg>
        <input type="text" name="q" placeholder="Rechercher..." value="<?= isset($_GET['q']) ? e($_GET['q']) : '' ?>" style="width:100%; border:none; background:rgba(0,0,0,0.04); padding:10px 12px 10px 38px; border-radius:var(--radius-md); font-size:14px; font-weight:500; color:var(--ink); outline:none;">
      </form>
    </div>
  </header>

  
  <div class="cart-overlay" id="cartOverlay"></div>
  <div class="cart-drawer" id="cartDrawer" style="box-shadow:var(--shadow-float); border-left:1px solid var(--border);">
    <div class="cart-drawer-head" style="border-bottom:1px solid var(--border); padding:24px;">
      <h3 style="font-weight:800; font-size:18px;">Panier</h3>
      <button class="cart-close" onclick="toggleCart()" style="font-size:24px; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--ink-soft)'">&times;</button>
    </div>
    
    <div id="drawerShippingMeter" style="padding: 16px 24px 0 24px;"></div>
    <div class="cart-items" id="cartItems">
      <div class="cart-empty" style="text-align:center; padding:64px 24px; color:var(--muted); font-weight:500;">Votre panier est vide</div>
    </div>
    <div class="cart-footer" style="padding:24px; border-top:1px solid var(--border); background:var(--bg);">
      <div class="cart-subtotal" style="display:flex; justify-content:space-between; margin-bottom:20px; font-weight:700; font-size:16px;"><span>Total</span><span id="cartSubtotal" style="color:var(--ink);">0.00 MAD</span></div>
      <a href="checkout.php" class="btn-saas" style="width:100%; padding:16px; margin-bottom:12px;">Passer la commande</a>
      <a href="cart.php" style="display:block; text-align:center; padding:12px; font-size:14px; font-weight:600; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">Voir le détail du panier</a>
    </div>
  </div>

  <script>
    
    const revealCallback = (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
        }
      });
    };
    const revealObserver = new IntersectionObserver(revealCallback, { threshold: 0.1 });
    window.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    });

    
    const header = document.getElementById('siteHeader');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        header.style.boxShadow = 'var(--shadow-md)';
        header.style.background = 'var(--glass)';
      } else {
        header.style.boxShadow = 'var(--shadow-sm)';
        header.style.background = 'var(--glass)';
      }
    });

    function toggleDarkMode() {
      const isDark = document.documentElement.classList.toggle('dark-mode');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }

    function toggleMobileMenu() {
      const header = document.getElementById('siteHeader');
      const links = document.getElementById('mobileMenuLinks');
      if (header.classList.contains('menu-open')) {
        header.classList.remove('menu-open');
        header.style.borderRadius = 'var(--radius-full)';
        links.style.display = 'none';
      } else {
        header.classList.add('menu-open');
        header.style.borderRadius = 'var(--radius-lg)';
        links.style.display = 'flex';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('headerSearchInput');
        const suggestionsPanel = document.getElementById('headerSearchSuggestions');
        const searchForm = document.getElementById('headerSearchForm');
        
        if (searchInput && suggestionsPanel) {
            let debounceTimeout;
            
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                const val = searchInput.value.trim();
                
                if (val.length < 2) {
                    suggestionsPanel.style.display = 'none';
                    suggestionsPanel.innerHTML = '';
                    return;
                }
                
                debounceTimeout = setTimeout(() => {
                    fetch(`actions/search_ajax.php?q=${encodeURIComponent(val)}`)
                    .then(r => r.json())
                    .then(data => {
                        suggestionsPanel.innerHTML = '';
                        if (data.length === 0) {
                            suggestionsPanel.innerHTML = '<div style="padding:16px; font-size:13px; color:var(--muted); text-align:center; font-weight:500;">Aucun manga trouvé</div>';
                            suggestionsPanel.style.display = 'flex';
                            return;
                        }
                        
                        data.forEach(p => {
                            const item = document.createElement('a');
                            item.href = `product.php?slug=${p.slug}`;
                            item.style.display = 'flex';
                            item.style.alignItems = 'center';
                            item.style.gap = '12px';
                            item.style.padding = '10px 14px';
                            item.style.textDecoration = 'none';
                            item.style.borderBottom = '1px solid var(--border)';
                            item.style.transition = 'background 0.2s';
                            
                            item.onmouseover = () => item.style.background = 'var(--bg)';
                            item.onmouseout = () => item.style.background = 'transparent';
                            
                            item.innerHTML = `
                                <img src="${p.image_url}" alt="" style="width:36px; height:48px; object-fit:cover; border-radius:4px; border:1px solid var(--border);">
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:13.5px; font-weight:700; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${p.title}</div>
                                    <div style="font-size:11px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px;">${p.author}</div>
                                </div>
                                <div style="font-size:12.5px; font-weight:800; color:var(--primary);">${p.price}</div>
                            `;
                            suggestionsPanel.appendChild(item);
                        });
                        
                        if (suggestionsPanel.lastChild) {
                            suggestionsPanel.lastChild.style.borderBottom = 'none';
                        }
                        
                        suggestionsPanel.style.display = 'flex';
                    });
                }, 250);
            });
            
            document.addEventListener('click', (e) => {
                if (!searchForm.contains(e.target)) {
                    suggestionsPanel.style.display = 'none';
                }
            });
            
            searchInput.addEventListener('focus', () => {
                if (searchInput.value.trim().length >= 2) {
                    suggestionsPanel.style.display = 'flex';
                }
            });
        }
    });
  </script>

  <main>