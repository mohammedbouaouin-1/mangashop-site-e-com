<?php

$current_price = floatval($p['price']);
$has_old       = !empty($p['old_price']);
$rating        = number_format($p['rating'] ?? 4.5, 1);
?>
<div class="product-card" style="background:#fff; border-radius:var(--radius-lg); position:relative; transition:all 0.3s ease; box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; display:flex; flex-direction:column;" 
     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-float)';" 
     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
    
    
    <div style="position:relative; aspect-ratio:1/1.3; background:var(--bg); overflow:hidden;">
        <a href="product.php?slug=<?= e($p['slug']) ?>" style="display:block; width:100%; height:100%;">
            <img src="<?= asset($p['image_url']) ?>" alt="<?= e($p['title']) ?>" loading="lazy" referrerpolicy="no-referrer" style="width:100%; height:100%; object-fit:contain; padding:24px; transition:transform 0.4s ease;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
        </a>
        
        <div style="position:absolute; top:16px; left:16px; display:flex; flex-direction:column; gap:8px;">
            <?php if(!empty($p['is_new'])): ?>
                <span style="background:#fff; color:var(--ink); font-size:11px; font-weight:700; padding:4px 10px; border-radius:var(--radius-full); box-shadow:var(--shadow-sm);">Nouveau</span>
            <?php endif; ?>
            <?php if($has_old): ?>
                <span style="background:var(--red); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:var(--radius-full); box-shadow:var(--shadow-sm);">-<?= discount((float)$p['old_price'], $current_price) ?>%</span>
            <?php endif; ?>
        </div>

        <button onclick="toggleWishlist(<?= (int)$p['id'] ?>, this)" aria-label="Favoris" style="position:absolute; top:16px; right:16px; width:36px; height:36px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:var(--shadow-sm); color:var(--muted); transition:color 0.2s;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--muted)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
    </div>

    
    <div style="padding:24px; display:flex; flex-direction:column; flex:1;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-size:12px; font-weight:600; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;"><?= e($p['cat_name'] ?? 'Manga') ?></span>
            <span style="font-size:12px; font-weight:600; color:var(--ink-soft); display:flex; align-items:center; gap:4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="none" style="color:#eab308;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <?= $rating ?>
            </span>
        </div>
        
        <a href="product.php?slug=<?= e($p['slug']) ?>" style="text-decoration:none; margin-bottom:8px;">
            <h3 style="font-size:17px; font-weight:700; color:var(--ink); line-height:1.4; letter-spacing:-0.01em; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"><?= e($p['title']) ?></h3>
        </a>
        
        <p style="font-size:13px; color:var(--muted); font-weight:500; margin-bottom:auto;"><?= e($p['author'] ?? 'Éditeur Inconnu') ?></p>
        
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
            <div>
                <span style="display:block; font-size:18px; font-weight:800; color:var(--ink);"><?= number_format($current_price, 2) ?> MAD</span>
                <?php if($has_old): ?>
                    <span style="display:block; font-size:13px; color:var(--muted); text-decoration:line-through; font-weight:500;"><?= number_format($p['old_price'], 2) ?></span>
                <?php endif; ?>
            </div>
            
            <button onclick="addToCart(<?= (int)$p['id'] ?>)" aria-label="Ajouter" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; background:var(--bg); border:1px solid var(--border); border-radius:50%; color:var(--ink); transition:all 0.2s;" onmouseover="this.style.background='var(--primary)'; this.style.color='#fff'; this.style.borderColor='var(--primary)';" onmouseout="this.style.background='var(--bg)'; this.style.color='var(--ink)'; this.style.borderColor='var(--border)';">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            </button>
        </div>
    </div>
</div>
