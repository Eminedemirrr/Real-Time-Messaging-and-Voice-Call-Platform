<form action="login_process.php" method="POST">
    <label for="username">Kullanıcı Adı:</label>
    <input type="text" name="username" required>
    
    <label for="password">Şifre:</label>
    <input type="password" name="password" required>
    
    <button type="submit">Giriş Yap</button>
    <button onclick="window.location.href='register.php';">Hesap Oluştur</button>
</form>

