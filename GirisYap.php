<?php
session_start();
$host = "localhost";
$dbname = "testdb";
$user = "root";
$pass = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $query = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $query->execute(["username" => $username]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["kullanici_adi"] = $user["username"];
            $_SESSION["rol_id"] = $user["rol_id"] ?? 3;
            $success = "✓ Giriş başarılı! Yönlendiriliyorsunuz...";
            header("refresh:1;url=index.php");
        } else {
            $error = "Kullanıcı adı veya şifre yanlış!";
        }
    } else {
        $error = "Kullanıcı bulunamadı!";
    }
}

// Kayıt sayfasından gelirse mesaj göster
if (isset($_GET['registered'])) {
    $success = "✓ Kayıt başarılı! Şimdi giriş yapın.";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
</head>
<body style="background: #1a1a1a;">

<form method="POST">
    <div style="display: flex; justify-content: center; flex-direction: column;
                position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                width: 300px;">

        <h1 style="margin-bottom: 20px; color: white;">GİRİŞ YAP</h1>

        <?php if (!empty($error)): ?>
            <p style="color: red; background:rgba(255,0,0,0.1); padding:10px; border-radius:5px;">❌ <?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p style="color: #28a745; background:rgba(40,167,69,0.1); padding:10px; border-radius:5px; font-weight:bold;">
                <?php echo $success; ?>
            </p>
        <?php endif; ?>

        <label for="username" style="color:white;">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="password" style="color:white;">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" style="margin-top: 20px;">Giriş Yap</button>
        
        <a href="index.php?sayfa=KayitOl" style="margin-top:10px; color:#28a745; text-decoration:none; display:block;">Hesabın yok mu? Kayıt Ol</a>
    </div>
</form>

</body>
</html>