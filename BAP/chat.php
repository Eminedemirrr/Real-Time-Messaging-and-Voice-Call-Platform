<?php
session_start();
include 'db.php';

// Giriş yapmış kullanıcıyı ve arkadaş ID'sini kontrol edin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_GET['friend_id'])) {
    header("Location: login.php");
    exit();
}

$friend_id = $_GET['friend_id'];
$username = $_SESSION['username'];

// Giriş yapan kullanıcının ID'sini alın
$user_sql = "SELECT UserID FROM users WHERE Name = '$username'";
$user_result = $conn->query($user_sql);
$user_id = $user_result->fetch_assoc()['UserID'];

// Sohbet edilen kullanıcının adını al
$friend_sql = "SELECT Name FROM users WHERE UserID = '$friend_id'";
$friend_result = $conn->query($friend_sql);
$friend_name = $friend_result->fetch_assoc()['Name'];

// Mesaj gönderme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = $_POST['message'];
    $send_message_sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$user_id', '$friend_id', '$message')";
    $conn->query($send_message_sql);
}

// Mesajları çekme
$chat_sql = "SELECT * FROM messages 
             WHERE (sender_id = '$user_id' AND receiver_id = '$friend_id') 
             OR (sender_id = '$friend_id' AND receiver_id = '$user_id') 
             ORDER BY timestamp ASC";
$chat_result = $conn->query($chat_sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sohbet</title>
    <script>
        // Sayfanın belirli aralıklarla yenilenmesi için JavaScript
        setInterval(function() {
            location.reload();
        }, 50000); // 3 saniyede bir sayfayı yeniler
    </script>
</head>
<body>
    <h2><?php echo htmlspecialchars($friend_name); ?> ile Sohbet Ediyorsunuz</h2>

    <!-- Mesajları Listeleme -->
    <div style="border: 1px solid #ccc; padding: 10px; width: 300px; height: 400px; overflow-y: scroll;">
        <?php
        if ($chat_result->num_rows > 0) {
            while ($chat = $chat_result->fetch_assoc()) {
                if ($chat['sender_id'] == $user_id) {
                    echo "<p><strong>Sen: </strong>" . htmlspecialchars($chat['message']) . " <small>" . $chat['timestamp'] . "</small></p>";
                } else {
                    echo "<p><strong>" . htmlspecialchars($friend_name) . ": </strong>" . htmlspecialchars($chat['message']) . " <small>" . $chat['timestamp'] . "</small></p>";
                }
            }
        } else {
            echo "<p>Henüz mesaj yok.</p>";
        }
        ?>
    </div>

    <!-- Mesaj Gönderme Formu -->
    <form method="POST" action="chat.php?friend_id=<?php echo $friend_id; ?>">
        <textarea name="message" placeholder="Mesajınızı yazın..." required></textarea>
        <button type="submit">Gönder</button>
    </form>
</body>
</html>
