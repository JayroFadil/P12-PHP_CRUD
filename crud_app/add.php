<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (name, price, image) VALUES ('$name', '$price', '$image')";
        if (mysqli_query($conn, $sql)) {
            header('Location: index.php');
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
    } else {
        echo 'Upload file gagal!';
    }
}
?>

<form method='POST' enctype='multipart/form-data'>
    Nama: <input type='text' name='name' required><br>
    Harga: <input type='number' step='0.01' name='price' required><br>
    Gambar: <input type='file' name='image' required><br>
    <button type='submit' name='submit'>Simpan</button>
</form>