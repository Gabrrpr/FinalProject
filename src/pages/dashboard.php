<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once __DIR__ . '/../../templates/header.php';
?>
<h2>User Dashboard</h2>
<p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! You are logged in as <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>.</p>
<p>Use the navigation to access elections and voting.</p>
<?php
require_once __DIR__ . '/../../templates/footer.php';
