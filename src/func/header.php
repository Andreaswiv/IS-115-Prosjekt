<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);
} else {
    $username = null;
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/assets/css/headerStyle.css">
<div class="header">
    <p>
        <?php if ($username): ?>
            Velkommen, <strong><?php echo $username; ?></strong>!
        <?php endif; ?>
    </p>
    <div class="image-container">
        <a href="<?php echo BASE_URL; ?>src/forms/form_exisUser.php">
            <img src="<?php echo BASE_URL; ?>public/assets/img/userImage.png" alt="User Image" class="user-image">
        </a>
    </div>
</div>
