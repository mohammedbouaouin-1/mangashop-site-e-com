<?php require_once 'includes/header.php'; ?>

<div class="cart-page">
    <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;margin-bottom:32px">
        Mon <span style="color:var(--red)">Panier</span>
        <?php if ($totalQty): ?><span style="font-size:15px;font-weight:400;color:var(--muted);margin-left:10px">(<span id="cartPageCount"><?= $totalQty ?></span> article<?= $totalQty>1?'s':'' ?>)</span><?php endif; ?>
    </h1>

    <?php if (empty($cartItems)): ?>
    <div style="text-align:center;padding:80px 20px">
        <div style="width:72px;height:72px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--muted)"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:22px;margin-bottom:10px">Votre panier est vide</h2>
        <p style="color:var(--muted);margin-bottom:28px;font-size:14px">Decouvrez notre catalogue et ajoutez vos mangas preferes.</p>
        <a href="catalogue.php" class="btn-primary">Voir le catalogue</a>
    </div>

    <?php else: ?>

    <div id="cartBannerZone">
    <?php if ($freeBooks > 0): ?>
    <div class="free-books-banner" style="margin-bottom:24px">
        Vous avez droit a <strong><?= $freeBooks ?> manga<?= $freeBooks>1?'s':'' ?> gratuit<?= $freeBooks>1?'s':'' ?></strong> + livraison offerte. Ajoutez-les simplement au panier.
    </div>
    <?php elseif ($totalQty < 3): ?>
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 18px;margin-bottom:24px;font-size:13px;color:#92400e">
        Ajoutez <?= 3 - $totalQty ?> manga<?= (3-$totalQty)>1?'s':'' ?> de plus pour obtenir <strong>1 gratuit</strong> + livraison offerte.
        <a href="catalogue.php" style="color:var(--red);margin-left:8px;font-weight:500">Continuer les achats</a>
    </div>
    <?php endif; ?>
    </div>

    <div class="cart-layout">
        <div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th style="text-align:center">Quantite</th>
                        <th style="text-align:right">Prix</th>
                        <th style="text-align:right">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cartTableBody">
                    <?php foreach ($cartItems as $item): ?>
                    <tr id="row-<?= e($item['id']) ?>">
                        <td>
                            <div class="cart-prod-cell">
                                <div class="cart-prod-thumb" style="position:relative;overflow:hidden;">
                                    <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"><?= strtoupper(mb_substr($item['title'],0,1)) ?></span>
                                    <?php if(!empty($item['image_url'])): ?>
                                        <img src="<?= asset($item['image_url']) ?>" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:2;" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php
                                        $isBundle = strpos((string)($item['id']), 'b_') === 0;
                                        $itemLink = $isBundle
                                            ? 'bundle.php?slug=' . e($item['slug'] ?? '')
                                            : 'product.php?slug=' . e($item['slug'] ?? '');
                                    ?>
                                    <a href="<?= $itemLink ?>" class="cart-prod-title"><?= e($item['title']) ?></a>
                                    <div class="cart-prod-author"><?= e($item['author'] ?? 'MangaShop') ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center">
                            <div class="cart-qty-ctrl" style="display:inline-flex">
                                <button onclick="cartUpdateQty('<?= e($item['id']) ?>',-1)">−</button>
                                <span id="qty-<?= e($item['id']) ?>"><?= $item['qty'] ?></span>
                                <button onclick="cartUpdateQty('<?= e($item['id']) ?>',1)">+</button>
                            </div>
                        </td>
                        <td style="text-align:right;font-size:14px"><?= number_format($item['price'],2) ?> MAD</td>
                        <td style="text-align:right;font-size:14px;font-weight:700" id="line-<?= e($item['id']) ?>"><?= number_format($item['line'],2) ?> MAD</td>
                        <td style="text-align:right">
                            <button class="cart-remove" onclick="cartRemove('<?= e($item['id']) ?>')" title="Retirer">&times;</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="display:flex;justify-content:space-between;margin-top:20px;flex-wrap:wrap;gap:10px">
                <a href="catalogue.php" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-weight:500;color:var(--ink)">Continuer les achats</a>
            </div>

            <div class="deals-block" style="margin-top:32px">
                <div class="deals-block-title">Offres groupees</div>
                <p class="deals-block-sub">Ajoutez plus de mangas et obtenez des cadeaux automatiquement.</p>
                <div class="deals-grid">
                    <div class="deal-item"><div class="deal-num">3</div><div class="deal-txt">3 achetes — 1 gratuit</div><div class="deal-sub">+ Livraison offerte</div></div>
                    <div class="deal-item"><div class="deal-num">5</div><div class="deal-txt">5 achetes — 2 gratuits</div><div class="deal-sub">+ Livraison offerte</div></div>
                    <div class="deal-item"><div class="deal-num">7</div><div class="deal-txt">7 achetes — 4 gratuits</div><div class="deal-sub">+ Livraison offerte</div></div>
                </div>
            </div>
        </div>

        <div class="order-summary">
            <h3>Recapitulatif</h3>
            
            <!-- Progress Meter (Module 3) -->
            <div id="cartPageShippingMeter" style="margin-bottom:20px;">
                <?php if ($totalQty > 0): 
                    $percent = $totalQty >= 2 ? 100 : 50;
                    $label = $totalQty >= 2 
                        ? "Félicitations ! Livraison Gratuite débloquée !" 
                        : "Plus qu'1 manga pour débloquer la livraison gratuite !";
                    $color = $totalQty >= 2 ? "var(--green)" : "var(--primary)";
                ?>
                <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px 16px; transition: all 0.3s;">
                   <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:var(--ink); margin-bottom:8px;">
                       <span style="font-size:11px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:6px;"><?= $label ?></span>
                   </div>
                   <div style="height:6px; background:var(--bg2); border-radius:var(--radius-full); overflow:hidden; border: 1px solid var(--border);">
                       <div style="height:100%; width:<?= $percent ?>%; background:linear-gradient(90deg, var(--primary), <?= $color ?>); border-radius:var(--radius-full); transition:width 0.4s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                   </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Progress Meter Collectionneur -->
            <div id="cartPageCollectorMeter" style="margin-bottom:20px;">
                <?php
                if ($totalQty > 0):
                    $nextMilestone = 3;
                    $nextFree = 1;
                    $prevMilestone = 0;
                    if ($totalQty >= 7) {
                        $nextMilestone = 7;
                        $nextFree = 4;
                        $percent = 100;
                        $label = "🎉 Avantage Collectionneur Max débloqué ! (4 mangas OFFERTS)";
                    } elseif ($totalQty >= 5) {
                        $nextMilestone = 7;
                        $nextFree = 4;
                        $prevMilestone = 5;
                        $percent = (($totalQty - 5) / 2) * 100;
                        $needed = 7 - $totalQty;
                        $label = "Plus que <strong>$needed</strong> manga" . ($needed > 1 ? "s" : "") . " pour obtenir <strong>4 mangas GRATUITS</strong> !";
                    } elseif ($totalQty >= 3) {
                        $nextMilestone = 5;
                        $nextFree = 2;
                        $prevMilestone = 3;
                        $percent = (($totalQty - 3) / 2) * 100;
                        $needed = 5 - $totalQty;
                        $label = "Plus que <strong>$needed</strong> manga" . ($needed > 1 ? "s" : "") . " pour obtenir <strong>2 mangas GRATUITS</strong> !";
                    } else {
                        $nextMilestone = 3;
                        $nextFree = 1;
                        $prevMilestone = 0;
                        $percent = ($totalQty / 3) * 100;
                        $needed = 3 - $totalQty;
                        $label = "Plus que <strong>$needed</strong> manga" . ($needed > 1 ? "s" : "") . " pour obtenir <strong>1 manga GRATUIT</strong> !";
                    }
                ?>
                <div style="background:rgba(212,175,55,0.06); border:1px solid rgba(212,175,55,0.25); border-radius:12px; padding:12px 16px; transition: all 0.3s;">
                   <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:var(--ink); margin-bottom:8px;">
                       <span style="font-size:11px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:6px;"><?= $label ?></span>
                   </div>
                   <div style="height:6px; background:var(--bg2); border-radius:var(--radius-full); overflow:hidden; border: 1px solid var(--border);">
                       <div style="height:100%; width:<?= $percent ?>%; background:linear-gradient(90deg, var(--primary), var(--gold)); border-radius:var(--radius-full); transition:width 0.4s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                   </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="summary-row"><span>Sous-total</span><span id="cartPageSubtotal"><?= number_format($subtotal,2) ?> MAD</span></div>
            <?php if ($freeBooks > 0): ?>
            <div class="summary-row" style="color:var(--green)"><span><?= $freeBooks ?> manga<?= $freeBooks>1?'s':'' ?> offert<?= $freeBooks>1?'s':'' ?></span><span>Gratuit</span></div>
            <?php endif; ?>
            
            <!-- Économies Réelles -->
            <div id="savingsCalculatedRow" style="display: <?= ($freeBooks > 0 || !empty($_SESSION['promo'])) ? 'flex' : 'none' ?>; justify-content:space-between; margin-bottom:8px; color:var(--green); font-size:14px; font-weight:700;">
                <span>Économies Réalisées</span>
                <span id="savingsPageTotalAmt">
                    <?php
                    $savingsAmt = 0;
                    if ($freeBooks > 0) { $savingsAmt += $freeBooks * 49; }
                    if (!empty($_SESSION['promo'])) { $savingsAmt += $subtotal * $_SESSION['promo']['pct'] / 100; }
                    echo '-' . number_format($savingsAmt, 2) . ' MAD';
                    ?>
                </span>
            </div>

            <div class="summary-row">
                <span>Livraison</span>
                <span id="cartPageShipping" style="color:<?= $shipping===0?'var(--green)':'inherit' ?>"><?= $shipping===0?'Gratuite':number_format($shipping,2).' MAD' ?></span>
            </div>
            <hr class="summary-sep">
            <div class="summary-row summary-total"><span>Total</span><span id="cartPageTotal" style="color:var(--red)"><?= number_format($total,2) ?> MAD</span></div>


            <!-- Code promo -->
            <div style="margin:16px 0;padding:16px;background:var(--bg);border-radius:10px;border:1px solid var(--border);box-sizing:border-box;">
                <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:10px;">Code promo</div>
                <?php if (!empty($_SESSION['promo'])): ?>
                <div id="promoApplied" style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;font-weight:700;color:var(--green);"> <?= e($_SESSION['promo']['code']) ?> — -<?= $_SESSION['promo']['pct'] ?>%</span>
                    <button onclick="removePromo()" style="font-size:11px;color:var(--red);background:none;border:none;cursor:pointer;font-weight:600;">Retirer</button>
                </div>
                <?php else: ?>
                <div id="promoApplied" style="display:none;align-items:center;justify-content:space-between;">
                    <span id="promoLabel" style="font-size:13px;font-weight:700;color:var(--green);"></span>
                    <button onclick="removePromo()" style="font-size:11px;color:var(--red);background:none;border:none;cursor:pointer;font-weight:600;">Retirer</button>
                </div>
                <div id="promoForm" style="display:flex;gap:8px;align-items:center;box-sizing:border-box;">
                    <input type="text" id="promoInput" placeholder="Ex: MANGA10" style="flex:1;min-width:0;box-sizing:border-box;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--ink);outline:none;">
                    <button onclick="applyPromo()" style="padding:10px 16px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;box-sizing:border-box;white-space:nowrap;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='var(--ink)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='var(--primary)'; this.style.transform='translateY(0)';" onactive="this.style.transform='translateY(1px)';">Appliquer</button>
                </div>
                <!-- Promo hint - active coupons -->
                <div id="promoSuggestions" style="margin-top:12px;border-top:1px dashed var(--border);padding-top:10px;">
                    <?php
                    $db = getDB();
                    $stmt = $db->query("SELECT code, discount_pct FROM promo_codes WHERE active = 1 AND (expires_at IS NULL OR expires_at >= CURDATE()) AND used < max_uses ORDER BY discount_pct DESC");
                    $activePromos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($activePromos)):
                    ?>
                        <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;">Codes promos disponibles (Cliquez pour appliquer) :</div>
                        <div style="display:flex; flex-wrap:wrap; gap:8px;">
                            <?php foreach ($activePromos as $promo): ?>
                                <button onclick="insertPromoCode('<?= e($promo['code']) ?>')" 
                                        style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; background:rgba(212,175,55,0.06); border:1px dashed rgba(212,175,55,0.4); border-radius:6px; font-size:11px; font-weight:700; color:var(--primary); cursor:pointer; transition:all 0.2s;"
                                        onmouseover="this.style.background='rgba(212,175,55,0.12)'; this.style.borderColor='var(--primary)';"
                                        onmouseout="this.style.background='rgba(212,175,55,0.06)'; this.style.borderColor='rgba(212,175,55,0.4)';">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="flex-shrink:0;"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                    <span><?= e($promo['code']) ?></span>
                                    <span style="font-weight:800; background:var(--primary); color:#fff; padding:2px 4px; border-radius:3px; font-size:9px;">-<?= $promo['discount_pct'] ?>%</span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Vous avez un code promo ? Saisissez-le ci-dessus.</div>
                    <?php endif; ?>
                </div>
                <div id="promoMsg" style="font-size:12px;margin-top:6px;"></div>
                <?php endif; ?>
            </div>
            <div id="promoDiscountRow" style="display:<?= !empty($_SESSION['promo'])?'flex':'none' ?>;justify-content:space-between;margin-bottom:8px;color:var(--green);font-size:14px;font-weight:600;">
                <span>Réduction (<?= !empty($_SESSION['promo'])?$_SESSION['promo']['pct']:0 ?>%)</span>
                <span id="promoDiscountAmt">-<?= !empty($_SESSION['promo'])? number_format($subtotal * $_SESSION['promo']['pct'] / 100, 2) : '0.00' ?> MAD</span>
            </div>

            <a href="checkout.php" class="btn-checkout" style="margin-top:14px">Passer la commande</a>
            
            <!-- WhatsApp checkout express button -->
            <button onclick="orderCartViaWhatsApp()" style="display:block; width:100%; padding:16px; background:#25D366; color:#fff !important; text-align:center; font-weight:800; font-size:14px; border-radius:10px; transition:all 0.2s; border:none; cursor:pointer; margin-top:10px; display:flex; align-items:center; justify-content:center; gap:8px;" onmouseover="this.style.background='#20ba59'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#25D366'; this.style.transform='translateY(0)';">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 001.37 5.084L2 22l5.094-1.333a9.96 9.96 0 004.917 1.294h.005c5.507 0 9.99-4.478 9.99-9.986 0-2.67-1.037-5.178-2.92-7.062A9.925 9.925 0 0012.012 2zm5.859 13.987c-.242.684-1.2 1.252-1.644 1.3-1.12.124-2.527-.272-4.072-1.002-3.155-1.488-5.132-4.664-5.289-4.877-.158-.213-1.28-1.702-1.28-3.245 0-1.542.809-2.3 1.099-2.6.29-.3.636-.376.848-.376h.606c.218 0 .497-.082.775.596.284.696.97 2.37.103 2.545-.866.175-.727.562-.164 1.134.424.431.848.862 1.488 1.488.727.726 1.345 1.09 2.053 1.45.65.334.887.218 1.218-.164.33-.382 1.428-1.666 1.808-2.246.381-.58.763-.48 1.28-.272.515.207 3.284 1.548 3.513 1.666.23.118.382.176.438.272.057.098.057.562-.185 1.246z"/></svg>
                Acheter via WhatsApp
            </button>
            <div style="display:flex;justify-content:center;gap:16px;margin-top:14px;flex-wrap:wrap">
                <span style="font-size:11.5px;color:var(--muted)">Paiement securise</span>
                <span style="font-size:11.5px;color:var(--muted)">Livraison rapide</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let prevCartQty = <?= $totalQty ?>;

function triggerConfetti() {
    const canvas = document.createElement('canvas');
    canvas.style.position = 'fixed';
    canvas.style.inset = '0';
    canvas.style.width = '100vw';
    canvas.style.height = '100vh';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '99999';
    document.body.appendChild(canvas);
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const colors = ['#d4a373', '#a24f2b', '#eab308', '#25D366', '#3b82f6'];
    const particles = [];
    
    for (let i = 0; i < 80; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            r: Math.random() * 6 + 4,
            d: Math.random() * canvas.height,
            color: colors[Math.floor(Math.random() * colors.length)],
            tilt: Math.random() * 10 - 5,
            tiltAngleIncremental: Math.random() * 0.07 + 0.02,
            tiltAngle: 0
        });
    }
    
    let animationFrame;
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        let remaining = false;
        particles.forEach((p, idx) => {
            p.tiltAngle += p.tiltAngleIncremental;
            p.y += (Math.cos(p.d) + 3 + p.r / 2) / 2;
            p.x += Math.sin(p.tiltAngle);
            p.tilt = Math.sin(p.tiltAngle - idx/3) * 15;
            
            if (p.y < canvas.height) {
                remaining = true;
                ctx.beginPath();
                ctx.lineWidth = p.r;
                ctx.strokeStyle = p.color;
                ctx.moveTo(p.x + p.tilt + p.r / 2, p.y);
                ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 2);
                ctx.stroke();
            }
        });
        
        if (remaining) {
            animationFrame = requestAnimationFrame(draw);
        } else {
            document.body.removeChild(canvas);
        }
    }
    draw();
}

function updateCartBanner(totalQty, freeBooks) {
    const zone = document.getElementById('cartBannerZone');
    if (!zone) return;
    let html = '';
    if (freeBooks >= 4) {
        html = `<div class="free-books-banner" style="margin-bottom:24px">Vous avez droit a <strong>4 mangas gratuits</strong> + livraison offerte. Ajoutez-les simplement au panier.</div>`;
    } else if (freeBooks === 2) {
        html = `<div class="free-books-banner" style="margin-bottom:24px">Vous avez droit a <strong>2 mangas gratuits</strong> + livraison offerte. Ajoutez-les simplement au panier.</div>`;
    } else if (freeBooks === 1) {
        html = `<div class="free-books-banner" style="margin-bottom:24px">Vous avez droit a <strong>1 manga gratuit</strong> + livraison offerte. Ajoutez-les simplement au panier.</div>`;
    } else if (totalQty >= 2) {
        const needed = 3 - totalQty;
        html = `<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 18px;margin-bottom:24px;font-size:13px;color:#92400e">Ajoutez <strong>${needed} manga${needed>1?'s':''}</strong> de plus pour obtenir <strong>1 gratuit</strong> + livraison offerte.<a href="catalogue.php" style="color:var(--red);margin-left:8px;font-weight:500">Continuer les achats</a></div>`;
    } else if (totalQty === 1) {
        html = `<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 18px;margin-bottom:24px;font-size:13px;color:#92400e">Ajoutez <strong>2 mangas</strong> de plus pour obtenir <strong>1 gratuit</strong> + livraison offerte.<a href="catalogue.php" style="color:var(--red);margin-left:8px;font-weight:500">Continuer les achats</a></div>`;
    }
    zone.innerHTML = html;
}

function updatePageSummary(data) {
    const sub  = document.getElementById('cartPageSubtotal');  if(sub)  sub.textContent  = data.subtotal + ' MAD';
    const tot  = document.getElementById('cartPageTotal');     if(tot)  tot.textContent  = data.total    + ' MAD';
    const cnt  = document.getElementById('cartPageCount');     if(cnt)  cnt.textContent  = data.totalQty;
    const ship = document.getElementById('cartPageShipping');
    if (ship) {
        ship.textContent  = data.shipping;
        ship.style.color  = data.shipping === 'Gratuite' ? 'var(--green)' : 'inherit';
    }
    const b = document.getElementById('cartBadge'); if(b) b.textContent = data.cartCount;
    updateCartBanner(data.totalQty, data.freeBooks);
    
    
    const promoPct = <?= !empty($_SESSION['promo']) ? $_SESSION['promo']['pct'] : 0 ?>;
    const subtotalAmt = parseFloat(data.subtotal.replace(/,/g, ''));
    let savings = data.freeBooks * 49.00;
    if (promoPct > 0) {
        savings += (subtotalAmt * promoPct) / 100;
    }
    
    const savingsRow = document.getElementById('savingsCalculatedRow');
    const savingsAmtSpan = document.getElementById('savingsPageTotalAmt');
    if (savingsRow && savingsAmtSpan) {
        if (savings > 0) {
            savingsRow.style.display = 'flex';
            savingsAmtSpan.textContent = '-' + savings.toFixed(2) + ' MAD';
        } else {
            savingsRow.style.display = 'none';
        }
    }
    
    
    updatePageShippingMeter(data.totalQty);
    updatePageCollectorMeter(data.totalQty);
    
    updateCartDrawer();
}

function applyPromo() {
    const code = document.getElementById('promoInput')?.value.trim();
    if (!code) return;
    fetch('actions/promo.php', {method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=apply&code=${encodeURIComponent(code)}`})
    .then(r=>r.json()).then(data=>{
        const msg = document.getElementById('promoMsg');
        if (data.success) {
            document.getElementById('promoForm').style.display = 'none';
            const applied = document.getElementById('promoApplied');
            applied.style.display = 'flex';
            document.getElementById('promoLabel').textContent = ' ' + code.toUpperCase() + ' — -' + data.pct + '%';
            document.getElementById('promoDiscountRow').style.display = 'flex';
            if (msg) msg.textContent = '';
            location.reload();
        } else {
            if (msg) { msg.textContent = data.msg; msg.style.color = 'var(--red)'; }
        }
    });
}
function removePromo() {
    fetch('actions/promo.php', {method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=remove`})
    .then(()=>location.reload());
}

function insertPromoCode(code) {
    const input = document.getElementById('promoInput');
    if (input) {
        input.value = code;
        applyPromo();
    }
}

function cartUpdateQty(pid, delta) {
    fetch('actions/cart.php', {method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=update&product_id=${pid}&delta=${delta}`})
    .then(r=>r.json()).then(data=>{
        if (data.newQty <= 0) {
            document.getElementById('row-'+pid)?.remove();
        } else {
            const q = document.getElementById('qty-'+pid);  if(q) q.textContent = data.newQty;
            const l = document.getElementById('line-'+pid); if(l && data.lineTotal !== undefined) l.textContent = data.lineTotal + ' MAD';
        }
        if (data.totalQty === 0) { location.reload(); return; }
        updatePageSummary(data);
    });
}

function cartRemove(pid) {
    fetch('actions/cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=remove&product_id=${pid}`})
    .then(r=>r.json()).then(data=>{
        document.getElementById('row-'+pid)?.remove();
        if (data.cartCount == 0) { location.reload(); return; }
        updatePageSummary(data);
    });
}

function orderCartViaWhatsApp() {
    let itemsStr = '';
    let rows = document.querySelectorAll('#cartTableBody tr');
    if (!rows.length) return;
    
    rows.forEach((row, index) => {
        let titleEl = row.querySelector('.cart-prod-title');
        let qtyEl = row.querySelector('[id^="qty-"]');
        if (titleEl && qtyEl) {
            let title = titleEl.innerText;
            let qty = qtyEl.innerText;
            itemsStr += `${index + 1}. *${title}* (Qté: ${qty})\n`;
        }
    });
    
    let totalAmt = document.getElementById('cartPageTotal').innerText;
    
    let message = `Bonjour MangaShop !\nJe souhaite passer une commande express pour le panier suivant :\n\n${itemsStr}\n*Montant Total* : ${totalAmt}\n\nMerci de me recontacter pour finaliser la livraison (Paiement à la livraison) !`;
    let encoded = encodeURIComponent(message);
    window.open(`https://wa.me/<?= WHATSAPP_NUMBER ?>?text=${encoded}`, '_blank');
}

function updatePageShippingMeter(qty) {
    const container = document.getElementById('cartPageShippingMeter');
    if (!container) return;
    
    if (qty === 0) {
        container.innerHTML = '';
        return;
    }
    
    if (qty >= 2 && prevCartQty < 2) {
        triggerConfetti();
    }
    prevCartQty = qty;
    
    let percent = qty >= 2 ? 100 : 50;
    let label = qty >= 2 
        ? "Félicitations ! Livraison Gratuite débloquée !" 
        : "Plus qu'1 manga pour débloquer la livraison gratuite !";
    let color = qty >= 2 ? "var(--green)" : "var(--primary)";
    
    container.innerHTML = `
    <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px 16px; transition: all 0.3s;">
       <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:var(--ink); margin-bottom:8px;">
           <span style="font-size:11px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:6px;">${label}</span>
       </div>
       <div style="height:6px; background:var(--bg2); border-radius:var(--radius-full); overflow:hidden; border: 1px solid var(--border);">
           <div style="height:100%; width:${percent}%; background:linear-gradient(90deg, var(--primary), ${color}); border-radius:var(--radius-full); transition:width 0.4s cubic-bezier(0.16, 1, 0.3, 1);"></div>
       </div>
    </div>
    `;
}

function updatePageCollectorMeter(qty) {
    const container = document.getElementById('cartPageCollectorMeter');
    if (!container) return;
    
    if (qty === 0) {
        container.innerHTML = '';
        return;
    }
    
    let percent = 0, label = '';
    if (qty >= 7) {
        percent = 100;
        label = "🎉 Avantage Collectionneur Max débloqué ! (4 mangas OFFERTS)";
    } else if (qty >= 5) {
        percent = ((qty - 5) / 2) * 100;
        let needed = 7 - qty;
        label = `Plus que <strong>${needed}</strong> manga${needed > 1 ? 's' : ''} pour obtenir <strong>4 mangas GRATUITS</strong> !`;
    } else if (qty >= 3) {
        percent = ((qty - 3) / 2) * 100;
        let needed = 5 - qty;
        label = `Plus que <strong>${needed}</strong> manga${needed > 1 ? 's' : ''} pour obtenir <strong>2 mangas GRATUITS</strong> !`;
    } else {
        percent = (qty / 3) * 100;
        let needed = 3 - qty;
        label = `Plus que <strong>${needed}</strong> manga${needed > 1 ? 's' : ''} pour obtenir <strong>1 manga GRATUIT</strong> !`;
    }
    
    container.innerHTML = `
    <div style="background:rgba(212,175,55,0.06); border:1px solid rgba(212,175,55,0.25); border-radius:12px; padding:12px 16px; transition: all 0.3s;">
       <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:var(--ink); margin-bottom:8px;">
           <span style="font-size:11px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:6px;">${label}</span>
       </div>
       <div style="height:6px; background:var(--bg2); border-radius:var(--radius-full); overflow:hidden; border: 1px solid var(--border);">
           <div style="height:100%; width:${percent}%; background:linear-gradient(90deg, var(--primary), var(--gold)); border-radius:var(--radius-full); transition:width 0.4s cubic-bezier(0.16, 1, 0.3, 1);"></div>
       </div>
    </div>
    `;
}
</script>

<?php require_once 'includes/footer.php'; ?>
