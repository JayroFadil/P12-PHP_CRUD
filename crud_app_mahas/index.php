<?php
include 'db.php';

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pengaturan Paginasi
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// --- PERBAIKAN KEAMANAN (Prepared Statements) ---

// 1. Hitung total data dengan aman
$searchParam = "%$search%";
$countSql = "SELECT COUNT(*) AS total FROM mahasiswa WHERE nama_mahs LIKE ?";
$stmt_count = mysqli_prepare($conn, $countSql);
mysqli_stmt_bind_param($stmt_count, "s", $searchParam);
mysqli_stmt_execute($stmt_count);
$countResult = mysqli_stmt_get_result($stmt_count);
$countRow = mysqli_fetch_assoc($countResult);
$total = $countRow['total'];
$pages = ceil($total / $limit);

// 2. Ambil data dengan aman
$sql = "SELECT * FROM mahasiswa WHERE nama_mahs LIKE ? LIMIT ?, ?";
$stmt_data = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt_data, "sii", $searchParam, $start, $limit);
mysqli_stmt_execute($stmt_data);
$result = mysqli_stmt_get_result($stmt_data);

// --- Akhir Perbaikan Keamanan ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style tambahan agar foto seragam */
        .table-img {
            width: 80px;
            height: 80px;
            object-fit: cover; /* Memastikan gambar terpotong rapi */
            border-radius: 0.25rem; /* Sedikit lengkungan di sudut */
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Data Master Mahasiswa</h2>

    <div class="d-flex justify-content-between mb-3">
        
        <form method="GET" class="d-flex" style="width: 50%;">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari berdasarkan nama..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
        
        <a href="add.php" class="btn btn-success">+ Tambah Baru</a>
    </div>

    <div class="table-responsive"> <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>NIM</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Alamat</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="text-center">
                    <td><?php echo $row['id_mahas']; ?></td>
                    <td><?php echo $row['nim_mahas']; ?></td>
                    <td>
                        <img src="uploads/<?php echo htmlspecialchars($row['foto_mahas']); ?>" alt="Foto Mahasiswa" class="table-img">
                    </td>
                    <td><?php echo htmlspecialchars($row['nama_mahs']); ?></td>
                    <td><?php echo htmlspecialchars($row['prodi']); ?></td>
                    <td><?php echo htmlspecialchars($row['alamat_mahas']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id_mahas']; ?>" class="btn btn-warning btn-sm">Ubah</a>
                        <a href="delete.php?id=<?php echo $row['id_mahas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if ($total == 0) : ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1) { ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php } ?>
            
            <li class="page-item <?php echo ($page >= $pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
            </li>

        </ul>
    </nav>
    <?php } ?>

</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>