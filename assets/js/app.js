
'use strict';
let prodQty = 1;


function toggleCart() {
  document.getElementById('cartOverlay').classList.toggle('open');
  document.getElementById('cartDrawer').classList.toggle('open');
  document.body.style.overflow = document.getElementById('cartDrawer').classList.contains('open') ? 'hidden' : '';
}
function openCartDraw() {
  document.getElementById('cartOverlay').classList.add('open');
  document.getElementById('cartDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
}


function addToCart(productId, qty) {
  qty = qty || 1;
  fetch('actions/cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=add&product_id=${productId}&qty=${qty}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const badge = document.getElementById('cartBadge');
      if (badge) badge.textContent = data.cartCount;
      updateCartDrawer();
      openCartDraw();
    }
  })
  .catch(() => showToast('Erreur réseau', 'error'));
}


function buyNow(productId, qty) {
  qty = qty || 1;
  fetch('actions/cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=buy_now&product_id=${productId}&qty=${qty}`
  })
  .then(r => r.json())
  .then(d => { if (d.success) window.location.href = 'checkout.php'; });
}


function removeFromCart(productId) {
  fetch('actions/cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=remove&product_id=${productId}`
  })
  .then(r => r.json())
  .then(data => {
    const badge = document.getElementById('cartBadge');
    if (badge) badge.textContent = data.cartCount;
    updateCartDrawer();
  });
}


function updateQty(productId, delta) {
  fetch('actions/cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=update&product_id=${productId}&delta=${delta}`
  })
  .then(r => r.json())
  .then(data => {
    const badge = document.getElementById('cartBadge');
    if (badge) badge.textContent = data.cartCount;
    updateCartDrawer();
  });
}


function updateCartDrawer() {
  fetch('actions/cart.php?action=get')
  .then(r => r.json())
  .then(data => {
    const el  = document.getElementById('cartItems');
    const sub = document.getElementById('cartSubtotal');
    if (!el) return;

    if (!data.items || data.items.length === 0) {
      el.innerHTML = '<div class="cart-empty">Votre panier est vide</div>';
      if (sub) sub.textContent = '0.00 MAD';
      updateShippingMeter(0);
      return;
    }
    
    updateShippingMeter(data.totalQty);

    const totalQty = data.items.reduce((s, i) => s + i.qty, 0);
    let freeMsg = '';
    if      (totalQty >= 7) freeMsg = 'Vous avez <strong>4 mangas gratuits</strong> + livraison offerte !';
    else if (totalQty >= 5) freeMsg = 'Vous avez <strong>2 mangas gratuits</strong> + livraison offerte !';
    else if (totalQty >= 3) freeMsg = 'Vous avez <strong>1 manga gratuit</strong> + livraison offerte !';
    else if (totalQty === 2) freeMsg = 'Plus qu\'1 manga pour <strong>1 gratuit</strong> + livraison offerte !';

    let html = freeMsg ? `<div class="cart-free-notice">${freeMsg}</div>` : '';

    data.items.forEach(item => {
      const initial = item.title ? item.title[0].toUpperCase() : '?';
      html += `
      <div class="cart-item">
        <div class="cart-item-img" style="position:relative;width:60px;height:80px;overflow:hidden;border-radius:6px;">
          <div class="cart-item-img-placeholder" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:var(--bg);font-weight:bold;color:var(--muted);">${initial}</div>
          ${item.image_url ? `<img src="${item.image_url}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:2;" onerror="this.style.display='none'">` : ''}
        </div>
        <div class="cart-item-info">
          <div class="cart-item-title">${item.title}</div>
          <div class="cart-item-price">${item.price} MAD</div>
          <div class="cart-item-qty">
            <button class="qty-btn" onclick="updateQty('${item.id}',-1)">&#8722;</button>
            <span>${item.qty}</span>
            <button class="qty-btn" onclick="updateQty('${item.id}',1)">+</button>
          </div>
        </div>
        <button class="cart-item-remove" onclick="removeFromCart('${item.id}')" title="Retirer">&times;</button>
      </div>`;
    });

    el.innerHTML = html;
    if (sub) sub.textContent = data.subtotal + ' MAD';
  });
}


function changeProductQty(delta) {
  prodQty = Math.max(1, prodQty + delta);
  const el = document.getElementById('prodQtyDisplay');
  if (el) el.value = prodQty;
}


function toggleWishlist(productId, btn) {
  fetch('actions/wishlist.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `product_id=${productId}`
  })
  .then(r => r.json())
  .then(data => {
    if (btn) btn.style.color = data.inWishlist ? '#e63946' : '';
    showToast(data.inWishlist ? 'Ajouté aux favoris' : 'Retiré des favoris', 'success');
  });
}


function subscribeNewsletter(e) {
  e && e.preventDefault();
  const email = document.getElementById('newsletterEmail')?.value.trim();
  if (!email) return;
  fetch('actions/newsletter.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `email=${encodeURIComponent(email)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const input = document.getElementById('newsletterEmail');
      if (input) input.value = '';
    }
    showToast(data.success ? 'Merci pour votre inscription !' : (data.message || 'Erreur'), data.success ? 'success' : 'error');
  })
  .catch(() => showToast('Erreur réseau', 'error'));
}


function showToast(msg, type = '') {
  let t = document.getElementById('globalToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'globalToast';
    t.className = 'toast';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.className = 'toast' + (type ? ' ' + type : '');
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 2800);
}


document.addEventListener('DOMContentLoaded', () => {
  
  document.getElementById('cartOverlay')?.addEventListener('click', toggleCart);
  
  updateCartDrawer();
  
  
  document.querySelectorAll('[data-rating]').forEach(el => {
    const r = parseFloat(el.dataset.rating);
    let s = '';
    for (let i = 1; i <= 5; i++) s += i <= Math.round(r) ? '&#9733;' : '&#9734;';
    el.innerHTML = s;
    el.style.cssText = 'color:var(--gold);letter-spacing:1px;font-size:11px';
  });
});

function updateShippingMeter(qty) {
  const container = document.getElementById('drawerShippingMeter');
  if (!container) return;
  
  if (qty === 0) {
    container.innerHTML = '';
    return;
  }
  
  let percent = qty >= 2 ? 100 : 50;
  let label = qty >= 2 
    ? "Félicitations ! Livraison Gratuite débloquée ! " 
    : "Plus qu'1 manga pour débloquer la livraison gratuite ! ";
  let color = qty >= 2 ? "var(--green)" : "var(--primary)";
  
  container.innerHTML = `
  <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px 16px; margin-bottom: 8px; transition: all 0.3s;">
     <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:var(--ink); margin-bottom:8px;">
         <span style="font-size:11px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:6px;"> ${label}</span>
     </div>
     <div style="height:6px; background:var(--bg2); border-radius:var(--radius-full); overflow:hidden; border: 1px solid var(--border);">
         <div style="height:100%; width:${percent}%; background:linear-gradient(90deg, var(--primary), ${color}); border-radius:var(--radius-full); transition:width 0.4s cubic-bezier(0.16, 1, 0.3, 1);"></div>
     </div>
  </div>
  `;
}

