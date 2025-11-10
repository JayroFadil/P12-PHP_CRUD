<?php
include 'db.php';

$message = ""; // Untuk menyimpan pesan error atau sukses

if (isset($_POST['submit'])) {
    $nama = $_POST['name'];
    $nim = $_POST['nim'];
    $prodi = $_POST['prodi'];
    $alamat = $_POST['alamat']; // Menggunakan 'alamat' sesuai nama form

    // 1. Logika Upload File yang Lebih Baik
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        // Buat nama file unik untuk menghindari tabrakan
        $new_filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            
            // 2. PERBAIKAN KEAMANAN (Prepared Statement)
            // Ini adalah cara aman untuk memasukkan data ke database
            $stmt = $conn->prepare("INSERT INTO mahasiswa (nama_mahs, nim_mahas, foto_mahas, prodi, alamat_mahas) VALUES (?, ?, ?, ?, ?)");
            // 'sssss' berarti 5 variabel berikutnya adalah string
            $stmt->bind_param("sssss", $nama, $nim, $new_filename, $prodi, $alamat);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
            
        } else {
            $message = "Maaf, terjadi error saat meng-upload file.";
        }
    } else {
        $message = "Error: File gambar tidak di-upload atau rusak.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
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
    </style>
</head>
<body>

    <div class="form-container">
        <h3 class="text-center mb-4">Tambah Data Mahasiswa</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form method='POST' enctype='multipart/form-data'>
            
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="name" required>
            </div>

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" required>
            </div>

            <div class="mb-3">
                <label for="prodi" class="form-label">Prodi</label>
                <select class="form-select" name="prodi" id="prodi" required>
                    <option value="">----pilih prodi-----</option>
                    <option value="teknik_infor">Teknik Informatika</option>
                    <option value="teknik_mekatro">Teknik Mekatronika</option>
                    <option value="teknik_industri">Teknik Industri</option>
                    <option value="sistem_informasi">Sistem Informasi</option>
                    <option value="teknik_elektro">Teknik Elektro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" id="alamat" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Foto Mahasiswa</label>
                <input type="file" class="form-control" name="image" id="image" required>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>