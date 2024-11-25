<?php
session_start();
// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $username = htmlspecialchars($_SESSION['username']);
    $role = htmlspecialchars($_SESSION['role']);
} else {
    $username = null;
}
?>
<link rel="stylesheet" href="../../public/assets/css/headerStyle.css">
<div class="header">
    <p>
        <?php if ($username): ?>
            Velkommen, <strong><?php echo $username; ?></strong>!
        <?php endif; ?>
    </p>

</div>
