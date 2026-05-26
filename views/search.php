<?php require_once 'includes/header.php'; ?>

<div class="search-page">
    <div class="search-hero" style="background:var(--bg); border-bottom:1px solid var(--border); padding: 48px 0;">
        <div class="section-container">
            <h1 style="font-family:'Playfair Display',serif; font-size:32px; font-weight:800; margin-bottom:24px; text-align:center;">Trouvez votre <span style="color:var(--gold);">prochaine pépite</span></h1>
            <form action="search.php" method="GET" style="max-width:600px; margin:0 auto; position:relative;">
                <input type="text" name="q" value="<?= e($q) ?>" placeholder="Titre, auteur, genre..." 
                       style="width:100%; padding:18px 24px; border:1px solid var(--border); border-radius:14px; font-size:16px; font-weight:500; outline:none; box-shadow:var(--shadow-sm); background:var(--white);">
                <button type="submit" style="position:absolute; right:8px; top:8px; bottom:8px; background:var(--ink); color:#fff; border:none; border-radius:10px; padding:0 24px; font-weight:700; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='var(--gold)'; this.style.color='#000';" onmouseout="this.style.background='var(--ink)'; this.style.color='#fff';">Chercher</button>
            </form>
        </div>
    </div>

    <div class="section-container" style="padding-top:48px;">
        <?php if ($q): ?>
            <div class="section-header">
                <h3>Résultats pour <span style="color:var(--gold);">"<?= e($q) ?>"</span></h3>
                <span class="gold-text" style="font-size:14px; font-weight:600;"><?= count($results) ?> œuvre<?= count($results)>1?'s':'' ?> trouvée<?= count($results)>1?'s':'' ?></span>
            </div>

            <?php if ($results): ?>
                <div class="products-grid">
                    <?php foreach ($results as $p): include 'includes/product_card.php'; endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center; padding:64px 20px; background:var(--bg2); border-radius:16px; border:1px solid var(--border);">
                    <div style="font-size:48px; margin-bottom:16px; opacity:0.5; display:flex; justify-content:center; color:var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:20px; margin-bottom:8px;">Aucune œuvre trouvée</h3>
                    <p style="color:var(--muted); font-size:14px;">Essayez d'autres mots-clés ou parcourez nos œuvres populaires ci-dessous.</p>
                </div>
            <?php endif; ?>

            <div style="height:80px;"></div>
        <?php endif; ?>

        <!-- POPULAIRES SECTION -->
        <div class="section-header">
            <div>
                <span class="section-label">Sélection</span>
                <h2 class="section-title">Les plus <span style="color:var(--gold);">populaires</span></h2>
            </div>
            <a href="catalogue.php?sort=rating" class="view-all">Tout Explorer →</a>
        </div>

        <div class="scroll-wrap" style="position:relative; margin:0 -20px; padding:0 20px;">
            <div class="scroll-track horizontal-only" style="display:flex; overflow-x:auto; gap:20px; padding:20px 0; scrollbar-width:none; -ms-overflow-style:none;">
                <?php foreach ($popular as $p): ?>
                <div style="min-width:260px; max-width:260px; flex-shrink:0;">
                    <?php include 'includes/product_card.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.horizontal-only::-webkit-scrollbar { display: none; }
</style>

<?php require_once 'includes/footer.php'; ?>
