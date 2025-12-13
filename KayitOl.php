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


        $insert = $db->prepare("INSERT INTO users (username, password) VALUES (:u, :p)");
        $insert->execute([
                "u" => $username,
                "p" => $hashed
        ]);


        header("Location: login.php?registered=true");
        exit;
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
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <label style="color:white;">Kullanıcı Adı:</label>
        <input type="text" name="username" required>

        <label style="color:white; margin-top:10px;">Şifre:</label>
        <input type="password" name="password" required>

        <button type="submit" style="margin-top:20px;">Kayıt Ol</button>

        <a href="login.php" style="margin-top:10px; color:white;">Zaten hesabın var mı?</a>
    </div>
</form>

</body>
</html>