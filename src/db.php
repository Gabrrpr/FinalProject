<?php
// Secure PDO database connection helper
require_once __DIR__ . '/../config.php';
function get_db() {
    global $db;
    return $db;
}
