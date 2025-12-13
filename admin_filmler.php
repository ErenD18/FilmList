<?php
session_start();

if(!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit();
}

if(!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] > 2) {
    die('<div style="text-align:center; margin-top:50px; color:white; background:#1a1a1a; padding:100px;">
            <h1>❌ Erişim Engellendi</h1>
            <p>Bu sayfaya erişim yetkiniz yok. Sadece Admin ve Moderatör yetkisi gereklidir.</p>
            <a href="dashboard.php" style="color:#28a745;">Ana Sayfaya Dön</a>
         </div>');
}

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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] == 'ekle') {
    $baslik = trim($_POST['baslik']);
    $aciklama = trim($_POST['aciklama']);
    $resim_yolu = trim($_POST['resim_yolu']);
    $sayfa_adi = trim($_POST['sayfa_adi']);

    $insert = $db->prepare("INSERT INTO filmler (baslik, aciklama, resim_yolu, sayfa_adi) VALUES (:baslik, :aciklama, :resim_yolu, :sayfa_adi)");
    $insert->execute([
        'baslik' => $baslik,
        'aciklama' => $aciklama,
        'resim_yolu' => $resim_yolu,
        'sayfa_adi' => $sayfa_adi
    ]);
    $success_message = "Film başarıyla eklendi!";
}

if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $film_id = intval($_GET['sil']);
    $delete = $db->prepare("DELETE FROM filmler WHERE id = :id");
    $delete->execute(['id' => $film_id]);
    $success_message = "Film silindi!";
}

$filmler = $db->query("SELECT * FROM filmler ORDER BY eklenme_tarihi DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('img/arkaplan.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: white;
            font-family: Verdana;
        }
        .card {
            background: rgba(0,0,0,0.8);
            border: 1px solid #444;
        }
        .table {
            color: white;
        }
        .table thead {
            background: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Film Yönetimi</h1>
        <div>
            <?php if($_SESSION['rol_id'] == 1): ?>
                <a href="kullanici_yonetimi.php" class="btn btn-danger me-2">Kullanıcı Yönetimi</a>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-light">Ana Sayfa</a>
        </div>
    </div>

    <?php if(isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">Yeni Film Ekle</h3>
            <form method="POST">
                <input type="hidden" name="action" value="ekle">
                <div class="mb-3">
                    <label class="form-label">Film Başlığı</label>
                    <input type="text" name="baslik" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea name="aciklama" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Resim Yolu (örn: img/film.jpg)</label>
                    <input type="text" name="resim_yolu" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sayfa Adı (URL için, örn: matrix)</label>
                    <input type="text" name="sayfa_adi" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Film Ekle</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Mevcut Filmler</h3>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Başlık</th>
                        <th>Sayfa Adı</th>
                        <th>Resim</th>
                        <th>Eklenme Tarihi</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($filmler as $film): ?>
                        <tr>
                            <td><?php echo $film['id']; ?></td>
                            <td><?php echo htmlspecialchars($film['baslik']); ?></td>
                            <td><?php echo htmlspecialchars($film['sayfa_adi']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($film['resim_yolu']); ?>" style="height: 60px;"></td>
                            <td><?php echo date('d.m.Y', strtotime($film['eklenme_tarihi'])); ?></td>
                            <td>
                                <a href="?sil=<?php echo $film['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bu filmi silmek istediğinizden emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>