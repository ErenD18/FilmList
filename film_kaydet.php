<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['kullanici_adi']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmanız gerekiyor']);
    exit();
}

if(!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] < 1 || $_SESSION['rol_id'] > 3) {
    echo json_encode(['success' => false, 'message' => 'Yetkiniz yok']);
    exit();
}

$host = "localhost";
$dbname = "testdb";
$user = "root";
$pass = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $film_id = isset($_POST['film_id']) ? intval($_POST['film_id']) : 0;
    $durum = isset($_POST['durum']) ? $_POST['durum'] : null;
    $puan = isset($_POST['puan']) ? intval($_POST['puan']) : null;
    $kullanici_id = $_SESSION['user_id'];

    if ($film_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz film']);
        exit();
    }

    $check_film = $db->prepare("SELECT id FROM filmler WHERE id = :film_id");
    $check_film->execute(['film_id' => $film_id]);

    if ($check_film->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Film bulunamadı']);
        exit();
    }

    $check = $db->prepare("SELECT id FROM kullanici_filmler WHERE kullanici_id = :user_id AND film_id = :film_id");
    $check->execute(['user_id' => $kullanici_id, 'film_id' => $film_id]);

    if ($check->rowCount() > 0) {
        $update = $db->prepare("UPDATE kullanici_filmler SET durum = :durum, puan = :puan WHERE kullanici_id = :user_id AND film_id = :film_id");
        $update->execute([
            'durum' => $durum,
            'puan' => $puan,
            'user_id' => $kullanici_id,
            'film_id' => $film_id
        ]);
        echo json_encode(['success' => true, 'message' => 'Film güncellendi']);
    } else {
        $insert = $db->prepare("INSERT INTO kullanici_filmler (kullanici_id, film_id, durum, puan) VALUES (:user_id, :film_id, :durum, :puan)");
        $insert->execute([
            'user_id' => $kullanici_id,
            'film_id' => $film_id,
            'durum' => $durum,
            'puan' => $puan
        ]);
        echo json_encode(['success' => true, 'message' => 'Film kaydedildi']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
}
?>