<?php require_once 'includes/header.php'; ?>

<div class="register-page section-container" style="max-width:450px; margin: 80px auto; padding: 40px; background: var(--white); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow-md); color: var(--ink);">
    <div style="text-align:center; margin-bottom:30px;">
        <h1 style="font-family:'Playfair Display',serif; font-size:28px; margin-bottom:10px;">Inscription</h1>
        <p style="color:var(--muted); font-size:14px;">Créez votre compte en quelques secondes</p>
    </div>

    <?php if ($error): ?>
        <div style="background:#fff2f2; color:#e63946; padding:12px; border-radius:10px; font-size:13px; margin-bottom:20px; border:1px solid #ffebeb;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" style="display:flex; flex-direction:column; gap:20px;">
        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Nom complet</label>
            <input type="text" name="name" required placeholder="Ex: Mohammed Bouaouin" style="width:100%; padding:14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink);">
        </div>
        
        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Adresse Email</label>
            <input type="email" name="email" required placeholder="exemple@mail.com" style="width:100%; padding:14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink);">
        </div>

        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Mot de passe</label>
            <div style="position:relative; display:flex; align-items:center;">
                <input type="password" name="password" id="registerPasswordInput" required placeholder="Min. 6 caractères" style="width:100%; padding:14px 44px 14px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink); outline:none; box-sizing:border-box;">
                <button type="button" onclick="togglePassField('registerPasswordInput', this)" style="position:absolute; right:14px; background:none; border:none; color:var(--primary); cursor:pointer; font-size:11px; font-weight:700; text-transform:uppercase; padding:0; display:flex; align-items:center; justify-content:center;" title="Afficher/Masquer le mot de passe">Afficher</button>
            </div>
            
            <div style="margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; margin-bottom: 5px;">
                    <span style="color: var(--muted);">Force du mot de passe :</span>
                    <span id="strengthLabel" style="font-weight: bold; color: var(--muted); transition: color 0.3s ease;">Trop court</span>
                </div>
                <div style="width: 100%; height: 6px; background: var(--border); border-radius: 10px; overflow: hidden; position: relative;">
                    <div id="strengthBar" style="width: 0%; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 10px; background: #e63946;"></div>
                </div>
            </div>
        </div>

        <!-- BUG FIX: champ de confirmation du mot de passe ajouté -->
        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Confirmer le mot de passe</label>
            <div style="position:relative; display:flex; align-items:center;">
                <input type="password" name="password_confirm" id="registerConfirmInput" required placeholder="Répétez votre mot de passe" style="width:100%; padding:14px 44px 14px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink); outline:none; box-sizing:border-box;">
                <button type="button" onclick="togglePassField('registerConfirmInput', this)" style="position:absolute; right:14px; background:none; border:none; color:var(--primary); cursor:pointer; font-size:11px; font-weight:700; text-transform:uppercase; padding:0;" title="Afficher/Masquer">Afficher</button>
            </div>
            <div id="confirmMsg" style="font-size:11px; margin-top:6px; display:none;"></div>
        </div>
        
        <button type="submit" class="btn-primary" style="padding:16px; border-radius:10px; font-weight:bold; font-size:15px; margin-top:10px;">
            S'inscrire
        </button>

        <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:20px;">
            Déjà inscrit ? <a href="login.php" style="color:var(--red); font-weight:bold;">Se connecter</a>
        </p>
    </form>
</div>
<script>
function togglePassField(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Masquer';
    } else {
        input.type = 'password';
        btn.textContent = 'Afficher';
    }
}

document.getElementById('registerPasswordInput').addEventListener('input', function() {
    checkConfirm();
});

document.getElementById('registerConfirmInput')?.addEventListener('input', checkConfirm);

function checkConfirm() {
    const pass    = document.getElementById('registerPasswordInput').value;
    const confirm = document.getElementById('registerConfirmInput').value;
    const msg     = document.getElementById('confirmMsg');
    if (!confirm || !msg) return;
    msg.style.display = 'block';
    if (pass === confirm) {
        msg.textContent = ' Les mots de passe correspondent';
        msg.style.color = '
    } else {
        msg.textContent = ' Les mots de passe ne correspondent pas';
        msg.style.color = '#e63946';
    }
}    const password = this.value;
    const bar = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    
    if (!password) {
        bar.style.width = '0%';
        label.textContent = 'Trop court';
        label.style.color = 'var(--muted)';
        return;
    }
    
    let score = 0;
    
    
    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    
    
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    let strength = 'Faible';
    let color = '#e63946'; 
    let width = '25%';
    
    if (password.length < 6) {
        strength = 'Trop court';
        color = '#e63946';
        width = '15%';
    } else if (score >= 4) {
        strength = 'Excellent';
        color = '#2a9d8f'; 
        width = '100%';
    } else if (score >= 3) {
        strength = 'Fort';
        color = '#4ecdc4'; 
        width = '75%';
    } else if (score >= 2) {
        strength = 'Moyen';
        color = '#f4a261'; 
        width = '50%';
    } else {
        strength = 'Faible';
        color = '#e63946';
        width = '25%';
    }
    
    bar.style.width = width;
    bar.style.backgroundColor = color;
    label.textContent = strength;
    label.style.color = color;
});
</script>

<?php require_once 'includes/footer.php'; ?>
