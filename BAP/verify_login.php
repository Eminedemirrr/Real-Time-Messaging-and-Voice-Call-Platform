<?php
session_start();
include 'db.php';

// Form gönderildiyse doğrulama işlemini yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['code'];

    // Oturumda saklanan doğrulama koduyla karşılaştır
    if (isset($_SESSION['code']) && $entered_code == $_SESSION['code']) {
        echo "Giriş başarılı!"; // Başarılı giriş işlemini burada yapabilirsiniz
        
        // Kullanıcıyı giriş yapmış olarak işaretlemek için bir oturum değişkeni ayarlayın
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_SESSION['username']; // Giriş yapan kullanıcının adını saklayın

        // Giriş işlemi tamamlandığı için geçici oturum verilerini temizleyin
        unset($_SESSION['code']);
        header("Location: homepage.php"); // Giriş sonrası yönlendirilecek sayfa
        exit();
    } else {
        echo "Geçersiz doğrulama kodu. Lütfen doğru kodu girin.";
    }
} else {
    // Form henüz gönderilmediyse doğrulama formunu göster
    echo '
    <form method="POST" action="verify_login.php">
        <label for="code">Doğrulama Kodunu Girin:</label>
        <input type="text" name="code" required>
        <button type="submit">Doğrula</button>
    </form>';
}
?>
