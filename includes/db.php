<?php
$host = 'sql104.infinityfree.com';
$user = 'if0_39505851';
$pass = 'AKtbcpqaxf';
$dbname = 'if0_39505851_grocery_store';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>