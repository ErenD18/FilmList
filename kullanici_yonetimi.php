<?php
session_start();

if(!isset($_SESSION['kullanici_adi']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    die('<div style="text-align:center; margin-top:50px; color:white; background:#1a1a1a; padding:100px;">
            <h1>❌ Erişim Engellendi</h1>
            <p>Bu sayfaya sadece Admin erişebilir.</p>
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

if (isset($_POST['rol_guncelle'])) {
    $user_id = intval($_POST['user_id']);
    $yeni_rol = intval($_POST['yeni_rol']);

    if ($user_id != $_SESSION['user_id']) {
        $update = $db->prepare("UPDATE users SET rol_id = :rol WHERE id = :id");
        $update->execute(['rol' => $yeni_rol, 'id' => $user_id]);
        $success_message = "Kullanıcı rolü güncellendi!";
    } else {
        $error_message = "Kendi rolünüzü değiştiremezsiniz!";
    }
}

if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $user_id = intval($_GET['sil']);

    if ($user_id != $_SESSION['user_id']) {
        $delete = $db->prepare("DELETE FROM users WHERE id = :id");
        $delete->execute(['id' => $user_id]);
        $success_message = "Kullanıcı silindi!";
    } else {
        $error_message = "Kendi hesabınızı silemezsiniz!";
    }
}

$users = $db->query("
    SELECT u.*, r.rol_adi, r.aciklama as rol_aciklama
    FROM users u
    LEFT JOIN roller r ON u.rol_id = r.id
    ORDER BY u.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$roller = $db->query("SELECT * FROM roller")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
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
            background: rgba(0,0,0,0.9);
            border: 1px solid #444;
        }
        .table {
            color: white;
        }
        .table thead {
            background: rgba(255,255,255,0.1);
        }
        .badge-admin { background: #dc3545; }
        .badge-moderator { background: #ffc107; color: #000; }
        .badge-uye { background: #28a745; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 Kullanıcı Yönetimi</h1>
        <div>
            <a href="admin_filmler.php" class="btn btn-warning me-2">Film Yönetimi</a>
            <a href="dashboard.php" class="btn btn-light">Ana Sayfa</a>
        </div>
    </div>

    <?php if(isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if(isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Rol Açıklamaları -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>📋 Rol Yetkileri</h5>
            <ul class="mb-0">
                <li><span class="badge badge-admin">Admin</span> - Tüm yetkiler (Film yönetimi, kullanıcı yönetimi)</li>
                <li><span class="badge badge-moderator">Moderatör</span> - Film ekleme/silme yetkisi</li>
                <li><span class="badge badge-uye">Üye</span> - Film izleme ve puanlama</li>
            </ul>
        </div>
    </div>

    <!-- Kullanıcı Listesi -->
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Kayıtlı Kullanıcılar</h3>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>Mevcut Rol</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($u['username']); ?>
                                <?php if($u['id'] == $_SESSION['user_id']): ?>
                                    <span class="badge bg-info">Siz</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'badge-uye';
                                if($u['rol_id'] == 1) $badge_class = 'badge-admin';
                                elseif($u['rol_id'] == 2) $badge_class = 'badge-moderator';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $u['rol_adi'] ?? 'Üye'; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                echo isset($u['created_at'])
                                    ? date('d.m.Y H:i', strtotime($u['created_at']))
                                    : '-';
                                ?>
                            </td>
                            <td>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <select name="yeni_rol" class="form-select form-select-sm d-inline-block" style="width:auto;">
                                            <?php foreach($roller as $rol): ?>
                                                <option value="<?php echo $rol['id']; ?>"
                                                    <?php echo ($u['rol_id'] == $rol['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $rol['rol_adi']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="rol_guncelle" class="btn btn-primary btn-sm">Güncelle</button>
                                    </form>
                                    <a href="?sil=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm ms-1"
                                       onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
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