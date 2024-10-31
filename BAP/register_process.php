<?php
session_start(); // Oturum başlat
include 'db.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$code = rand(100000, 999999); // 6 haneli doğrulama kodu

// Kullanıcı bilgilerini geçici olarak oturumda sakla
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
$_SESSION['password'] = $password;
$_SESSION['token'] = $code; // Doğrulama kodunu oturuma kaydet

// PHPMailer ile e-posta gönderme işlemi
$mail = new PHPMailer(true);
try {
    // SMTP ayarları
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sensozeren@gmail.com';
    $mail->Password = 'ulqbhezwmxjeszuk';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // SMTP seçenekleri (SSL doğrulama sorunları için)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Gönderen ve alıcı bilgileri
    $mail->setFrom('sensozeren@gmail.com', 'G12 message');
    $mail->addAddress($email, $username);

    // E-posta içeriği
    $mail->isHTML(true);
    $mail->Subject = 'İki Faktörlü Doğrulama Kodu';
    $mail->Body    = "Merhaba $username, doğrulama kodunuz: <b>$code</b>";

    $mail->send();
    echo "Kayıt başarılı! Lütfen iki faktörlü doğrulama kodunu girin.";
    header("Location: verify_2fa.php"); // Doğrulama sayfasına yönlendirme
} catch (Exception $e) {
    echo "Doğrulama kodu gönderilemedi. Hata: {$mail->ErrorInfo}";
}
?>
