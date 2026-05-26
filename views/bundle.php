<?php require_once 'includes/header.php'; ?>

<div class="product-page">
    <nav class="breadcrumb">
        <a href="index.php">Accueil</a><span>/</span>
        <a href="bundles.php">Bundles</a><span>/</span>
        <span><?= e($bundle['name']) ?></span>
    </nav>

    <div class="product-layout">
        <div class="gallery-col">
            <?php $coverCount = count($prods); ?>
            <div class="bundle-cover-collage" data-count="<?= $coverCount ?>">
                <?php foreach ($prods as $pr): ?>
                <div class="collage-cell">
                    <div class="collage-initial"><?= e(mb_substr($pr['title'], 0, 1)) ?></div>
                    <?php if (!empty($pr['image_url'])): ?>
                        <img src="<?= asset($pr['image_url']) ?>" alt="<?= e($pr['title']) ?>" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:2;" onerror="this.style.display='none'">
                    <?php endif; ?>
                    <div class="collage-label"><?= e(mb_substr($pr['title'], 0, 16)) ?></div>
                </div>
                <?php endforeach; ?>
                <div class="collage-overlay"></div>
                <div class="collage-pack-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <?= $coverCount ?> volumes
                </div>
            </div>
        </div>

        <div class="prod-info-right">
            <div class="prod-badge-exclusive">PACK EXCLUSIF</div>
            <div class="prod-author-link">Contient <?= count($prods) ?> manga<?= count($prods)>1?'s':'' ?></div>
            <h1 class="prod-full-title"><?= e($bundle['name']) ?></h1>

            <div class="prod-pricing-block">
                <?php if ($bundle['old_price']): ?><span class="prod-badge-sale">Solde</span><br><?php endif; ?>
                <span class="prod-price-now"><?= number_format($bundle['price'],2) ?> MAD</span>
                <?php if ($bundle['old_price']): ?>
                <div class="prod-price-row">
                    <span class="prod-price-old"><?= number_format($bundle['old_price'],2) ?> MAD</span>
                    <span class="prod-price-save">-<?= discount((float)$bundle['old_price'],(float)$bundle['price']) ?>%</span>
                    <span style="font-size:12px;color:var(--green);font-weight:500">Economisez <?= number_format($bundle['old_price']-$bundle['price'],0) ?> MAD</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="prod-qty-row">
                <div class="qty-control">
                    <button class="qty-ctrl-btn" onclick="changeProductQty(-1)">−</button>
                    <input type="number" id="prodQtyDisplay" class="qty-display" value="1" min="1" readonly>
                    <button class="qty-ctrl-btn" onclick="changeProductQty(1)">+</button>
                </div>
                <button class="btn-outline-dark" onclick="addToCart('b_<?= $bundle['id'] ?>', parseInt(document.getElementById('prodQtyDisplay').value) || 1)">Ajouter au panier</button>
            </div>
            <button class="btn-primary-red" onclick="buyNow('b_<?= $bundle['id'] ?>', parseInt(document.getElementById('prodQtyDisplay').value) || 1)" style="width:100%">Acheter maintenant</button>

            <div class="prod-guarantees">
                <div class="guarantee-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg><span>Livraison 24–48h</span></div>
                <div class="guarantee-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg><span>Paiement a la livraison</span></div>
                <div class="guarantee-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg><span><?= count($prods) ?> volumes inclus</span></div>
            </div>

            <div class="prod-desc-block">
                <h3>Mangas inclus dans ce pack</h3>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <?php foreach ($prods as $pr): ?>
                    <a href="product.php?slug=<?= e($pr['slug']) ?>" style="display:flex;align-items:center;gap:12px;padding:12px;border:1px solid var(--border);border-radius:10px;transition:border-color .2s" onmouseover="this.style.borderColor='var(--ink)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="position:relative;width:44px;height:60px;border-radius:5px;overflow:hidden;flex-shrink:0;background:var(--bg);display:flex;align-items:center;justify-content:center;font-weight:bold;color:var(--muted)">
                            <div style="position:absolute;z-index:1;"><?= e(mb_substr($pr['title'], 0, 1)) ?></div>
                            <?php if(!empty($pr['image_url'])): ?>
                                <img src="<?= asset($pr['image_url']) ?>" alt="<?= e($pr['title']) ?>" referrerpolicy="no-referrer" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:2;background:#fff;" onerror="this.style.display='none'">
                            <?php endif; ?>
                        </div>
                        <div style="flex:1">
                            <div style="font-size:13px;font-weight:600"><?= e($pr['title']) ?></div>
                            <div style="font-size:11.5px;color:var(--muted)"><?= e($pr['author']) ?></div>
                        </div>
                        <div style="font-size:13px;font-weight:600"><?= number_format($pr['price'],2) ?> MAD</div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="prod-desc-block">
                <h3>Description</h3>
                <div class="prod-desc"><?= nl2br(e($bundle['description'])) ?></div>
            </div>
        </div>
    </div>

    <div style="margin-top:64px;padding-bottom:48px">
        <div class="section-head section-inner" style="padding:0;margin-bottom:24px">
            <div>
                <p class="section-label">Vous aimerez aussi</p>
                <h2 class="section-title">Autres <span class="accent">packs</span></h2>
            </div>
            <a href="bundles.php" class="view-all">Voir tout →</a>
        </div>
        <div class="related-grid" style="align-items:stretch">
            <?php
            $shown = 0;
            foreach ($others as $b):
                if ($b['id'] === $bundle['id']) continue;
                if ($shown >= 3) break;
                $shown++;
                $pct = (!empty($b['old_price']) && $b['old_price'] > $b['price'])
                       ? round((1 - $b['price']/$b['old_price'])*100) : 0;
            ?>
            <a href="bundle.php?slug=<?= e($b['slug']) ?>" class="other-bundle-card">
                <div class="obc-img">
                    <div class="obc-initial"><?= e(mb_substr($b['name'], 0, 1)) ?></div>
                    <?php if (!empty($b['image_url'])): ?>
                        <img src="<?= asset($b['image_url']) ?>" alt="<?= e($b['name']) ?>" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:2;" onerror="this.style.display='none'">
                    <?php endif; ?>
                    <?php if ($pct > 0): ?>
                        <span class="obc-pct">-<?= $pct ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="obc-body">
                    <div class="obc-name"><?= e($b['name']) ?></div>
                    <div class="obc-price"><?= number_format($b['price'], 2) ?> MAD</div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
