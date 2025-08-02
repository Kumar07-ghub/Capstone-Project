<<<<<<< HEAD
<?php
// Your plain text password
$password = 'Admin@123password';

// Hash the password using PASSWORD_BCRYPT
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Display the hashed password
echo 'Hashed password: ' . $hashedPassword;
?>
=======
<?php
// Your plain text password
$password = 'password';

// Hash the password using PASSWORD_BCRYPT
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Display the hashed password
echo 'Hashed password: ' . $hashedPassword;
?>
>>>>>>> 07d8e6ccfd579de17e14e08d58f8a836c66fbf26
