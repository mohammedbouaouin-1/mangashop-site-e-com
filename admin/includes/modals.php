
<style>
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(42, 30, 26, 0.4);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-content {
    background: var(--white);
    border-radius: 24px;
    width: 100%;
    max-width: 650px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 30px 60px -12px rgba(42, 30, 26, 0.25);
    border: 1px solid var(--border);
    animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(20px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.modal-head {
    padding: 32px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: var(--white);
    z-index: 10;
}
.modal-title { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 900; color: var(--ink); }
.modal-body { padding: 32px; }
.modal-footer { padding: 24px 32px; border-top: 1px solid var(--border); display: flex; gap: 12px; justify-content: flex-end; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--ink); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    outline: none;
    transition: 0.2s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); background: var(--white); outline: none; }

.btn { padding: 14px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; border: none; font-size: 14px; }
.btn-primary { background: var(--ink); color: #fff; }
.btn-primary:hover { background: var(--primary); transform: translateY(-2px); }
.btn-ghost { background: var(--bg); color: var(--ink-soft); }
</style>


<div id="productModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-head">
      <h2 class="modal-title" id="pModalTitle">Nouveau Produit</h2>
      <button onclick="closeModal('productModal')" style="background:none; border:none; cursor:pointer; color:var(--muted);"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save_product">
      <input type="hidden" name="product_id" id="p_id">
      <div class="modal-body">
        <div class="form-group">
          <label>Titre du Manga *</label>
          <input type="text" name="title" id="p_title" required placeholder="Ex: One Piece Vol. 104">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Rayon (Catégorie) *</label>
            <select name="category_id" id="p_category" required>
              <?php foreach($db->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll() as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Auteur</label>
            <input type="text" name="author" id="p_author" placeholder="Ex: Eiichiro Oda">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Prix (MAD) *</label>
            <input type="number" name="price" id="p_price" step="0.01" required placeholder="99.00">
          </div>
          <div class="form-group">
            <label>Ancien Prix (Promo)</label>
            <input type="number" name="old_price" id="p_old_price" step="0.01" placeholder="129.00">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" id="p_stock" value="100">
          </div>
          <div class="form-group">
            <label>Badge</label>
            <select name="badge" id="p_badge">
              <option value="">Aucun</option>
              <option value="new">Nouveau</option>
              <option value="best">Best-seller</option>
              <option value="hot">Populaire</option>
              <option value="sale">Promotion</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>URL de l'image de couverture</label>
          <input type="text" name="image_url" id="p_image" placeholder="assets/images/covers/nom.jpg">
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="p_desc" rows="4" placeholder="Résumé de l'histoire..."></textarea>
        </div>
        <div style="display:flex;gap:24px;margin-top:8px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600;color:var(--ink);">
            <input type="checkbox" name="featured" id="p_featured" style="width:16px;height:16px;"> Mis en avant (Best-seller)
          </label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600;color:var(--ink);">
            <input type="checkbox" name="is_new" id="p_is_new" style="width:16px;height:16px;"> Nouvelle sortie
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('productModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer le Produit</button>
      </div>
    </form>
  </div>
</div>


<div id="categoryModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-head">
      <h2 class="modal-title" id="cModalTitle">Nouvelle Catégorie</h2>
      <button onclick="closeModal('categoryModal')" style="background:none; border:none; cursor:pointer; color:var(--muted);"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save_category">
      <input type="hidden" name="category_id" id="c_id">
      <div class="modal-body">
        <div class="form-group">
          <label>Nom du Rayon *</label>
          <input type="text" name="name" id="c_name" required placeholder="Ex: Seinen">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Icône (Émoji)</label>
            <input type="text" name="icon" id="c_icon" placeholder="" maxlength="10">
          </div>
          <div class="form-group">
            <label>Couleur de fond (Hex)</label>
            <input type="color" name="color" id="c_color" value="#f1ede6">
          </div>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="c_desc" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('categoryModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer la Catégorie</button>
      </div>
    </form>
  </div>
</div>


<div id="userModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-head">
      <h2 class="modal-title" id="uModalTitle">Nouvel Utilisateur</h2>
      <button onclick="closeModal('userModal')" style="background:none; border:none; cursor:pointer; color:var(--muted);"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save_user">
      <input type="hidden" name="user_id" id="u_id">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label>Nom complet *</label>
            <input type="text" name="name" id="u_name" required placeholder="Ex: Ahmed Alami">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" id="u_email" required placeholder="Ex: ahmed@exemple.com">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" id="u_pass" placeholder="Laissez vide pour conserver">
          </div>
          <div class="form-group">
            <label>Rôle</label>
            <select name="role" id="u_role">
              <option value="user">Utilisateur / Client</option>
              <option value="admin">Administrateur</option>
              <option value="livreur">Livreur (Coursier)</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Téléphone</label>
          <input type="tel" name="phone" id="u_phone" placeholder="06 00 00 00 00">
        </div>
        <div class="form-group">
          <label>Adresse complète</label>
          <textarea name="address" id="u_address" rows="2"></textarea>
        </div>
        <div class="form-group">
          <label>Ville</label>
          <select name="city" id="u_city">
             <option value="">Choisir...</option>
             <?php foreach(['Casablanca','Rabat','Marrakech','Fès','Tanger','Agadir','Meknès'] as $v): ?>
                <option value="<?= $v ?>"><?= $v ?></option>
             <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('userModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer l'utilisateur</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) { 
    // Reset specific titles if opening for a new entry
    if(id === 'productModal') document.getElementById('pModalTitle').innerText = 'Nouveau Produit';
    if(id === 'categoryModal') document.getElementById('cModalTitle').innerText = 'Nouvelle Catégorie';
    if(id === 'userModal') document.getElementById('uModalTitle').innerText = 'Nouvel Utilisateur';
    
    // Clear IDs and secret fields
    if(id === 'productModal') document.getElementById('p_id').value = '';
    if(id === 'categoryModal') document.getElementById('c_id').value = '';
    if(id === 'bundleModal') {
        document.getElementById('bModalTitle').innerText = 'Nouveau Bundle';
        document.getElementById('b_id').value = '';
        ['b_name','b_price','b_old_price','b_image','b_desc'].forEach(fid => {
            const el = document.getElementById(fid);
            if(el) el.value = '';
        });
    }
    if(id === 'userModal') {
        document.getElementById('u_id').value = '';
        document.getElementById('u_pass').placeholder = 'Mot de passe requis';
        document.getElementById('u_pass').required = true;
    }
    
    document.getElementById(id).style.display = 'flex'; 
}
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function editProduct(p) {
    document.getElementById('pModalTitle').innerText = 'Modifier le Produit';
    document.getElementById('p_id').value = p.id;
    document.getElementById('p_title').value = p.title;
    document.getElementById('p_category').value = p.category_id;
    document.getElementById('p_author').value = p.author;
    document.getElementById('p_price').value = p.price;
    document.getElementById('p_old_price').value = p.old_price || '';
    document.getElementById('p_stock').value = p.stock || 0;
    document.getElementById('p_badge').value = p.badge || '';
    document.getElementById('p_image').value = p.image_url;
    document.getElementById('p_desc').value = p.description || '';
    document.getElementById('p_featured').checked = !!parseInt(p.featured);
    document.getElementById('p_is_new').checked = !!parseInt(p.is_new);
    openModal('productModal');
}

function editCategory(c) {
    document.getElementById('cModalTitle').innerText = 'Modifier la Catégorie';
    document.getElementById('c_id').value = c.id;
    document.getElementById('c_name').value = c.name;
    document.getElementById('c_icon').value = c.icon;
    document.getElementById('c_color').value = c.color;
    document.getElementById('c_desc').value = c.description || '';
    openModal('categoryModal');
}

function editUser(u) {
    document.getElementById('uModalTitle').innerText = 'Modifier l\'Utilisateur';
    document.getElementById('u_id').value = u.id;
    document.getElementById('u_name').value = u.name;
    document.getElementById('u_email').value = u.email;
    document.getElementById('u_role').value = u.role;
    document.getElementById('u_phone').value = u.phone || '';
    document.getElementById('u_address').value = u.address || '';
    document.getElementById('u_city').value = u.city || '';
    
    const passInput = document.getElementById('u_pass');
    passInput.placeholder = 'Laissez vide pour conserver';
    passInput.required = false;
    passInput.value = '';
    
    openModal('userModal');
}


document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) closeModal(e.target.id);
    });
});
</script>



<div id="bundleModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-head">
      <h2 class="modal-title" id="bModalTitle">Nouveau Bundle</h2>
      <button onclick="closeModal('bundleModal')" style="background:none;border:none;cursor:pointer;color:var(--muted);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save_bundle">
      <input type="hidden" name="bundle_id" id="b_id">
      <div class="modal-body">
        <div class="form-group">
          <label>Nom du Bundle / Pack *</label>
          <input type="text" name="name" id="b_name" required placeholder="Ex: Pack Shonen Essentiel">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Prix (MAD) *</label>
            <input type="number" name="price" id="b_price" step="0.01" required placeholder="299.00">
          </div>
          <div class="form-group">
            <label>Ancien Prix (Promo)</label>
            <input type="number" name="old_price" id="b_old_price" step="0.01" placeholder="399.00">
          </div>
        </div>
        <div class="form-group">
          <label>URL de l'image de couverture</label>
          <input type="text" name="image_url" id="b_image" placeholder="assets/images/covers/bundle-pack-xxx.jpg">
        </div>
        <div class="form-group">
          <label>Description du pack</label>
          <textarea name="description" id="b_desc" rows="4" placeholder="Contenu du pack, avantages, mangas inclus..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('bundleModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer le Bundle</button>
      </div>
    </form>
  </div>
</div>

<script>
function editBundle(b) {
  document.getElementById('bModalTitle').innerText = 'Modifier le Bundle';
  document.getElementById('b_id').value        = b.id;
  document.getElementById('b_name').value      = b.name;
  document.getElementById('b_price').value     = b.price;
  document.getElementById('b_old_price').value = b.old_price || '';
  document.getElementById('b_image').value     = b.image_url || '';
  document.getElementById('b_desc').value      = b.description || '';
  document.getElementById('bundleModal').style.display = 'flex';
}
</script>

<script>
// ── Recherche universelle dans les tableaux ──────────────────
function filterTable(tableId, inputId) {
  const q = document.getElementById(inputId).value.toLowerCase();
  const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = (!q || text.includes(q)) ? '' : 'none';
  });
}
</script>
