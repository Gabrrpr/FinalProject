<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Electronic Voting System</title>
    <link rel="stylesheet" href="/FinalProject/public/assets/style.css">
</head>
<body>
<header>
    <h1>Automated Electronic Voting System</h1>
    <nav>
        <a href="/FinalProject/public/">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/FinalProject/public/dashboard">Dashboard</a>
            <a href="/FinalProject/public/vote">Vote</a>
            <a href="/FinalProject/public/results">Results</a>
        <?php endif; ?>
        <a href="/FinalProject/public/admin">Admin</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/FinalProject/public/logout">Logout</a>
        <?php else: ?>
            <a href="/FinalProject/public/login">Login</a>
            <a href="/FinalProject/public/register">Sign Up</a>
        <?php endif; ?>
    </nav>
    <hr>
</header>
<main>
