<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $errors[] = 'Both username and password are required.';
    } else {
        $db = get_db();
        $stmt = $db->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            // Success: set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin');
            } else {
                header('Location: dashboard');
            }
            exit;
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>
<h2>Login</h2>
<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="POST" action="login">
    <label>Username: <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register">Register here</a>.</p>
<?php
require_once __DIR__ . '/../../templates/footer.php';
