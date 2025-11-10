<?php
include "db.php";

$id = null;
$nama = "";
$nim = "";
$prodi = "";
$alamat = "";
$foto = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id_mahas'];
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id']; 
}

if (!$id) {
    die("Error: ID Mahasiswa tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['name'];
    $nim = $_POST['nim'];
    $prodi = $_POST['prodi'];
    $alamat = $_POST['alamat'];
    $current_image = $_POST['current_image']; 
    
    $img_to_save = $current_image; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; 
        
        $new_filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $img_to_save = $new_filename; 

            if (!empty($current_image) && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
        } else {
            echo "Maaf, terjadi error saat meng-upload file baru.";
        }
    }

    $stmt_update = $conn->prepare("UPDATE mahasiswa SET nama_mahs = ?, nim_mahas = ?, prodi = ?, alamat_mahas = ?, foto_mahas = ? WHERE id_mahas = ?");
    
    $stmt_update->bind_param("sssssi", $nama, $nim, $prodi, $alamat, $img_to_save, $id);

    if ($stmt_update->execute()) {
        header("Location: index.php"); 
        exit();
    } else {
        echo "Error updating record: " . $stmt_update->error;
    }
    $stmt_update->close();
}

$stmt_select = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahas = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
$data = $result->fetch_assoc();

if ($data) {
    $nama = $data['nama_mahs'];
    $nim = $data['nim_mahas'];
    $prodi = $data['prodi'];
    $alamat = $data['alamat_mahas'];
    $foto = $data['foto_mahas']; 
} else {
    echo "Data tidak ditemukan.";
    exit(); 
}

$stmt_select->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 0; 
        }
        .form-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 600px;
        }
        .current-photo {
            max-width: 150px;
            height: auto;
            border-radius: 0.375rem; 
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h3 class="text-center mb-4">Ubah Data Mahasiswa</h3>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_mahas" value="<?= $id ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($foto) ?>">

            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="name" required value="<?= htmlspecialchars($nama) ?>">
            </div>

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" required value="<?= htmlspecialchars($nim) ?>">
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <select class="form-select" name="prodi" id="prodi">
                    <option value="">----pilih prodi-----</option>
                    <option value="teknik_infor" <?php if ($prodi == 'teknik_infor') echo 'selected'; ?>>Teknik Informatika</option>
                    <option value="teknik_mekatro" <?php if ($prodi == 'teknik_mekatro') echo 'selected'; ?>>Teknik Mekatronika</option>
                    <option value="teknik_industri" <?php if ($prodi == 'teknik_industri') echo 'selected'; ?>>Teknik Industri</option>
                    <option value="sistem_informasi" <?php if ($prodi == 'sistem_informasi') echo 'selected'; ?>>Sistem Informasi</option>
                    <option value="teknik_elektro" <?php if ($prodi == 'teknik_elektro') echo 'selected'; ?>>Teknik Elektro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="alamat" rows="3"><?= htmlspecialchars($alamat) ?></textarea>
            </div>

            <?php if (!empty($foto)): ?>
                <div class="mb-3">
                    <label class="form-label">Foto Saat Ini:</label><br>
                    <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($nama) ?>" class="current-photo">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="image" class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" class="form-control" name="image" id="image">
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>