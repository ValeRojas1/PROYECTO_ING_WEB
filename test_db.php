<?php
require_once dirname(__FILE__) . '/config/database.php';

$stmt = $conn->prepare("SELECT * FROM usuarios");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Email: " . $row['email'] . "\n";
    echo "Password Hash: " . $row['password'] . "\n";
    echo "Hash verification '123456': " . (password_verify('123456', $row['password']) ? 'TRUE' : 'FALSE') . "\n";
    echo "-------------------\n";
}
?>
