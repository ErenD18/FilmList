<?php 
$title = "FilmList"; 
$h1 = "FILMLIST"; 
$sayfa = isset($_GET['sayfa']) ? ($_GET['sayfa']) : 'anasayfa'; 
$dosyaAdi = $sayfa . '.php'; 
$h1_boyut = ($sayfa == 'anasayfa') ? '80px' : '60px';  
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
</style>
</head>

<body>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-end">
        <h1 style="position: absolute; left: 5px"<?php echo $h1_boyut; ?>><?php echo $h1?></h1>
        <a href="index.php?sayfa=KayitOl" class="btn btn-light butonstili me-2">KAYIT OL</a>
        <a href="index.php?sayfa=GirisYap" class="btn btn-light butonstili">GİRİŞ YAP</a>
    </div>
</div>
<div class="container text-center">
<?php 
if ($sayfa == 'anasayfa') { 
?>
    <p class="fs-1">Haftanın Filmleri</p>
    <div class="row justify-content-center gy-4">
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="index.php?sayfa=oldboy">
                <img src="img/oldboy.jpeg" class="img-fluid rounded shadow">
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="index.php?sayfa=askvegurur">
                <img src="img/askvegurur.jpg" class="img-fluid rounded shadow">
            </a>
        </div>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="index.php?sayfa=csm">
                <img src="img/chainsawman.jpg" class="img-fluid rounded shadow">
            </a>
        </div>
    </div>
<?php 
} else {
    if (file_exists($dosyaAdi)) {
        require $dosyaAdi;
    } else { 
?>
<?php 
    } 
} 
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>