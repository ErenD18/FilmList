<?php
session_start();

if(!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit();
}

$title = "Dashboard - FilmList";
$h1 = "FILMLIST";
$sayfa = isset($_GET['sayfa']) ? ($_GET['sayfa']) : 'anasayfa';
$h1_boyut = ($sayfa == 'anasayfa') ? '80px' : '60px';

$host = "localhost";
$dbname = "testdb";
$user = "root";
$pass = "mysql378";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('img/arkaplan.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Verdana;
            color: white;
        }
        h1 {
            font-family: Verdana;
            color: white;
            font-weight: bold;
        }
        h2{
            font-family: Verdana;
            color: white;
        }
        p {
            font-family: Verdana;
            color: white;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 40px !important;
            }
            p {
                font-size: 24px !important;
            }
        }
        @media (max-width: 480px) {
            h1 {
                font-size: 30px !important;
            }
            p {
                font-size: 18px !important;
            }
        }
        .butonstili {
            font-weight: bold;
            font-family: Verdana;
            color: black;
            font-size: 10.5px
        }
        .profil-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-family: Verdana;
            font-size: 10.5px;
        }
    </style>
</head>

<body>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-end">
        <h1 style="position: absolute; left: 5px; font-size: <?php echo $h1_boyut; ?>"><?php echo $h1?></h1>

        <?php
        if(isset($_SESSION['rol_id']) && $_SESSION['rol_id'] <= 2):
            ?>
            <a href="admin_filmler.php" class="btn btn-warning butonstili me-2">FİLM YÖNETİMİ</a>
        <?php endif; ?>

        <?php
        if(isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1):
            ?>
            <a href="kullanici_yonetimi.php" class="btn btn-danger butonstili me-2">KULLANICI YÖNETİMİ</a>
        <?php endif; ?>

        <span class="profil-badge me-2">👤 <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?></span>
        <a href="cikis.php" class="btn btn-light butonstili">ÇIKIŞ YAP</a>
    </div>
</div>
<div class="container text-center">
    <?php
    if ($sayfa == 'anasayfa') {
        $filmler = $db->query("SELECT * FROM filmler ORDER BY eklenme_tarihi DESC")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <p class="fs-1">Haftanın Filmleri</p>
        <div class="row justify-content-center gy-4">
            <?php foreach($filmler as $film): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="dashboard.php?sayfa=<?php echo htmlspecialchars($film['sayfa_adi']); ?>">
                        <img src="<?php echo htmlspecialchars($film['resim_yolu']); ?>"
                             class="img-fluid rounded shadow"
                             alt="<?php echo htmlspecialchars($film['baslik']); ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        $film_query = $db->prepare("SELECT * FROM filmler WHERE sayfa_adi = :sayfa_adi");
        $film_query->execute(['sayfa_adi' => $sayfa]);
        $film_varmi = $film_query->fetch(PDO::FETCH_ASSOC);

        if ($film_varmi) {
            require 'film_sablon.php';
        } else {
            ?>
            <p class="fs-3">Sayfa bulunamadı!</p>
            <?php
        }
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>