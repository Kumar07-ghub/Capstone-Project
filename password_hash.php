<?php
// Your plain text password
$password = 'password';

// Hash the password using PASSWORD_BCRYPT
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Display the hashed password
echo 'Hashed password: ' . $hashedPassword;
?>
