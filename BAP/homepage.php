<?php
session_start();
include 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Arkadaşlık isteklerini listeleme
$requests_sql = "SELECT users.Name, friendships.id AS request_id, friendships.user_id FROM users 
                JOIN friendships ON users.UserID = friendships.user_id 
                WHERE friendships.friend_id = (SELECT UserID FROM users WHERE Name = '$username')
                AND friendships.status = 'pending'";
$requests_result = $conn->query($requests_sql);

// Arkadaş listesini çekme
$friends_sql = "SELECT * FROM users 
                JOIN friendships ON users.UserID = friendships.friend_id 
                WHERE friendships.user_id = (SELECT UserID FROM users WHERE Name = '$username')
                AND friendships.status = 'accepted'";
$friends_result = $conn->query($friends_sql);

// Yeni arkadaş ekleme formu gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_friend'])) {
        $new_friend = $_POST['new_friend'];
        $new_friend_id_sql = "SELECT UserID FROM users WHERE Name = '$new_friend'";
        $new_friend_id_result = $conn->query($new_friend_id_sql);

        if ($new_friend_id_result->num_rows > 0) {
            $new_friend_id = $new_friend_id_result->fetch_assoc()['UserID'];
            $user_id_sql = "SELECT UserID FROM users WHERE Name = '$username'";
            $user_id_result = $conn->query($user_id_sql);
            $user_id = $user_id_result->fetch_assoc()['UserID'];

            // Arkadaşlık isteğini gönder
            $add_friend_sql = "INSERT INTO friendships (user_id, friend_id, status) VALUES ('$user_id', '$new_friend_id', 'pending')";
            if ($conn->query($add_friend_sql) === TRUE) {
                echo "Arkadaşlık isteği gönderildi!";
            } else {
                echo "Arkadaş ekleme başarısız: " . $conn->error;
            }
        } else {
            echo "Kullanıcı bulunamadı.";
        }
    }

    // Arkadaşlık isteği kabul edildiyse
    if (isset($_POST['accept_request_id'])) {
        $request_id = $_POST['accept_request_id'];
        $request_user_id = $_POST['request_user_id']; // İsteği gönderenin ID'si

        // Kullanıcının kendi UserID'sini alalım
        $user_id_sql = "SELECT UserID FROM users WHERE Name = '$username'";
        $user_id_result = $conn->query($user_id_sql);
        $user_id = $user_id_result->fetch_assoc()['UserID'];

        // Çift yönlü arkadaşlık ekle
        $accept_sql1 = "UPDATE friendships SET status='accepted' WHERE id='$request_id'";
        $accept_sql2 = "INSERT INTO friendships (user_id, friend_id, status) VALUES ('$user_id', '$request_user_id', 'accepted')";

        if ($conn->query($accept_sql1) === TRUE && $conn->query($accept_sql2) === TRUE) {
            echo "Arkadaşlık isteği kabul edildi!";
        } else {
            echo "Hata: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
</head>
<body>
    <h1>Hoş Geldin, <?php echo htmlspecialchars($username); ?></h1>

    <!-- Arkadaş Ekleme Formu -->
    <form method="POST" action="homepage.php">
        <label for="new_friend">Arkadaş Ekle:</label>
        <input type="text" name="new_friend" required placeholder="Kullanıcı Adı">
        <button type="submit">Ekle</button>
    </form>

    <!-- Gelen Arkadaşlık İstekleri -->
    <h2>Arkadaşlık İstekleri</h2>
    <ul>
        <?php
        if ($requests_result->num_rows > 0) {
            while ($request = $requests_result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($request['Name']) .
                     "<form method='POST' style='display:inline'>
                        <input type='hidden' name='accept_request_id' value='" . $request['request_id'] . "'>
                        <input type='hidden' name='request_user_id' value='" . $request['user_id'] . "'>
                        <button type='submit'>Kabul Et</button>
                      </form>
                    </li>";
            }
        } else {
            echo "<p>Bekleyen arkadaşlık isteğiniz yok.</p>";
        }
        ?>
    </ul>

    <!-- Arkadaş Listesi -->
    <h2>Arkadaşlar</h2>
    <ul>
        <?php
        if ($friends_result->num_rows > 0) {
            while ($friend = $friends_result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($friend['Name']) . 
                     " - <a href='chat.php?friend_id=" . $friend['UserID'] . "'>Sohbet Et</a></li>";
            }
        } else {
            echo "<p>Arkadaşınız yok.</p>";
        }
        ?>
    </ul>
</body>
</html>
