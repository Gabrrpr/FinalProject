<?php
// Logout logic
session_start();
session_destroy();
header('Location: /');
exit;
