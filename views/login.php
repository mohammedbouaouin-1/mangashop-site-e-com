<?php require_once 'includes/header.php'; ?>

<div class="login-page section-container" style="max-width:450px; margin: 80px auto; padding: 40px; background: var(--white); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow-md); color: var(--ink);">
    <div style="text-align:center; margin-bottom:30px;">
        <h1 style="font-family:'Playfair Display',serif; font-size:28px; margin-bottom:10px;">Connectez-vous</h1>
        <p style="color:var(--muted); font-size:14px;">Accédez à vos favoris et vos commandes</p>
    </div>

    <?php if ($error): ?>
        <div style="background:#fff2f2; color:#e63946; padding:12px; border-radius:10px; font-size:13px; margin-bottom:20px; border:1px solid #ffebeb;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php $redir = htmlspecialchars($_GET['redirect'] ?? ''); ?>
    <form method="POST" action="login.php<?= $redir ? '?redirect='.$redir : '' ?>" style="display:flex; flex-direction:column; gap:20px;">
        <?php if ($redir): ?>
        <div style="background:#fff8f3; border:1px solid #e8c9b0; border-radius:10px; padding:12px 16px; font-size:13px; color:#a24f2b; font-weight:600; display:flex; align-items:center; gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Connectez-vous pour accéder à la commande
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Adresse Email</label>
            <input type="email" name="email" required placeholder="exemple@mail.com" style="width:100%; padding:14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink);">
        </div>
        <div class="form-group">
            <label style="display:block; margin-bottom:8px; font-size:13px; font-weight:600;">Mot de passe</label>
            <div style="position:relative; display:flex; align-items:center;">
                <input type="password" name="password" id="loginPasswordInput" required placeholder="••••••••" style="width:100%; padding:14px 44px 14px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; background: var(--bg); color: var(--ink); outline:none; box-sizing:border-box;">
                <button type="button" onclick="togglePassField('loginPasswordInput', this)" style="position:absolute; right:14px; background:none; border:none; color:var(--primary); cursor:pointer; font-size:11px; font-weight:700; text-transform:uppercase; padding:0; display:flex; align-items:center; justify-content:center;" title="Afficher/Masquer le mot de passe">Afficher</button>
            </div>
        </div>
        
        <button type="submit" class="btn-primary" style="padding:16px; border-radius:10px; font-weight:bold; font-size:15px; margin-top:10px;">
            Se connecter
        </button>

        <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:20px;">
            Pas encore de compte ? <a href="register.php" style="color:var(--red); font-weight:bold;">S'inscrire</a>
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
</script>

<?php require_once 'includes/footer.php'; ?>
