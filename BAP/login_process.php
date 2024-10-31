<?php
session_start();
include 'db.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanında kontrol edin
    $sql = "SELECT * FROM users WHERE Name='$username' AND Password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $email = $user['Email'];
        $code = rand(100000, 999999); // 6 haneli doğrulama kodu

        // Doğrulama kodunu oturuma kaydet
        $_SESSION['username'] = $username;
        $_SESSION['code'] = $code;
        $_SESSION['email'] = $email;

        // PHPMailer ile e-posta gönderme işlemi
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sensozeren@gmail.com';
            $mail->Password = 'ulqbhezwmxjeszuk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sensozeren@gmail.com', 'Giriş Doğrulama');
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Giriş Doğrulama Kodu';
            $mail->Body    = "Merhaba $username, giriş yapabilmeniz için doğrulama kodunuz: <b>$code</b>";

            $mail->send();
            header("Location: verify_login.php"); // Doğrulama sayfasına yönlendirin
            exit();
        } catch (Exception $e) {
            echo "Doğrulama kodu gönderilemedi. Hata: {$mail->ErrorInfo}";
        }
    } else {
        echo "Kullanıcı adı veya şifre yanlış.";
    }
} else {
    echo "Kullanıcı adı veya şifre değeri gönderilmedi.";
}
?>
