<?php require_once 'includes/header.php'; ?>

<div class="catalogue-wrap" style="padding-top: 40px;">
    <div class="section-head">
        <div>
            <p class="section-label">Offres Spéciales</p>
            <h1 class="section-title">Nos <span class="accent">Bundles</span> d'Exception</h1>
        </div>
        <span style="font-size:13px;color:var(--muted);font-weight:500"><?= count($bundles) ?> pack<?= count($bundles) > 1 ? 's' : '' ?> disponible<?= count($bundles) > 1 ? 's' : '' ?></span>
    </div>

    <!-- SECTEUR ONGLETS -->
    <div style="display:flex; justify-content:center; gap:16px; margin-bottom:48px; border-bottom: 2px solid var(--border); padding-bottom: 16px;">
        <button onclick="switchTab('packs')" id="tabBtnPacks" class="btn-tab active" style="padding:14px 28px; font-weight:700; font-size:14px; border-radius:var(--radius-full); transition:all 0.2s; border: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; gap: 8px;">
            Packs Curatés
        </button>
        <button onclick="switchTab('custom')" id="tabBtnCustom" class="btn-tab" style="padding:14px 28px; font-weight:700; font-size:14px; border-radius:var(--radius-full); transition:all 0.2s; border: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; gap: 8px;">
            Pack Sur-Mesure
        </button>
    </div>

    <!-- ONGLET 1 : PACKS CURATÉS -->
    <div id="tabContentPacks">
        <div class="catalogue-layout">
            <!-- SIDEBAR FILTERS -->
            <aside class="filters-sidebar" style="background:var(--white); border:1px solid var(--border); border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
                <!-- Search -->
                <div class="filter-group" style="margin-bottom:28px;">
                    <h4 style="font-family:'Inter',sans-serif; font-size:15px; font-weight:700; margin-bottom:16px; color:var(--ink); text-transform:uppercase; letter-spacing:0.5px;">Recherche</h4>
                    <form action="bundles.php" method="GET" style="position:relative;">
                        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="Chercher un pack..." style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:10px; font-size:13px; outline:none; transition:border-color 0.2s; background:var(--bg); color:var(--ink);" onfocus="this.style.borderColor='var(--ink)'" onblur="this.style.borderColor='var(--border)'">
                        <button type="submit" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Price Filter -->
                <div class="filter-group" style="margin-bottom:28px;">
                    <h4 style="font-family:'Inter',sans-serif; font-size:15px; font-weight:700; margin-bottom:16px; color:var(--ink); text-transform:uppercase; letter-spacing:0.5px;">Budget Max</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <input type="range" min="0" max="2000" step="50" value="<?= $filters['max_price'] ?: 2000 ?>" 
                               oninput="document.getElementById('priceVal').innerText = this.value + ' MAD'"
                               onchange="location.href='bundles.php?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),...{max_price:this.value}})"
                               style="width:100%; accent-color:var(--ink); cursor:pointer;">
                        <span id="priceVal" style="font-size:14px; font-weight:700; color:var(--ink);"><?= ($filters['max_price'] ?: 2000) ?> MAD</span>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="filter-group" style="margin-bottom:28px;">
                    <h4 style="font-family:'Inter',sans-serif; font-size:15px; font-weight:700; margin-bottom:16px; color:var(--ink); text-transform:uppercase; letter-spacing:0.5px;">Sélection</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <label class="filter-option" style="display:flex; align-items:center; cursor:pointer; font-size:14px; color:var(--ink-soft);">
                            <input type="checkbox" onchange="toggleBundleFilter('promo')" <?= $filters['promo'] ? 'checked' : '' ?> style="margin-right:10px; accent-color:var(--ink); width:16px; height:16px;">
                            En promotion
                        </label>
                    </div>
                </div>

                <a href="bundles.php" style="display:flex; align-items:center; justify-content:center; gap:8px; font-size:13px; font-weight:600; color:var(--ink); border:2px solid var(--border); padding:12px; border-radius:10px; margin-top:20px; transition:all 0.2s; text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    Réinitialiser
                </a>
            </aside>

            <!-- BUNDLES GRID -->
            <div>
                <!-- Sort Bar -->
                <div class="sort-bar" style="margin-bottom:32px;">
                    <div style="font-size:13px;color:var(--muted)">
                        Affiche <?= count($bundles) ?> packs exceptionnels
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <label style="font-size:12.5px;color:var(--muted)">Trier :</label>
                        <select onchange="location.href='bundles.php?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),...{sort:this.value}})" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;background:var(--white);color:var(--ink);">
                            <option value="id DESC" <?= $sort === 'id DESC' ? 'selected' : '' ?>>Plus récents</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Prix croissant</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                        </select>
                    </div>
                </div>

                <?php if ($bundles): ?>
                <div class="bundles-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:32px;">
                    <?php foreach ($bundles as $b):
                        $pct = $b['old_price'] ? discount((float)$b['old_price'],(float)$b['price']) : 0;
                        $savings = $b['old_price'] ? $b['old_price'] - $b['price'] : 0;
                        $initial = strtoupper(mb_substr($b['name'], 0, 1));
                    ?>
                    <div class="bundle-card reveal active" style="background:var(--white); border:1px solid var(--border); border-radius:16px; overflow:hidden; transition:transform 0.4s ease, box-shadow 0.4s ease; display:flex; flex-direction:column;">
                        <a href="bundle.php?slug=<?= e($b['slug']) ?>" style="display:block; position:relative; aspect-ratio:1.2/1; overflow:hidden;">
                            <?php if (!empty($b['image_url'])): ?>
                                <img src="<?= asset($b['image_url']) ?>" alt="<?= e($b['name']) ?>" style="width:100%; height:100%; object-fit:cover; transition:transform 0.5s ease;">
                            <?php else: ?>
                                <div style="width:100%; height:100%; background:var(--bg); display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-size:60px; color:rgba(0,0,0,0.1);"><?= $initial ?></div>
                            <?php endif; ?>
                            
                            <?php if ($pct): ?>
                                <div style="position:absolute; top:20px; left:20px; background:var(--red); color:#fff; font-size:12px; font-weight:800; padding:6px 12px; border-radius:4px; z-index:5;">-<?= $pct ?>%</div>
                            <?php endif; ?>
                        </a>
                        
                        <div style="padding:24px; flex-grow:1; display:flex; flex-direction:column;">
                            <?php if ($savings > 0): ?>
                                <span style="display:inline-block; font-size:11px; font-weight:800; color:var(--red); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Économisez <?= number_format($savings,0) ?> MAD</span>
                            <?php endif; ?>
                            
                            <h3 style="font-size:18px; font-weight:800; color:var(--ink); margin-bottom:8px; line-height:1.4;"><?= e($b['name']) ?></h3>
                            <p style="font-size:13.5px; color:var(--ink-soft); line-height:1.6; margin-bottom:24px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= e($b['description']) ?>
                            </p>
                            
                            <div style="margin-top:auto; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <div style="font-size:22px; font-weight:900; color:var(--ink);"><?= number_format($b['price'],2) ?> <span style="font-size:12px; font-weight:600;">MAD</span></div>
                                    <?php if ($b['old_price']): ?>
                                        <div style="font-size:13px; color:var(--muted); text-decoration:line-through;"><?= number_format($b['old_price'],2) ?> MAD</div>
                                    <?php endif; ?>
                                </div>
                                <a href="bundle.php?slug=<?= e($b['slug']) ?>" class="btn-main" style="padding:12px 24px; font-size:13px; font-weight:700; border-radius:8px; background:var(--ink); color:#fff; text-decoration:none;">Voir l'offre</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="text-align:center; padding:100px 20px;">
                    <div style="font-size:48px; margin-bottom:24px; opacity:0.2; display:flex; justify-content:center; color:var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <h3 style="font-size:20px; font-weight:800; margin-bottom:12px;">Aucun pack trouvé</h3>
                    <p style="color:var(--muted);">Essayez d'ajuster vos critères ou réinitialisez les filtres.</p>
                    <a href="bundles.php" style="display:inline-block; margin-top:24px; color:var(--ink); font-weight:700; border-bottom:2px solid var(--ink); text-decoration:none; padding-bottom:4px;">Voir tous les packs</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ONGLET 2 : CRÉATEUR DE PACK SUR-MESURE -->
    <div id="tabContentCustom" style="display:none; padding-bottom: 80px;">
        <div class="custom-builder-container">
            <!-- Left: Grid of available manga volumes -->
            <div>
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: clamp(1.4rem, 3vw, 1.8rem); font-weight: 800; color: var(--ink); margin-bottom: 8px;">Sélectionnez vos volumes</h2>
                    <p style="color:var(--ink-soft); font-size:14px; font-weight: 500;">Sélectionnez au moins 2 mangas. Plus vous en ajoutez, plus vous gagnez de cadeaux !</p>
                </div>
                
                <div class="manga-selection-grid">
                    <?php foreach ($allProducts as $p): ?>
                    <div class="manga-select-card" id="manga-card-<?= $p['id'] ?>" onclick="toggleSelectManga(<?= $p['id'] ?>, '<?= e(addslashes($p['title'])) ?>', '<?= e($p['image_url']) ?>', <?= $p['price'] ?>)" data-id="<?= $p['id'] ?>">
                        <div class="badge-selected"></div>
                        <div class="manga-select-thumb">
                            <?php if ($p['image_url']): ?>
                                <img src="<?= asset($p['image_url']) ?>" alt="" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <span><?= strtoupper(mb_substr($p['title'], 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--ink); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 36px; line-height: 1.4; margin-bottom: 4px;"><?= e($p['title']) ?></div>
                        <div style="font-size: 11px; color: var(--muted); margin-bottom: 8px; font-weight:600;"><?= e($p['author'] ?? 'MangaShop') ?></div>
                        <div style="font-size: 13.5px; font-weight: 800; color: var(--primary);"><?= number_format($p['price'], 2) ?> MAD</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Right: Dynamic checkout sticky card -->
            <div class="order-summary" style="position: sticky; top: 120px;">
                <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; color: var(--ink);">
                    Pack Sur-Mesure
                </h3>
                <p style="font-size: 12px; color: var(--ink-soft); margin-bottom: 12px; font-weight:600;">Composez et économisez en direct</p>
                
                <!-- 3D Collector Box -->
                <div class="collector-box-scene">
                    <div class="collector-box-3d" id="collectorBox3d">
                        <div class="collector-box-face collector-box-front">
                            <span>Coffret Collector</span>
                            <span style="font-size:16px; margin-top:6px;" id="collectorBoxQty">0 Volume</span>
                        </div>
                        <div class="collector-box-face collector-box-back"></div>
                        <div class="collector-box-face collector-box-left"></div>
                        <div class="collector-box-face collector-box-right"></div>
                        <div class="collector-box-face collector-box-top"></div>
                    </div>
                </div>
                
                <!-- gift progress tracker -->
                <div style="margin-bottom:28px;">
                    <div style="display:flex; justify-content:space-between; font-size:11.5px; font-weight:700; color:var(--ink); margin-bottom:12px; text-transform:uppercase; letter-spacing:0.04em;">
                        <span>Objectif cadeaux</span>
                        <span id="giftsCountBadge" style="color:var(--primary);">0 manga sélectionné</span>
                    </div>
                    <div class="gift-progress-wrapper" style="margin-bottom: 56px;">
                        <div class="gift-progress-track">
                            <div id="giftProgressFill" class="gift-progress-fill"></div>
                        </div>
                        <div class="gift-nodes">
                            <div class="gift-node" id="node-3" style="position:absolute; left:42%;">
                                <div class="gift-node-label">3 tomes<br><strong>1 Offert</strong></div>
                            </div>
                            <div class="gift-node" id="node-5" style="position:absolute; left:70%;">
                                <div class="gift-node-label">5 tomes<br><strong>2 Offerts</strong></div>
                            </div>
                            <div class="gift-node" id="node-7" style="position:absolute; left:97%;">
                                <div class="gift-node-label">7+ tomes<br><strong>4 Offerts</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- selected items count list -->
                <div style="border-top:1px solid var(--border); padding-top:20px; margin-bottom:24px;">
                    <div style="font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;">Volumes sélectionnés (<span id="selectedCount">0</span>)</div>
                    <div id="selectedVolumesList" style="display:flex; flex-direction:column; gap:8px; max-height:180px; overflow-y:auto; padding-right:4px;">
                        <div style="font-size:12.5px; color:var(--muted); font-style:italic; text-align:center; padding:16px 0;">Aucun volume sélectionné. Cliquez sur les mangas pour composer votre pack.</div>
                    </div>
                </div>
                
                <!-- pricing table -->
                <div style="border-top:1px solid var(--border); padding-top:20px; margin-bottom:24px; font-size:14px;">
                    <div class="summary-row">
                        <span>Sous-total brut</span>
                        <span id="customRawTotal" style="font-weight:600; color:var(--ink);">0.00 MAD</span>
                    </div>
                    <div class="summary-row" id="customShippingRow">
                        <span>Livraison</span>
                        <span id="customShippingAmt" style="font-weight:600;">29.00 MAD</span>
                    </div>
                    <div class="summary-row" id="customPromoRow" style="color: var(--green); display:none; font-weight: 700;">
                        <span id="customPromoLabel">Manga offert</span>
                        <span>Gratuit</span>
                    </div>
                    <hr class="summary-sep">
                    <div class="summary-row summary-total">
                        <span>Total Estimé</span>
                        <span id="customNetTotal" style="color:var(--red); font-weight: 900; font-size: 20px;">0.00 MAD</span>
                    </div>
                </div>
                
                <button onclick="addCustomPackToCart()" id="btnCustomCheckout" class="btn-checkout" style="width:100%; border:none; border-radius:10px; padding:16px; cursor:not-allowed; opacity:0.5; font-size:13.5px;" disabled>
                    Ajouter mon Pack au Panier
                </button>
                <div style="text-align:center; margin-top:12px; font-size:11.5px; color:var(--ink-soft); font-weight:600;">
                    Déduction définitive de vos mangas offerts dans le panier !
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STYLE DÉDIÉ CRÉATEUR -->
<style>
/* 3D COLLECTOR BOX STYLES */
@keyframes boxShake {
    0%, 100% { transform: rotateX(25deg) rotateY(-30deg); }
    25% { transform: rotateX(28deg) rotateY(-26deg) scale(1.03); }
    50% { transform: rotateX(22deg) rotateY(-34deg) scale(0.97); }
    75% { transform: rotateX(26deg) rotateY(-28deg); }
}
.box-shake {
    animation: boxShake 0.5s ease-in-out;
}
.collector-box-scene {
    perspective: 800px;
    width: 160px;
    height: 120px;
    margin: 16px auto;
}
.collector-box-3d {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transform: rotateX(25deg) rotateY(-30deg);
    transition: transform 0.5s ease;
}
.collector-box-face {
    position: absolute;
    border: 1px solid rgba(255,255,255,0.1);
    box-sizing: border-box;
}
.collector-box-front {
    width: 160px;
    height: 120px;
    background: linear-gradient(135deg, #1c1412, #a24f2b);
    transform: translateZ(40px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.collector-box-back {
    width: 160px;
    height: 120px;
    background: #1c1412;
    transform: rotateY(180deg) translateZ(40px);
}
.collector-box-left {
    width: 80px;
    height: 120px;
    background: #7d3419;
    transform: rotateY(-90deg) translateZ(40px);
    left: 40px; /* (160/2 - 80/2) */
}
.collector-box-right {
    width: 80px;
    height: 120px;
    background: #b25832;
    transform: rotateY(90deg) translateZ(40px);
    left: 40px;
}
.collector-box-top {
    width: 160px;
    height: 80px;
    background: linear-gradient(135deg, #b25832, #a24f2b);
    transform: rotateX(90deg) translateZ(40px);
    top: 20px; /* (120/2 - 80/2) */
}

.btn-tab {
    background: var(--white);
    color: var(--ink-soft);
    border: 1px solid var(--border) !important;
}
.btn-tab:hover {
    color: var(--ink);
    background: var(--bg2);
}
.btn-tab.active {
    background: var(--ink) !important;
    color: var(--white) !important;
    border-color: var(--ink) !important;
}

.custom-builder-container {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 48px;
    align-items: start;
}
@media (max-width: 1024px) {
    .custom-builder-container {
        grid-template-columns: 1fr;
        gap: 32px;
    }
}
.manga-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
}
.manga-select-card {
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    position: relative;
    user-select: none;
}
.manga-select-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}
.manga-select-card.selected {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary);
    background: var(--gold-light);
}
.manga-select-card .badge-selected {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 20px;
    height: 20px;
    background: var(--primary);
    color: #fff;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    box-shadow: var(--shadow-sm);
    z-index: 5;
}
.manga-select-card.selected .badge-selected {
    display: flex;
}
.manga-select-thumb {
    aspect-ratio: 1/1.3;
    background: var(--bg);
    border-radius: var(--radius-sm);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    font-weight: 800;
    color: var(--muted);
    border: 1px solid var(--border);
}
.manga-select-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.manga-select-card:hover .manga-select-thumb img {
    transform: scale(1.04);
}

/* gift progress and nodes */
.gift-progress-wrapper {
    margin: 20px 0;
    position: relative;
}
.gift-progress-track {
    height: 8px;
    background: var(--bg2);
    border-radius: var(--radius-full);
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border);
}
.gift-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--primary), var(--gold));
    border-radius: var(--radius-full);
    transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.gift-nodes {
    display: flex;
    justify-content: space-between;
    margin-top: -14px;
    position: relative;
}
.gift-node {
    width: 20px;
    height: 20px;
    background: var(--white);
    border: 3px solid var(--border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: all 0.3s;
}
.gift-node.active {
    background: var(--gold);
    border-color: var(--primary);
    box-shadow: 0 0 10px rgba(162, 79, 43, 0.4);
}
.gift-node-label {
    position: absolute;
    top: 24px;
    font-size: 9px;
    font-weight: 700;
    color: var(--ink-soft);
    text-align: center;
    white-space: nowrap;
    transform: translateX(-40%);
    line-height: 1.3;
}
.gift-node.active .gift-node-label {
    color: var(--primary);
}
</style>

<script>
function switchTab(tab) {
    if (tab === 'packs') {
        document.getElementById('tabBtnPacks').classList.add('active');
        document.getElementById('tabBtnCustom').classList.remove('active');
        document.getElementById('tabContentPacks').style.display = 'block';
        document.getElementById('tabContentCustom').style.display = 'none';
    } else {
        document.getElementById('tabBtnPacks').classList.remove('active');
        document.getElementById('tabBtnCustom').classList.add('active');
        document.getElementById('tabContentPacks').style.display = 'none';
        document.getElementById('tabContentCustom').style.display = 'block';
    }
}

function toggleBundleFilter(key) {
    const url = new URL(window.location);
    if (url.searchParams.has(key)) url.searchParams.delete(key);
    else url.searchParams.set(key, '1');
    window.location = url.toString();
}

let selectedMangas = [];

function toggleSelectManga(id, title, img, price) {
    const idx = selectedMangas.findIndex(item => item.id === id);
    const card = document.getElementById('manga-card-' + id);
    
    if (idx === -1) {
        selectedMangas.push({id, title, img, price});
        if (card) card.classList.add('selected');
    } else {
        selectedMangas.splice(idx, 1);
        if (card) card.classList.remove('selected');
    }
    
    updateCustomPackState();
}

function updateCustomPackState() {
    const listEl = document.getElementById('selectedVolumesList');
    const cnt = selectedMangas.length;
    
    // Mettre à jour la boîte collector 3D
    const boxQty = document.getElementById('collectorBoxQty');
    if (boxQty) {
        boxQty.innerText = cnt === 0 ? '0 Volume' : (cnt === 1 ? '1 Volume' : cnt + ' Volumes');
    }
    const box3d = document.getElementById('collectorBox3d');
    if (box3d) {
        box3d.classList.remove('box-shake');
        void box3d.offsetWidth; // Déclencher le reflow
        box3d.classList.add('box-shake');
    }
    
    document.getElementById('selectedCount').innerText = cnt;
    const badgeText = cnt === 0 ? 'Aucun manga' : (cnt === 1 ? '1 manga sélectionné' : `${cnt} mangas sélectionnés`);
    document.getElementById('giftsCountBadge').innerText = badgeText;
    
    if (cnt === 0) {
        listEl.innerHTML = `<div style="font-size:12.5px; color:var(--muted); font-style:italic; text-align:center; padding:16px 0;">Aucun volume sélectionné. Cliquez sur les mangas pour composer votre pack.</div>`;
    } else {
        let html = '';
        selectedMangas.forEach(item => {
            const initial = item.title ? item.title[0].toUpperCase() : '?';
            html += `
            <div style="display:flex; align-items:center; gap:10px; padding:8px; border:1px solid var(--border); border-radius:8px; background:var(--bg);">
                <div style="width:30px; height:40px; border-radius:4px; overflow:hidden; background:var(--bg2); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-weight:bold; font-size:11px; position:relative;">
                    ${item.img ? `<img src="${item.img}" style="width:100%; height:100%; object-fit:cover;">` : `<span>${initial}</span>`}
                </div>
                <div style="flex:1; font-size:12.5px; font-weight:600; color:var(--ink); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${item.title}</div>
                <div style="font-size:12px; font-weight:700; color:var(--primary);">${item.price.toFixed(2)} MAD</div>
                <button onclick="event.stopPropagation(); toggleSelectManga(${item.id}, '', '', 0)" style="font-size:16px; color:var(--muted); cursor:pointer; font-weight:bold; border:none; background:none; padding: 0 4px;">&times;</button>
            </div>
            `;
        });
        listEl.innerHTML = html;
    }
    
    let subtotal = selectedMangas.reduce((sum, item) => sum + item.price, 0);
    let shipping = (cnt >= 2) ? 0 : 29.00;
    let freeBooks = cnt >= 7 ? 4 : (cnt >= 5 ? 2 : (cnt >= 3 ? 1 : 0));
    
    document.getElementById('customRawTotal').innerText = subtotal.toFixed(2) + ' MAD';
    
    const shipAmt = document.getElementById('customShippingAmt');
    if (cnt >= 2) {
        shipAmt.innerText = 'Gratuite';
        shipAmt.style.color = 'var(--green)';
    } else {
        shipAmt.innerText = '29.00 MAD';
        shipAmt.style.color = 'inherit';
    }
    
    const promoRow = document.getElementById('customPromoRow');
    if (freeBooks > 0) {
        promoRow.style.display = 'flex';
        document.getElementById('customPromoLabel').innerText = `${freeBooks} manga${freeBooks > 1 ? 's' : ''} OFFERT${freeBooks > 1 ? 's' : ''} !`;
    } else {
        promoRow.style.display = 'none';
    }
    
    let netTotal = subtotal + shipping;
    if (freeBooks > 0 && cnt > 0) {
        let sorted = [...selectedMangas].sort((a, b) => a.price - b.price);
        let freeAmt = 0;
        for (let i = 0; i < Math.min(freeBooks, sorted.length); i++) {
            freeAmt += sorted[i].price;
        }
        netTotal = Math.max(0, subtotal - freeAmt + shipping);
    }
    document.getElementById('customNetTotal').innerText = netTotal.toFixed(2) + ' MAD';
    
    const checkoutBtn = document.getElementById('btnCustomCheckout');
    if (cnt >= 2) {
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
        checkoutBtn.disabled = false;
    } else {
        checkoutBtn.style.opacity = '0.5';
        checkoutBtn.style.cursor = 'not-allowed';
        checkoutBtn.disabled = true;
    }
    
    let percent = 0;
    if (cnt >= 7) percent = 100;
    else if (cnt >= 5) percent = 70 + ((cnt - 5) / 2) * 27;
    else if (cnt >= 3) percent = 42 + ((cnt - 3) / 2) * 28;
    else if (cnt > 0) percent = (cnt / 3) * 42;
    
    document.getElementById('giftProgressFill').style.width = percent + '%';
    
    document.getElementById('node-3').className = 'gift-node' + (cnt >= 3 ? ' active' : '');
    document.getElementById('node-5').className = 'gift-node' + (cnt >= 5 ? ' active' : '');
    document.getElementById('node-7').className = 'gift-node' + (cnt >= 7 ? ' active' : '');
}

function addCustomPackToCart() {
    if (selectedMangas.length < 2) return;
    
    const pids = selectedMangas.map(item => item.id).join(',');
    const checkoutBtn = document.getElementById('btnCustomCheckout');
    checkoutBtn.innerText = 'Création du pack...';
    checkoutBtn.disabled = true;
    
    fetch('actions/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=add&product_ids=${pids}&qty=1`
    })
    .then(r => r.json())
    .then(data => {
        checkoutBtn.innerText = 'Ajouter mon Pack au Panier';
        checkoutBtn.disabled = false;
        
        if (data.success) {
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cartCount;
            
            selectedMangas = [];
            document.querySelectorAll('.manga-select-card').forEach(card => card.classList.remove('selected'));
            updateCustomPackState();
            
            updateCartDrawer();
            openCartDraw();
            showToast('Pack Sur-Mesure ajouté au panier !', 'success');
        } else {
            showToast('Erreur lors de l\'ajout', 'error');
        }
    })
    .catch(() => {
        checkoutBtn.innerText = 'Ajouter mon Pack au Panier';
        checkoutBtn.disabled = false;
        showToast('Erreur réseau', 'error');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>

