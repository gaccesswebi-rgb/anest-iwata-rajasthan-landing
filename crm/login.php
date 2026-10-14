<?php
require __DIR__ . '/auth.php';

$error = '';
$hash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $pass = (string)($_POST['password'] ?? '');
    if (CRM_PASSWORD_HASH === '') {
        // Setup helper: only displays a hash for the owner to paste into config.php; stores nothing.
        if (strlen($pass) >= 8) $hash = password_hash($pass, PASSWORD_DEFAULT);
        else $error = 'Use at least 8 characters.';
    } elseif (hash_equals(CRM_USER, (string)($_POST['user'] ?? '')) && password_verify($pass, CRM_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['crm_user'] = CRM_USER;
        header('Location: ./');
        exit;
    } else {
        sleep(2); // slows down password guessing
        $error = 'Wrong username or password.';
    }
}

crm_header('Login');
?>
<div class="login">
    <h1>Leads CRM</h1>
    <?php if (CRM_PASSWORD_HASH === ''): ?>
        <p class="muted">First-time setup: choose a password. Copy the generated hash into <code>CRM_PASSWORD_HASH</code> in <code>config.local.php</code>, then reload this page.</p>
        <?php if ($hash): ?><textarea class="hash" readonly onclick="this.select()"><?= h($hash) ?></textarea><?php endif; ?>
    <?php endif; ?>
    <?php if ($error): ?><p class="alert"><?= h($error) ?></p><?php endif; ?>
    <form method="post" class="stack">
        <?= csrf_field() ?>
        <?php if (CRM_PASSWORD_HASH !== ''): ?>
            <label>Username <input name="user" autocomplete="username" required></label>
        <?php endif; ?>
        <label>Password <input name="password" type="password" autocomplete="<?= CRM_PASSWORD_HASH === '' ? 'new-password' : 'current-password' ?>" required></label>
        <button class="btn"><?= CRM_PASSWORD_HASH === '' ? 'Generate hash' : 'Login' ?></button>
    </form>
</div>
<?php crm_footer();
