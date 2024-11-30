<?php session_start(); ?>
<div class="header-container">
    <!-- Left-aligned text "educan tutoring" -->
	<div class="logo" style="display: flex; flex-direction: column; align-items: flex-start;">
		<img src="assets\images\logo.png" style="width: 5.6em; height: 1.9em;">
	</div>
    
    <!-- Conditional display for logged-in state -->
    <div class="auth-links">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="logout-link">Logout</a>
        <?php else: ?>
            <a href="login.html" class="login-link">Login</a>
        <?php endif; ?>
    </div>
</div>
