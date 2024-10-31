<?php
session_start();
include 'db.php';

// Form gönderildiyse doğrulama işlemini yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    // Oturumdaki doğrulama kodunu kontrol et
    if ($code == $_SESSION['token']) {
        // Doğrulama başarılı, kullanıcıyı veritabanına kaydedelim
        $username = $_SESSION['username'];
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        $sql = "INSERT INTO USERS (Name, Email, Password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "Doğrulama başarılı! Kayıt tamamlandı.";
            // Kayıt tamamlandıktan sonra oturum bilgilerini temizleyin
            session_unset();
            session_destroy();
        } else {
            echo "Hata: " . $conn->error;
        }
    } else {
        echo "Kod geçersiz. Lütfen doğru kodu girin.";
    }
} else {
    // Form henüz gönderilmediyse doğrulama formunu göster
    echo '
    <form method="POST" action="verify_2fa.php">
        <label for="code">Doğrulama Kodunu Girin:</label>
        <input type="text" name="code" required>
        <button type="submit">Doğrula</button>
    </form>';
}
?>
