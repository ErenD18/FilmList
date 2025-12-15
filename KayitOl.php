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
    die("Veritabanı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $check = $db->prepare("SELECT id FROM users WHERE username = :u");
    $check->execute(["u" => $username]);

    if ($check->rowCount() > 0) {
        $error = "Bu kullanıcı adı zaten alınmış!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insert = $db->prepare("INSERT INTO users (username, password, rol_id) VALUES (:u, :p, 3)");
        $insert->execute([
            "u" => $username,
            "p" => $hashed
        ]);

        $success = "✓ Kayıt başarılı! Giriş yapabilirsiniz.";
        // 2 saniye sonra giriş sayfasına yönlendir
        header("refresh:2;url=index.php?sayfa=GirisYap");
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
</head>
<body style="background: #1a1a1a;">

<form method="POST">
    <div style="display:flex; flex-direction:column; width:300px;
                position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">

        <h1 style="color:white; margin-bottom:20px;">KAYIT OL</h1>

        <?php if (!empty($error)) : ?>
            <p style="color:red; background:rgba(255,0,0,0.1); padding:10px; border-radius:5px;">❌ <?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)) : ?>
            <p style="color:#28a745; background:rgba(40,167,69,0.1); padding:10px; border-radius:5px; font-weight:bold;">
                <?php echo $success; ?>
            </p>
        <?php endif; ?>

        <label style="color:white;">Kullanıcı Adı:</label>
        <input type="text" name="username" required>

        <label style="color:white; margin-top:10px;">Şifre:</label>
        <input type="password" name="password" required>

        <button type="submit" style="margin-top:20px;">Kayıt Ol</button>

        <a href="index.php?sayfa=GirisYap" style="margin-top:10px; color:#28a745; text-decoration:none; display:block;">Zaten hesabın var mı? Giriş Yap</a>
    </div>
</form>

</body>
</html>