<?php
// Logout logic
session_start();
session_destroy();
header('Location: /FinalProject/public/login');
exit;
