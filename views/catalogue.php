<?php require_once 'includes/header.php'; ?>

<style>

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
.skeleton-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.skeleton-img {
    width: 100%;
    aspect-ratio: 1/1.3;
    border-radius: var(--radius-md);
    background: linear-gradient(90deg, var(--bg) 25%, var(--bg2) 50%, var(--bg) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite linear;
}
.skeleton-text {
    height: 12px;
    border-radius: 4px;
    background: linear-gradient(90deg, var(--bg) 25%, var(--bg2) 50%, var(--bg) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite linear;
}
.skeleton-title { width: 80%; height: 16px; }
.skeleton-author { width: 50%; }
.skeleton-price { width: 40%; height: 18px; }


input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: linear-gradient(90deg, var(--border), var(--primary));
    border-radius: 10px;
    outline: none;
}
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--primary);
    border: 2px solid var(--white);
    cursor: pointer;
    box-shadow: var(--shadow-sm);
    transition: transform 0.1s ease;
}
input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}
</style>

<div class="catalogue-wrap" style="padding-bottom: 100px;">
    <div class="section-head" style="margin-bottom: 48px; border-bottom:1px solid var(--border); padding-bottom:32px;">
        <div>
          <span style="font-size:12px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:8px;">Bibliothèque de données</span>
          <h1 style="font-size:clamp(2rem, 4vw, 2.5rem); font-weight:800; color:var(--ink); letter-spacing:-0.03em; margin:0;">
              <?php if ($filters['promo']): ?>Filtre <span style="color:var(--primary);">Promotion</span>
              <?php elseif ($filters['q']): ?>Résultats pour <span style="color:var(--primary);">"<?= e($filters['q']) ?>"</span>
              <?php elseif ($sort === 'best'): ?>Classement <span style="color:var(--primary);">Best-Sellers</span>
              <?php elseif ($sort === 'new'): ?>Derniers <span style="color:var(--primary);">Ajouts</span>
              <?php elseif ($currentCat): ?>
                  <?= e($currentCat['icon']) ?> <span style="color:var(--primary);"><?= e($currentCat['name']) ?></span>
              <?php else: ?>Catalogue <span style="color:var(--primary);">Intégral</span>
              <?php endif; ?>
          </h1>
        </div>
        <span style="font-size:13px; color:var(--ink); font-weight:600; padding:6px 14px; background:var(--bg2); border:1px solid var(--border); border-radius:var(--radius-full); box-shadow:var(--shadow-sm);"><?= $total ?> entrée<?= $total > 1 ? 's' : '' ?></span>
    </div>

    <div class="catalogue-layout" style="display:grid; grid-template-columns: 260px 1fr; gap:48px; align-items:start;">
        
        <aside class="filters-sidebar" style="background:var(--white); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; box-shadow:var(--shadow-sm); position:sticky; top:100px;">
            
            <button id="mobileFilterToggle" onclick="toggleMobileFilters()" style="align-items:center; justify-content:space-between; width:100%; padding:12px 16px; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius-md); font-weight:700; color:var(--ink); font-size:14px;">
                <span>Filtrer les produits</span>
                <svg id="filterChevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div class="filters-collapsible-content" id="filtersContent">
                
                <div class="filter-group" style="margin-bottom:24px; margin-top: 16px;">
                    <h4 style="font-size:12px; font-weight:700; margin-bottom:16px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em;">Recherche rapide</h4>
                    <input type="text" placeholder="Titre, auteur, mot-clé..." value="<?= e($filters['q']) ?>" oninput="filterSearch(this.value)" style="width:100%; border:1.5px solid var(--border); background:var(--bg); padding:10px 12px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--ink); outline:none; transition:border-color 0.2s;" onfocus="this.style.borderColor='var(--ink)'" onblur="this.style.borderColor='var(--border)'">
                </div>

                
                <div class="filter-group" style="margin-bottom:24px; padding-top:24px; border-top:1px solid var(--border);">
                    <h4 style="font-size:12px; font-weight:700; margin-bottom:16px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em;">Genres</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <?php foreach ($cats as $c): ?>
                        <label style="display:flex; align-items:center; cursor:pointer; font-size:14px; font-weight:500; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">
                            <input type="checkbox" class="custom-checkbox cat-checkbox" data-slug="<?= e($c['slug']) ?>" onchange="filterCat('<?= e($c['slug']) ?>', this.checked)" <?= ($filters['cat'] === $c['slug']) ? 'checked' : '' ?> style="margin-right:12px;">
                            <?= e($c['name']) ?>
                            <span style="margin-left:auto; font-size:11px; background:var(--bg); padding:2px 6px; border:1px solid var(--border); border-radius:var(--radius-full); color:var(--muted); font-weight:600;"><?= $catCounts[$c['id']] ?? 0 ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                
                <div class="filter-group" style="margin-bottom:24px; padding-top:24px; border-top:1px solid var(--border);">
                    <h4 style="font-size:12px; font-weight:700; margin-bottom:16px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em;">Budget Max</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <input type="range" min="0" max="250" step="10" value="<?= $filters['max_price'] ?: 250 ?>" 
                               oninput="filterPrice(this.value)"
                               style="width:100%; accent-color:var(--ink); cursor:pointer;">
                        <span id="priceVal" style="font-size:14px; font-weight:700; color:var(--ink);"><?= ($filters['max_price'] ?: 250) ?> MAD</span>
                    </div>
                </div>

                
                <div class="filter-group" style="margin-bottom:24px; padding-top:24px; border-top:1px solid var(--border);">
                    <h4 style="font-size:12px; font-weight:700; margin-bottom:16px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em;">Vues rapides</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <label style="display:flex; align-items:center; cursor:pointer; font-size:14px; font-weight:500; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">
                            <input type="checkbox" class="custom-checkbox sort-checkbox" data-sort="best" onchange="filterSort(this.checked, 'best')" <?= $sort === 'best' ? 'checked' : '' ?> style="margin-right:12px;">
                            Best-Sellers
                        </label>
                        <label style="display:flex; align-items:center; cursor:pointer; font-size:14px; font-weight:500; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">
                            <input type="checkbox" class="custom-checkbox sort-checkbox" data-sort="new" onchange="filterSort(this.checked, 'new')" <?= $sort === 'new' ? 'checked' : '' ?> style="margin-right:12px;">
                            Nouveautés
                        </label>
                        <label style="display:flex; align-items:center; cursor:pointer; font-size:14px; font-weight:500; color:var(--ink-soft); transition:color 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">
                            <input type="checkbox" class="custom-checkbox promo-checkbox" onchange="filterPromo(this.checked)" <?= $filters['promo'] ? 'checked' : '' ?> style="margin-right:12px;">
                            En promotion
                        </label>
                    </div>
                </div>

                <a href="catalogue.php" style="display:flex; align-items:center; justify-content:center; gap:8px; font-size:13px; font-weight:600; color:var(--ink); background:var(--bg); border:1px solid var(--border); padding:10px; border-radius:var(--radius-full); transition:all 0.2s; text-decoration:none;" onmouseover="this.style.background='var(--white)'; this.style.borderColor='var(--ink)';" onmouseout="this.style.background='var(--bg)'; this.style.borderColor='var(--border)';">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    Réinitialiser
                </a>
            </div>
        </aside>

        
        <div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px; padding:16px; background:var(--white); border:1px solid var(--border); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm);">
                <div style="font-size:13px; font-weight:500; color:var(--muted);">
                    Affichage <strong id="displayRange">1–<?= min($perPage, $total) ?></strong> sur <strong id="displayTotal"><?= $total ?></strong> résultats
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <label style="font-size:12px; font-weight:600; color:var(--ink-soft); text-transform:uppercase; letter-spacing:0.05em;">Trier par</label>
                    <select id="sortBy" style="border:none; background:var(--bg); padding:6px 12px; border-radius:var(--radius-full); font-size:13px; font-weight:600; color:var(--ink); outline:none; cursor:pointer;" onchange="applyFilters()">
                        <option value="" <?= !$sort ? 'selected' : '' ?>>Pertinence</option>
                        <option value="best" <?= $sort === 'best' ? 'selected' : '' ?>>Best-Sellers</option>
                        <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Nouveautés</option>
                        <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Mieux notés</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Prix croissant</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                    </select>
                </div>
            </div>

            
            <div id="activeFiltersContainer" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;"></div>

            <div id="productsGridContainer" style="transition: opacity 0.2s ease;">
                <?php if ($products): ?>
                <div class="catalogue-grid">
                    <?php foreach ($products as $p): include 'includes/product_card.php'; endforeach; ?>
                </div>

                
                <?php if ($pages > 1): ?>
                <div style="display:flex; justify-content:center; gap:8px; margin-top:64px;">
                    <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="pagination-link" data-page="<?= $page - 1 ?>" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid var(--border); background:var(--white); color:var(--ink); font-weight:600; transition:all 0.2s; box-shadow:var(--shadow-sm);" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';">&laquo;</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                        <?php if($i === $page): ?>
                            <span style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); background:var(--primary); color:#fff; font-weight:700; font-size:14px; box-shadow:0 4px 10px rgba(162,79,43,0.3);"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="pagination-link" data-page="<?= $i ?>" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid transparent; color:var(--ink-soft); font-weight:600; font-size:14px; transition:all 0.2s;" onmouseover="this.style.background='var(--bg)'; this.style.color='var(--ink)';"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="pagination-link" data-page="<?= $page + 1 ?>" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-full); border:1px solid var(--border); background:var(--white); color:var(--ink); font-weight:600; transition:all 0.2s; box-shadow:var(--shadow-sm);" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';">&raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div style="text-align:center; padding:100px 20px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
                    <div style="font-size:48px; margin-bottom:16px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20M4 19.5V5a2.5 2.5 0 0 1 2.5-2.5H20M12 6v6m0 0l-2-2m2 2l2-2"/></svg>
                    </div>
                    <h3 style="font-size:24px; font-weight:800; margin-bottom:12px; color:var(--ink); letter-spacing:-0.02em;">Aucune donnée trouvée</h3>
                    <p style="color:var(--ink-soft); line-height:1.6; max-width:400px; margin:0 auto 24px;">La requête spécifiée n'a renvoyé aucun résultat. Veuillez modifier vos paramètres.</p>
                    <a href="catalogue.php" class="btn-saas" style="padding:12px 24px;">Remise à zéro</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMobileFilters() {
    const content = document.getElementById('filtersContent');
    const chevron = document.getElementById('filterChevron');
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
    }
}

let currentFilters = {
    cat: '<?= e($filters['cat']) ?>',
    q: '<?= e($filters['q']) ?>',
    promo: '<?= $filters['promo'] ? "1" : "" ?>',
    max_price: '<?= e($filters['max_price']) ?>',
    sort: '<?= e($sort) ?>',
    page: '<?= $page ?>'
};

function filterCat(slug, isChecked) {
    if (isChecked) {
        currentFilters.cat = slug;
        document.querySelectorAll('.cat-checkbox').forEach(cb => {
            if (cb.dataset.slug !== slug) cb.checked = false;
        });
    } else {
        if (currentFilters.cat === slug) currentFilters.cat = '';
    }
    currentFilters.page = 1;
    applyFilters();
}

function filterPromo(isChecked) {
    currentFilters.promo = isChecked ? '1' : '';
    currentFilters.page = 1;
    applyFilters();
}

function filterSort(isChecked, sortVal) {
    if (isChecked) {
        currentFilters.sort = sortVal;
        document.querySelectorAll('.sort-checkbox').forEach(cb => {
            if (cb.dataset.sort !== sortVal) cb.checked = false;
        });
    } else {
        if (currentFilters.sort === sortVal) currentFilters.sort = '';
    }
    currentFilters.page = 1;
    applyFilters();
}

let searchTimeout;
function filterSearch(val) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentFilters.q = val.trim();
        currentFilters.page = 1;
        applyFilters();
    }, 300);
}

function filterPrice(val) {
    document.getElementById('priceVal').innerText = val + ' MAD';
    currentFilters.max_price = val;
    currentFilters.page = 1;
    applyFilters();
}

function updateActiveFiltersBadges() {
    const container = document.getElementById('activeFiltersContainer');
    if (!container) return;
    container.innerHTML = '';
    
    if (currentFilters.q) {
        createBadge(`Recherche: "${currentFilters.q}"`, () => {
            currentFilters.q = '';
            const searchInput = document.querySelector('.filter-group input[type="text"]');
            if (searchInput) searchInput.value = '';
            applyFilters();
        });
    }
    if (currentFilters.cat) {
        const cb = document.querySelector(`.cat-checkbox[data-slug="${currentFilters.cat}"]`);
        const labelText = cb ? cb.closest('label').textContent.trim().split('\n')[0] : currentFilters.cat;
        createBadge(`Genre: ${labelText}`, () => {
            currentFilters.cat = '';
            if (cb) cb.checked = false;
            applyFilters();
        });
    }
    if (currentFilters.max_price && currentFilters.max_price !== '250') {
        createBadge(`Budget: Max ${currentFilters.max_price} MAD`, () => {
            currentFilters.max_price = '250';
            const range = document.querySelector('.filter-group input[type="range"]');
            if (range) range.value = '250';
            const priceVal = document.getElementById('priceVal');
            if (priceVal) priceVal.innerText = '250 MAD';
            applyFilters();
        });
    }
    if (currentFilters.promo) {
        createBadge('En Promotion', () => {
            currentFilters.promo = '';
            const promoCb = document.querySelector('.promo-checkbox');
            if (promoCb) promoCb.checked = false;
            applyFilters();
        });
    }
    
    function createBadge(text, onRemove) {
        const badge = document.createElement('span');
        badge.style.display = 'inline-flex';
        badge.style.alignItems = 'center';
        badge.style.gap = '6px';
        badge.style.background = 'var(--bg2)';
        badge.style.border = '1px solid var(--border)';
        badge.style.color = 'var(--ink)';
        badge.style.fontSize = '12px';
        badge.style.fontWeight = '700';
        badge.style.padding = '5px 12px';
        badge.style.borderRadius = '20px';
        badge.style.boxShadow = 'var(--shadow-sm)';
        
        badge.innerHTML = `${text} <span style="font-size:14px; font-weight:800; color:var(--muted); cursor:pointer; display:inline-flex; align-items:center; transition:color 0.15s; margin-left:4px;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--muted)'">&times;</span>`;
        badge.querySelector('span').addEventListener('click', onRemove);
        container.appendChild(badge);
    }
}

function applyFilters() {
    const grid = document.getElementById('productsGridContainer');
    if (grid) {
        let skeletonHtml = '<div class="catalogue-grid">';
        for (let i = 0; i < 8; i++) {
            skeletonHtml += `
            <div class="skeleton-card">
                <div class="skeleton-img"></div>
                <div class="skeleton-text skeleton-title"></div>
                <div class="skeleton-text skeleton-author"></div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <div class="skeleton-text skeleton-price"></div>
                    <div style="width:36px; height:36px; border-radius:50%; background:var(--bg); animation:shimmer 1.4s infinite linear;"></div>
                </div>
            </div>`;
        }
        skeletonHtml += '</div>';
        grid.innerHTML = skeletonHtml;
    }

    const sortSel = document.getElementById('sortBy');
    if (sortSel) {
        currentFilters.sort = sortSel.value;
    }

    const params = new URLSearchParams();
    for (let key in currentFilters) {
        if (currentFilters[key]) {
            params.set(key, currentFilters[key]);
        }
    }

    const newUrl = window.location.pathname + '?' + params.toString();
    history.pushState(null, '', newUrl);

    params.set('ajax', '1');

    fetch('catalogue.php?' + params.toString())
    .then(r => r.text())
    .then(html => {
        if (grid) {
            grid.innerHTML = html;
            
            // Relier les gestionnaires de pagination et mettre à jour les badges
            bindPagination();
            updateActiveFiltersBadges();

            // Faire défiler en douceur vers le haut du catalogue pour voir les nouveaux résultats
            const layout = document.querySelector('.catalogue-layout');
            if (layout) {
                const yOffset = -120; // Décilage pour compenser le header fixe
                const y = layout.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }
        }
    })
    .catch(err => {
        console.error(err);
    });
}

function bindPagination() {
    document.querySelectorAll('.pagination-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentFilters.page = this.dataset.page;
            applyFilters();
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindPagination();
    updateActiveFiltersBadges();
});
</script>


<?php require_once 'includes/footer.php'; ?>
