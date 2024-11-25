<?php
$password = 'adminpassword';
$hashedPassword = '$2y$10$JeOfQb9qKcZbqsuAQzccyu51POISDoY6PKUKJrv/BGZ3dTYlvCEJq'; // Kopier hashet passord fra databasen

if (password_verify($password, $hashedPassword)) {
    echo "Password verification succeeded.";
} else {
    echo "Password verification failed.";
}
?>
