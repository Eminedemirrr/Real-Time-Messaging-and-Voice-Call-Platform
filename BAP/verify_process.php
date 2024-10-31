<?php
include 'db.php';

$code = $_POST['code'];

$sql = "SELECT * FROM USERS WHERE Token='$code'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Doğrulama başarılı!"; // Ana sayfaya yönlendirin
} else {
    echo "Kod geçersiz.";
}
?>
