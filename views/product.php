<?php require_once 'includes/header.php'; ?>

<style>

.booklet-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.9);
    backdrop-filter: blur(10px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    overflow-y: auto;
    padding: 12px;
    box-sizing: border-box;
}
.booklet-modal.open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}
.booklet-container {
    width: 100%;
    max-width: 500px;
    max-height: calc(100vh - 24px);
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-float);
    position: relative;
    display: flex;
    flex-direction: column;
    animation: bookletScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    box-sizing: border-box;
}
@keyframes bookletScale {
    from { transform: scale(0.92); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.booklet-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.booklet-body {
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--bg);
    position: relative;
    flex: 1;
    overflow-y: auto;
    min-height: 320px;
}
.booklet-page {
    display: none;
    width: 100%;
    max-width: 320px;
    aspect-ratio: 1/1.4;
    background: var(--bg2);
    border: 1px solid var(--border);
    box-shadow: 10px 10px 30px rgba(0,0,0,0.1);
    border-radius: 4px;
    padding: 20px;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
    animation: bookletFade 0.4s ease forwards;
}
@keyframes bookletFade {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}
.booklet-page.active {
    display: flex;
    flex-direction: column;
}
.booklet-page::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 10px;
    background: linear-gradient(90deg, rgba(0,0,0,0.06), transparent);
}
.booklet-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    flex-shrink: 0;
}
.btn-booklet-nav {
    padding: 10px 20px;
    border-radius: 100px;
    font-weight: 700;
    font-size: 13px;
    border: 1px solid var(--border);
    background: var(--white);
    color: var(--ink);
    cursor: pointer;
    transition: all 0.2s;
}
.btn-booklet-nav:hover {
    background: var(--bg);
    border-color: var(--ink);
}
.btn-booklet-custom {
    margin-top: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 24px;
    border: 2px dashed var(--primary);
    border-radius: var(--radius-full);
    font-size: 14px;
    font-weight: 700;
    background: rgba(162, 79, 43, 0.03);
    color: var(--primary);
    width: 100%;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-booklet-custom:hover {
    background: rgba(162, 79, 43, 0.08);
}
.dark-mode .btn-booklet-custom {
    background: rgba(212, 175, 55, 0.05);
}
.dark-mode .btn-booklet-custom:hover {
    background: rgba(212, 175, 55, 0.12);
}
@keyframes stripeShimmer {
    0% { left: -100%; }
    100% { left: 200%; }
}
</style>

<div class="product-page" style="padding-bottom:100px;">
    <nav class="breadcrumb" style="padding: 24px 0; font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em; display:flex; gap:12px; max-width:1200px; margin:0 auto;">
        <a href="index.php" style="transition:color 0.2s; color:var(--ink-soft);" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">Accueil</a><span>/</span>
        <a href="catalogue.php" style="transition:color 0.2s; color:var(--ink-soft);" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'">Catalogue</a><span>/</span>
        <a href="catalogue.php?cat=<?= e($p['cat_slug']) ?>" style="transition:color 0.2s; color:var(--ink-soft);" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--ink-soft)'"><?= e($p['cat_name']) ?></a><span>/</span>
        <span style="color:var(--ink);"><?= e($p['title']) ?></span>
    </nav>

    <div class="product-layout">
        <div class="gallery-col">
            <div class="gallery-main" style="position:relative; overflow:hidden; border-radius:var(--radius-lg); background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm); aspect-ratio:1/1.3; cursor:zoom-in;">
                <?php if (!empty($p['image_url'])): ?>
                    <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['title']) ?>" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:contain; padding:24px; transition:transform 0.1s ease; transform-origin:center;">
                <?php else: ?>
                    <div class="gallery-main-placeholder" style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; opacity:0.1;">
                        <div style="font-size:140px; font-weight:900;"><?= strtoupper(mb_substr($p['title'],0,1)) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <button onclick="toggleWishlist(<?= $p['id'] ?>,this)"
                style="margin-top:24px; display:flex; align-items:center; justify-content:center; gap:10px; padding:16px 24px; border:1px solid var(--border); border-radius:var(--radius-full); font-size:14px; font-weight:600; background:var(--white); color:var(--ink); width:100%; cursor:pointer; box-shadow:var(--shadow-sm); transition:all 0.3s;" onmouseover="this.style.background='var(--bg)'; this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.background='var(--white)'; this.style.borderColor='var(--border)'; this.style.color='var(--ink)';">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Ajouter à ma bibliothèque
            </button>
            
            <button onclick="openBookletReader()" class="btn-booklet-custom">
                Feuilleter un extrait
            </button>
        </div>

        <div class="prod-info-right">
            <div style="font-size:13px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;">Édité par <a href="catalogue.php?author=<?= urlencode($p['author']) ?>" style="color:var(--primary);text-decoration:none;"><?= e($p['author']) ?></a></div>
            <h1 style="font-size:clamp(2.2rem, 4vw, 3rem); font-weight:800; line-height:1.1; color:var(--ink); margin-bottom:24px; letter-spacing:-0.03em;"><?= e($p['title']) ?></h1>

            <?php if ($p['rating']): ?>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:32px;">
                <div class="stars-row" data-rating="<?= (float)$p['rating'] ?>" style="color:#eab308; stroke:#eab308;"></div>
                <span style="font-weight:700; font-size:15px;"><?= number_format($p['rating'],1) ?></span>
                <?php if ($p['review_count']): ?><span style="color:var(--muted); font-size:13px; font-weight:500;">(<?= number_format($p['review_count']) ?> avis vérifiés)</span><?php endif; ?>
            </div>
            <?php endif; ?>

            <div style="padding-bottom:32px; border-bottom:1px solid var(--border); margin-bottom:32px;">
                <?php if ($p['old_price']): ?><span style="display:inline-block; padding:4px 12px; background:var(--red-light); color:var(--red); font-size:11px; font-weight:800; text-transform:uppercase; border-radius:var(--radius-full); margin-bottom:12px; box-shadow:var(--shadow-sm);">Offre Limitée</span><br><?php endif; ?>
                
                <div style="display:flex; align-items:baseline; gap:16px;">
                    <span style="font-size:36px; font-weight:800; color:var(--ink); letter-spacing:-0.03em;"><?= number_format($p['price'],2) ?> MAD</span>
                    <?php if ($p['old_price']): ?>
                        <span style="font-size:18px; color:var(--muted); text-decoration:line-through; font-weight:500;"><?= number_format($p['old_price'],2) ?> MAD</span>
                    <?php endif; ?>
                </div>

                <div style="font-size:13px; color:<?= $p['stock'] > 0 ? 'var(--green)' : 'var(--red)' ?>; margin-top:16px; font-weight:600; display:flex; align-items:center; gap:8px; padding:8px 16px; background:<?= $p['stock'] > 0 ? 'rgba(74, 124, 89, 0.1)' : 'rgba(184, 59, 59, 0.15)' ?>; border-radius:var(--radius-full); width:fit-content; box-shadow:var(--shadow-sm);">
                    <?php if ($p['stock'] > 0): ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $p['stock'] <= 5 ? 'Plus que ' . $p['stock'] . ' en stock !' : 'En stock — Expédition sous 24h' ?>
                    <?php else: ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Rupture de stock
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ajout au panier & Quantité -->
            <div style="display:flex; gap:16px; margin-bottom:20px;">
                <div style="border:1px solid var(--border); border-radius:var(--radius-full); display:flex; align-items:center; padding:4px; max-width:140px; background:var(--white); box-shadow:var(--shadow-sm);">
                    <button onclick="changeProductQty(-1)" style="width:40px; height:40px; font-size:20px; color:var(--ink); font-weight:500; transition:background 0.2s; border-radius:50%;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">−</button>
                    <input type="number" id="prodQtyDisplay" value="1" min="1" readonly style="flex:1; width:100%; text-align:center; border:none; background:transparent; font-size:15px; font-weight:700; color:var(--ink); outline:none;">
                    <button onclick="changeProductQty(1)" style="width:40px; height:40px; font-size:20px; color:var(--ink); font-weight:500; transition:background 0.2s; border-radius:50%;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">+</button>
                </div>
                <button class="btn-saas" onclick="addToCart(<?= $p['id'] ?>, parseInt(document.getElementById('prodQtyDisplay').value) || 1)" style="flex:1; padding:18px; font-size:15px;" <?= $p['stock'] <= 0 ? 'disabled style="flex:1;padding:18px;font-size:15px;opacity:0.5;cursor:not-allowed;"' : '' ?>>
                    <?= $p['stock'] <= 0 ? 'Rupture de stock' : 'Ajouter au panier' ?>
                </button>
            </div>
            
            <button onclick="buyNow(<?= $p['id'] ?>, parseInt(document.getElementById('prodQtyDisplay').value) || 1)" style="width:100%; padding:20px; border-radius:var(--radius-full); font-size:15px; font-weight:700; background:var(--white); color:var(--ink); border:1px solid var(--border); box-shadow:var(--shadow-sm); cursor:pointer; transition:all 0.3s;" onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.transform='translateY(0)';">
                Acheter instantanément
            </button>

            <!-- Bouton WhatsApp Express (Module 2) -->
            <button onclick="orderViaWhatsApp()" style="width:100%; padding:20px; border-radius:var(--radius-full); font-size:15px; font-weight:700; background:#25D366; color:#fff; border:none; box-shadow:0 6px 15px rgba(37, 211, 102, 0.25); cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center; gap:10px; margin-top:16px;" onmouseover="this.style.boxShadow='0 8px 20px rgba(37, 211, 102, 0.4)'; this.style.transform='translateY(-2px)'; this.style.background='#20ba59';" onmouseout="this.style.boxShadow='0 6px 15px rgba(37, 211, 102, 0.25)'; this.style.transform='translateY(0)'; this.style.background='#25D366';">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 001.37 5.084L2 22l5.094-1.333a9.96 9.96 0 004.917 1.294h.005c5.507 0 9.99-4.478 9.99-9.986 0-2.67-1.037-5.178-2.92-7.062A9.925 9.925 0 0012.012 2zm5.859 13.987c-.242.684-1.2 1.252-1.644 1.3-1.12.124-2.527-.272-4.072-1.002-3.155-1.488-5.132-4.664-5.289-4.877-.158-.213-1.28-1.702-1.28-3.245 0-1.542.809-2.3 1.099-2.6.29-.3.636-.376.848-.376h.606c.218 0 .497-.082.775.596.284.696.97 2.37.103 2.545-.866.175-.727.562-.164 1.134.424.431.848.862 1.488 1.488.727.726 1.345 1.09 2.053 1.45.65.334.887.218 1.218-.164.33-.382 1.428-1.666 1.808-2.246.381-.58.763-.48 1.28-.272.515.207 3.284 1.548 3.513 1.666.23.118.382.176.438.272.057.098.057.562-.185 1.246z"/></svg>
                Commander via WhatsApp
            </button>

            <!-- Reassurance -->
            <div style="margin-top:48px; padding:24px; background:var(--white); border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px; font-size:13px; font-weight:600; color:var(--ink-soft);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span>Livraison premium trackée 24–48h</span>
                </div>
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px; font-size:13px; font-weight:600; color:var(--ink-soft);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <span>Paiement 100% sécurisé via Stripe</span>
                </div>
                <div style="display:flex; align-items:center; gap:16px; font-size:13px; font-weight:600; color:var(--ink-soft);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Qualité d'impression certifiée HD</span>
                </div>
            </div>

            <!-- Description -->
            <div style="margin-top:48px;">
                <h3 style="font-size:20px; font-weight:800; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:24px; color:var(--ink);">Synopsis</h3>
                <div style="font-size:15px; color:var(--ink-soft); line-height:1.7; font-weight:400;"><?= nl2br(e($p['description'])) ?></div>
                
                <?php if ($p['chapters']): ?>
                <details style="margin-top:24px; padding:20px; border:1px solid var(--border); border-radius:var(--radius-md); background:var(--white); box-shadow:var(--shadow-sm); cursor:pointer;">
                    <summary style="font-weight:700; color:var(--ink); font-size:14px; outline:none;">Sommaire des chapitres (<?= substr_count($p['chapters'],"\n")+1 ?>)</summary>
                    <div style="margin-top:16px; font-size:14px; color:var(--muted); line-height:1.7; padding-left:16px; border-left:2px solid var(--primary);"><?= nl2br(e($p['chapters'])) ?></div>
                </details>
                <?php endif; ?>
            </div>

            <!-- Metadata -->
            <div style="margin-top:48px;">
                <h3 style="font-size:20px; font-weight:800; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:24px; color:var(--ink);">Métadonnées</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
                    <div style="display:flex; flex-direction:column; gap:4px;"><span style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; font-weight:700;">Auteur</span><span style="font-size:14px; font-weight:600; color:var(--ink);"><?= e($p['author']) ?></span></div>
                    <div style="display:flex; flex-direction:column; gap:4px;"><span style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; font-weight:700;">Genre</span><span style="font-size:14px; font-weight:600; color:var(--ink);"><?= e($p['cat_name']) ?></span></div>
                    <div style="display:flex; flex-direction:column; gap:4px;"><span style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; font-weight:700;">Pagination</span><span style="font-size:14px; font-weight:600; color:var(--ink);"><?= $p['pages'] ?> pages</span></div>
                    <div style="display:flex; flex-direction:column; gap:4px;"><span style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; font-weight:700;">Éditeur</span><span style="font-size:14px; font-weight:600; color:var(--ink);"><?= e($p['publisher']) ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mega Deals -->
    <div style="margin-top:80px; max-width:1200px; margin-left:auto; margin-right:auto; background:var(--bg); border:1px solid var(--border); padding:64px 48px; border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); text-align:center; position:relative; overflow:hidden;">
        <h2 style="font-size:32px; font-weight:800; margin-bottom:16px; position:relative; z-index:1; color:var(--ink);">Avantage <span style="color:var(--primary);">Collectionneur</span></h2>
        <p style="font-size:16px; color:var(--ink-soft); max-width:600px; margin:0 auto 48px; position:relative; z-index:1; font-weight:500;">Étendez votre bibliothèque astucieusement. Nos algorithmes déduisent automatiquement les mangas offerts dans votre panier.</p>
        
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:24px; position:relative; z-index:1;">
            <div style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm); padding:32px; border-radius:24px; transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
                <div style="font-size:48px; font-weight:900; color:var(--primary); margin-bottom:16px; letter-spacing:-0.03em;">T3</div>
                <div style="font-size:16px; font-weight:700; margin-bottom:8px; color:var(--ink);">1 Manga Offert</div>
                <div style="font-size:13px; color:var(--muted); font-weight:600;">+ Livraison Offerte</div>
            </div>
            <div style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm); padding:32px; border-radius:24px; transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
                <div style="font-size:48px; font-weight:900; color:var(--primary); margin-bottom:16px; letter-spacing:-0.03em;">T5</div>
                <div style="font-size:16px; font-weight:700; margin-bottom:8px; color:var(--ink);">2 Mangas Offerts</div>
                <div style="font-size:13px; color:var(--muted); font-weight:600;">+ Livraison Offerte</div>
            </div>
            <div style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm); padding:32px; border-radius:24px; transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
                <div style="font-size:48px; font-weight:900; color:var(--primary); margin-bottom:16px; letter-spacing:-0.03em;">T7</div>
                <div style="font-size:16px; font-weight:700; margin-bottom:8px; color:var(--ink);">4 Mangas Offerts</div>
                <div style="font-size:13px; color:var(--muted); font-weight:600;">+ Livraison Offerte</div>
            </div>
        </div>
    </div>

    <!-- Avis Clients -->
    <?php
    $reviewSuccess = !empty($_GET['review']) && $_GET['review'] === 'ok';
    $reviewError   = !empty($_GET['review_err']) ? $_GET['review_err'] : '';
    $stmtRev = getDB()->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmtRev->execute([(int)$p['id']]);
    $reviews = $stmtRev->fetchAll();
    ?>
    <div style="margin-top:80px; max-width:1200px; margin-left:auto; margin-right:auto;">
        <h2 style="font-size:28px; font-weight:800; border-bottom:1px solid var(--border); padding-bottom:20px; margin-bottom:40px; color:var(--ink);">Avis clients <span style="color:var(--primary);">(<?= count($reviews) ?>)</span></h2>

        <?php if ($reviewSuccess): ?>
            <div style="background:rgba(74, 124, 89, 0.15);color:var(--green);padding:14px 18px;border-radius:10px;font-size:13px;margin-bottom:24px;border:1px solid rgba(74, 124, 89, 0.2);font-weight:600;"> Votre avis a été publié. Merci !</div>
        <?php elseif ($reviewError): ?>
            <div style="background:rgba(184, 59, 59, 0.15);color:var(--red);padding:14px 18px;border-radius:10px;font-size:13px;margin-bottom:24px;border:1px solid rgba(184, 59, 59, 0.2);font-weight:600;"><?= e(urldecode($reviewError)) ?></div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start;">
            <!-- Liste des avis -->
            <div style="display:flex; flex-direction:column; gap:16px;">
                <?php if (empty($reviews)): ?>
                    <div style="text-align:center; padding:48px 20px; background:var(--bg); border-radius:var(--radius-lg); border:1px solid var(--border);">
                        <div style="font-size:36px; margin-bottom:12px; opacity:0.4; display:flex; justify-content:center; color:var(--muted);">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <p style="color:var(--muted); font-size:14px; font-weight:500;">Aucun avis pour l'instant. Soyez le premier !</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                    <div style="background:var(--white); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; box-shadow:var(--shadow-sm);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800;"><?= strtoupper(mb_substr($rev['customer_name'],0,1)) ?></div>
                                <span style="font-weight:700; font-size:14px; color:var(--ink);"><?= e($rev['customer_name']) ?></span>
                            </div>
                            <div style="color:#eab308; font-size:16px;"><?= str_repeat('★', (int)$rev['rating']) ?><?= str_repeat('☆', 5-(int)$rev['rating']) ?></div>
                        </div>
                        <p style="font-size:14px; color:var(--ink-soft); line-height:1.6; margin:0;"><?= e($rev['comment']) ?></p>
                        <div style="font-size:11px; color:var(--muted); margin-top:10px; font-weight:500;"><?= date('d M Y', strtotime($rev['created_at'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Formulaire avis -->
            <div style="background:var(--white); border:1px solid var(--border); border-radius:var(--radius-lg); padding:28px; box-shadow:var(--shadow-sm); position:sticky; top:100px;">
                <h3 style="font-size:16px; font-weight:800; margin-bottom:20px; color:var(--ink);">Laisser un avis</h3>
                <?php if (isset($_SESSION['user'])): ?>
                <form method="POST" action="actions/review.php" style="display:flex; flex-direction:column; gap:16px;">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="product_slug" value="<?= e($p['slug']) ?>">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:8px;">Note</label>
                        <div style="display:flex; gap:6px;" id="starRow">
                            <?php for ($s=1; $s<=5; $s++): ?>
                            <button type="button" onclick="setRating(<?= $s ?>)" id="star-<?= $s ?>" aria-label="Note <?= $s ?> étoile<?= $s > 1 ? 's' : '' ?>" style="font-size:28px; background:none; border:none; cursor:pointer; color:#d1d5db; transition:color 0.15s; padding:0;">★</button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:8px;">Votre commentaire</label>
                        <textarea name="comment" required rows="4" placeholder="Partagez votre expérience avec ce manga..." style="width:100%; padding:12px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; font-family:inherit; resize:vertical; background:var(--white); color:var(--ink); box-sizing:border-box;"></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="padding:14px; border-radius:10px; font-size:14px; font-weight:700;">Publier l'avis</button>
                </form>
                <script>
                function setRating(n) {
                    document.getElementById('ratingInput').value = n;
                    for (let i=1; i<=5; i++) {
                        document.getElementById('star-'+i).style.color = i <= n ? '#eab308' : '#d1d5db';
                    }
                }
                </script>
                <?php else: ?>
                <div style="text-align:center; padding:20px;">
                    <p style="color:var(--muted); font-size:14px; margin-bottom:16px;">Connectez-vous pour laisser un avis.</p>
                    <a href="login.php" class="btn-primary" style="padding:12px 24px; font-size:14px;">Se connecter</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if ($related): ?>
    <div style="margin-top:100px; max-width:1200px; margin-left:auto; margin-right:auto;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px; border-bottom:1px solid var(--border); padding-bottom:24px;">
            <div>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--primary); display:block; margin-bottom:8px;">Suggestions algorithmiques</span>
                <h2 style="font-size:32px; font-weight:800; color:var(--ink); letter-spacing:-0.02em;">Généré pour <span style="color:var(--primary);">vous</span></h2>
            </div>
            <a href="catalogue.php?cat=<?= e($p['cat_slug']) ?>" style="font-size:13px; font-weight:600; color:var(--primary); transition:opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Voir plus de résultats →</a>
        </div>
        
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:32px;">
            <?php 
            $mainProduct = $p;
            foreach (array_slice($related, 0, 4) as $p): 
                include 'includes/product_card.php'; 
            endforeach; 
            $p = $mainProduct;
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Liseuse Booklet -->
<div class="booklet-modal" id="bookletModal" onclick="closeBookletReader(event)">
    <div class="booklet-container" onclick="event.stopPropagation()">
        <div class="booklet-header">
            <h3 style="font-size:16px; font-weight:800; color:var(--ink); margin:0;">Extrait Gratuit : <?= e($p['title']) ?></h3>
            <button onclick="closeBookletReader(null)" style="background:none; border:none; font-size:24px; color:var(--muted); cursor:pointer; font-weight:bold;">&times;</button>
        </div>
        <div class="booklet-body">
            <!-- Page 1 -->
            <div class="booklet-page active" id="bPage-1">
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%; text-align:center; gap:20px; font-family:'Playfair Display', serif;">
                    <h2 style="font-size:24px; font-weight:900; color:var(--primary); margin:0;"><?= e($p['title']) ?></h2>
                    <p style="font-family:'Inter', sans-serif; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted); letter-spacing:1px; margin:0;">Synopsis Extrait</p>
                    <p style="font-family:'Inter', sans-serif; font-size:13.5px; line-height:1.6; color:var(--ink-soft); max-width:340px; margin:0; font-weight:500;">
                        Découvrez les premières pages exclusives de ce chef-d'œuvre. L'aventure vous attend au fil des chapitres...
                    </p>
                    <div style="width:60px; height:2px; background:var(--primary); margin-top:10px;"></div>
                </div>
            </div>
            
            <!-- Page 2 -->
            <div class="booklet-page" id="bPage-2" style="padding:0;">
                <div style="width:100%; height:100%; position:relative;">
                    <img src="<?= e($p['image_url']) ?>" style="width:100%; height:100%; object-fit:cover;" alt="Cover Page">
                    <div style="position:absolute; bottom:16px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,0.6); color:#fff; font-size:10px; font-weight:bold; padding:4px 10px; border-radius:10px; font-family:'Inter',sans-serif;">Illustration de Couverture</div>
                </div>
            </div>
            
            <!-- Page 3 -->
            <div class="booklet-page" id="bPage-3">
                <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%; text-align:center; gap:16px;">
                    <h3 style="font-family:'Playfair Display', serif; font-size:20px; font-weight:900; margin:0;">Prêt à lire la suite ?</h3>
                    <p style="font-family:'Inter', sans-serif; font-size:13px; color:var(--ink-soft); line-height:1.6; max-width:300px; margin:0; font-weight:500;">
                        Ajoutez ce tome à votre panier ou commandez-le instantanément en un clic pour débloquer sa lecture intégrale physique HD.
                    </p>
                    <button onclick="closeBookletReader(null); addToCart(<?= $p['id'] ?>, 1);" class="btn-primary" style="padding:10px 20px; font-size:13px; margin-top:10px;">Ajouter au panier</button>
                </div>
            </div>
        </div>
        <div class="booklet-footer">
            <button class="btn-booklet-nav" id="btnBPrev" onclick="changeBookletPage(-1)" disabled>Précédent</button>
            <span style="font-size:13px; font-weight:700; color:var(--muted);" id="bookletPageNum">Page 1 / 3</span>
            <button class="btn-booklet-nav" id="btnBNext" onclick="changeBookletPage(1)">Suivant</button>
        </div>
    </div>
</div>

<script>


function orderViaWhatsApp() {
    const qty = parseInt(document.getElementById('prodQtyDisplay').value) || 1;
    const title = "<?= e(addslashes($p['title'])) ?>";
    const price = <?= $p['price'] ?>;
    const total = price * qty;
    
    const message = `Bonjour MangaShop !\nJe souhaite commander le manga suivant :\n\n*Manga* : ${title}\n*Quantité* : ${qty}\n*Montant Total* : ${total.toFixed(2)} MAD\n\nMerci de me recontacter pour finaliser la livraison (Paiement à la livraison) !`;
    const encoded = encodeURIComponent(message);
    window.open(`https://wa.me/<?= WHATSAPP_NUMBER ?>?text=${encoded}`, '_blank');
}


document.addEventListener('DOMContentLoaded', () => {
    const gallery = document.querySelector('.gallery-main');
    if (gallery) {
        const img = gallery.querySelector('img');
        if (img) {
            gallery.addEventListener('mousemove', (e) => {
                const rect = gallery.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transformOrigin = `${x}% ${y}%`;
                img.style.transform = 'scale(1.7)';
            });
            gallery.addEventListener('mouseleave', () => {
                img.style.transform = 'scale(1)';
                img.style.transformOrigin = 'center';
            });
        }
    }
});


let activeBPage = 1;
const totalBPages = 3;

function openBookletReader() {
    const modal = document.getElementById('bookletModal');
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        activeBPage = 1;
        updateBookletPages();
    }
}

function closeBookletReader(e) {
    if (e && e instanceof Event && e.target !== document.getElementById('bookletModal')) return;
    const modal = document.getElementById('bookletModal');
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

function changeBookletPage(delta) {
    activeBPage = Math.max(1, Math.min(totalBPages, activeBPage + delta));
    updateBookletPages();
}

function updateBookletPages() {
    for (let i = 1; i <= totalBPages; i++) {
        const page = document.getElementById('bPage-' + i);
        if (page) {
            if (i === activeBPage) {
                page.classList.add('active');
                page.style.display = 'flex';
                // Re-trigger animation
                page.style.animation = 'none';
                page.offsetHeight; // force reflow
                page.style.animation = '';
            } else {
                page.classList.remove('active');
                page.style.display = 'none';
            }
        }
    }
    
    const prev = document.getElementById('btnBPrev');
    const next = document.getElementById('btnBNext');
    if (prev) prev.disabled = activeBPage === 1;
    if (next) next.disabled = activeBPage === totalBPages;
    
    const num = document.getElementById('bookletPageNum');
    if (num) num.innerText = 'Page ' + activeBPage + ' / ' + totalBPages;
}
</script>

<?php require_once 'includes/footer.php'; ?>
