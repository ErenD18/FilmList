<?php
session_start();

$host = "localhost";
$dbname = "testdb";
$dbUser = "root";
$dbPass = "";

try {
    $db = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $dbUser,
            $dbPass,
            [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
    );
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası!");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Boş alan bırakmayın!";
    } else {

        $query = $db->prepare("SELECT id, username, password, rol_id FROM users WHERE username = :username LIMIT 1");
        $query->execute([
                "username" => $username
        ]);

        $user = $query->fetch();

        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["kullanici_adi"] = $user["username"];
            $_SESSION["rol_id"] = $user["rol_id"];

            header("Location: index.php");
            exit;

        } else {
            $error = "Kullanıcı adı veya şifre yanlış!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
</head>

<body style="background:#1a1a1a; font-family: Arial, sans-serif;">

<form method="POST">
    <div style="
        display:flex;
        justify-content:center;
        flex-direction:column;
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%, -50%);
        width:300px;
    ">

        <h1 style="margin-bottom:20px; color:white;">GİRİŞ YAP</h1>

        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <label style="color:white;">Kullanıcı Adı</label>
        <input type="text" name="username" required>

        <label style="color:white; margin-top:10px;">Şifre</label>
        <input type="password" name="password" required>

        <button type="submit" style="margin-top:20px; padding:8px;">
            Giriş Yap
        </button>

    </div>
</form>

</body>
</html>
