<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate inputs
    if (!$username || !$password || !in_array($role, ['admin','voter'])) {
        $errors[] = 'All fields are required and role must be valid.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3-30 chars, letters/numbers/underscore only.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        $db = get_db();
        // Check if username exists
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already taken.';
        } else {
            // Hash password
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
            if ($stmt->execute([$username, $hash, $role])) {
                $success = true;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<h2>Register</h2>
<?php if ($success): ?>
    <p style="color:green;">Registration successful! <a href="/login">Login here</a>.</p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="/register">
        <label>Username: <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Role:
            <select name="role" required>
                <option value="voter"<?= (($_POST['role'] ?? '')==='voter')?' selected':''; ?>>Voter</option>
                <option value="admin"<?= (($_POST['role'] ?? '')==='admin')?' selected':''; ?>>Admin</option>
            </select>
        </label><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login">Login here</a>.</p>
<?php endif; ?>
<?php
require_once __DIR__ . '/../../templates/footer.php';
