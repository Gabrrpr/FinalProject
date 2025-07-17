<?php
// Secure PDO database connection helper
function get_db() {
    static $pdo;
    if ($pdo) return $pdo;
    $conf = require __DIR__ . '/../config.php';
    $dsn = "mysql:host={$conf['host']};dbname={$conf['dbname']};charset={$conf['charset']}";
    try {
        $pdo = new PDO($dsn, $conf['user'], $conf['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
    }
}
