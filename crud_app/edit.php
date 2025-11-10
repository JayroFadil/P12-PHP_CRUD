<?php
include "db.php";

$id = null;
$nama = "";
$price = "";
$img = ""; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}


if (!$id) {
    die("Error: ID Produk tidak ditemukan.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['name'];
    $price = $_POST['price'];
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

    
    $stmt_update = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?");
    
    $stmt_update->bind_param("sdsi", $nama, $price, $img_to_save, $id);

    if ($stmt_update->execute()) {
        header("Location: index.php"); 
        exit();
    } else {
        echo "Error updating record: " . $stmt_update->error;
    }
    $stmt_update->close();
}



$stmt_select = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
$data = $result->fetch_assoc();

if ($data) {
    $nama = $data['name'];
    $price = $data['price'];
    $img = $data['image'];
} else {
    echo "Data tidak ditemukan.";
    exit(); 
}

$stmt_select->close();
$conn->close();
?>

<form method='POST' enctype='multipart/form-data'>
    <input type="hidden" name="id" value="<?= $id ?>">
    
    <input type="hidden" name="current_image" value="<?= htmlspecialchars($img) ?>">

    Nama: <input type='text' name='name' required value="<?= htmlspecialchars($nama) ?>"><br>
    Harga: <input type='number' step='0.01' name='price' required value="<?= htmlspecialchars($price) ?>"><br>

    <?php if (!empty($img)): ?>
        Gambar Saat Ini: <br>
        <img src="uploads/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($nama) ?>" height="100">
        <br>
    <?php endif; ?>

    Ganti Gambar (Opsional): <input type='file' name='image'><br>

    <button type='submit' name='submit'>Simpan Perubahan</button>
</form>