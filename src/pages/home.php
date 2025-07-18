<?php
// Home page
require_once __DIR__ . '/../../templates/header.php';
?>
<main class="home-main">
    <div class="welcome-box">
        <h1 id="welcome-title"></h1>
        <p class="subtitle">Empowering secure, fair, and easy elections for everyone.</p>
        <div class="home-actions">
            <!-- Use these in your navigation/menu -->
            <a href="/FinalProject/public/login">Login</a>
            <a href="/FinalProject/public/register">Sign Up</a>
        </div>
    </div>
</main>
<script>
// Typing effect for welcome message
const text = "Welcome to the Automated Electronic Voting System";
let i = 0;
function typeWriter() {
    if (i < text.length) {
        document.getElementById("welcome-title").innerHTML += text.charAt(i);
        i++;
        setTimeout(typeWriter, 35);
    }
}
window.onload = typeWriter;
</script>
<?php
require_once __DIR__ . '/../../templates/footer.php';