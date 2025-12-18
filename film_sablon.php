<?php

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

$film_query = $db->prepare("SELECT * FROM filmler WHERE sayfa_adi = :sayfa_adi");
$film_query->execute(['sayfa_adi' => $sayfa]);
$film = $film_query->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    echo '<p class="fs-3">Film bulunamadı!</p>';
    exit();
}

$kullanici_durum = null;
$kullanici_puan = null;

if(isset($_SESSION['user_id'])) {
    $kullanici_query = $db->prepare("SELECT durum, puan FROM kullanici_filmler WHERE kullanici_id = :user_id AND film_id = :film_id");
    $kullanici_query->execute([
        'user_id' => $_SESSION['user_id'],
        'film_id' => $film['id']
    ]);
    $kullanici_data = $kullanici_query->fetch(PDO::FETCH_ASSOC);

    if ($kullanici_data) {
        $kullanici_durum = $kullanici_data['durum'];
        $kullanici_puan = $kullanici_data['puan'];
    }
}
?>

    <div class="text-center my-4">
        <img src="<?php echo htmlspecialchars($film['resim_yolu']); ?>" class="img-fluid rounded shadow" style="max-width: 250px;">
    </div>

    <h1 class="text-center fw-bold" style="color: white; font-size: 55px;">
        <?php echo htmlspecialchars($film['baslik']); ?>
    </h1>

    <style>
        .d-flex.justify-content-center.align-items-center.flex-wrap {
            flex-direction: column;
            align-items: center;
        }
    </style>

<?php if(isset($_SESSION['user_id'])): ?>
    <div class="d-flex justify-content-center align-items-center flex-wrap my-4">
        <div class="d-flex align-items-center me-3 mb-2">
            <label for="durum" class="me-2 fw-bold" style="color: white;">DURUM</label>
            <select name="durum" id="durum" class="form-select form-select-sm" style="width: 180px;">
                <option value="">Seçiniz</option>
                <option value="izledim" <?php echo ($kullanici_durum == 'izledim') ? 'selected' : ''; ?>>İzledim</option>
                <option value="izlemeyi dusunuyorum" <?php echo ($kullanici_durum == 'izlemeyi dusunuyorum') ? 'selected' : ''; ?>>İzlemeyi Düşünüyorum</option>
            </select>
        </div>

        <div class="d-flex align-items-center mb-2">
            <label for="puan" class="me-2 fw-bold" style="color: white;">PUAN</label>
            <select name="puan" id="puan" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Seçiniz</option>
                <?php for($i = 1; $i <= 10; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($kullanici_puan == $i) ? 'selected' : ''; ?>>
                        ★ <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="text-center mb-3">
        <button id="kaydetBtn" class="btn btn-success">Kaydet</button>
        <span id="mesaj" style="color: #28a745; margin-left: 10px;"></span>
    </div>

    <script>
        const filmId = <?php echo $film['id']; ?>;
        const durumSelect = document.getElementById('durum');
        const puanSelect = document.getElementById('puan');
        const kaydetBtn = document.getElementById('kaydetBtn');
        const mesaj = document.getElementById('mesaj');

        kaydetBtn.addEventListener('click', function() {
            const durum = durumSelect.value;
            const puan = puanSelect.value;

            fetch('film_kaydet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `film_id=${filmId}&durum=${durum}&puan=${puan}`
            })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        mesaj.textContent = '✓ ' + data.message;
                        setTimeout(() => {
                            mesaj.textContent = '';
                        }, 3000);
                    } else {
                        mesaj.style.color = 'red';
                        mesaj.textContent = '✗ ' + data.message;
                    }
                })
                .catch(error => {
                    mesaj.style.color = 'red';
                    mesaj.textContent = '✗ Bir hata oluştu';
                });
        });
    </script>
<?php endif; ?>

    <p class="text-center mx-auto" style="color: white; max-width: 900px; font-size: 25px; line-height: 1.6;">
        <?php echo nl2br(htmlspecialchars($film['aciklama'])); ?>
    </p><?php