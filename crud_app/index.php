<?php
include 'db.php';

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pengaturan Paginasi
$limit = 5;
$page = isset($_GET['page']) ?
$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$countSql = "SELECT COUNT(*) AS total FROM products WHERE name LIKE '%$search%'";
$countResult = mysqli_query($conn, $countSql);
$countRow = mysqli_fetch_assoc($countResult);
$total = $countRow['total'];
$pages = ceil($total / $limit);

// Ambil data
$sql = "SELECT * FROM products WHERE name LIKE '%$search%' LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>

<form method='GET'>
  <input type='text' name='search' placeholder='Cari berdasarkan nama...' value='<?php echo $search;
?>'>
  <button type='submit'>Cari</button>
</form>

<a href='add.php'>+ Tambah Produk Baru</a>

<table border='1' cellpadding='10'>
  <tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Harga</th>
    <th>Gambar</th>
    <th>Aksi</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
  <tr>
    <td><?php echo $row['id'];
?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['price'];
?></td>
    <td><img src='uploads/<?php echo $row['image']; ?>' width='80'></td>
    <td>
      <a href='edit.php?id=<?php echo $row['id'];
?>'>Ubah</a> |
      <a href='delete.php?id=<?php echo $row['id']; ?>' onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
    </td>
  </tr>
  <?php } ?>
</table>

<?php for ($i = 1; $i <= $pages; $i++) { ?>
    <a href='?page=<?php echo $i;?>&search=<?php echo $search; ?>'>
        <?php echo $i; ?>
    </a>
<?php } ?>