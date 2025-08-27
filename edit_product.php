<?php
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM perfumes WHERE id=$id");
    $product = $result->fetch_assoc();
}

// Handle update
if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $gender = $_POST['gender'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // First image
    if(!empty($_FILES['image']['name'])){
        $image = $_FILES['image']['name'];
        $target = "images/".basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'],$target);
        $conn->query("UPDATE perfumes SET image='$image' WHERE id=$id");
    }

    // Second image
    if(!empty($_FILES['image2']['name'])){
        $image2 = $_FILES['image2']['name'];
        $target2 = "images/".basename($image2);
        move_uploaded_file($_FILES['image2']['tmp_name'],$target2);
        $conn->query("UPDATE perfumes SET image2='$image2' WHERE id=$id");
    }

    // Update other fields
    $conn->query("UPDATE perfumes SET name='$name', brand='$brand', price='$price', gender='$gender', stock='$stock', description='$description' WHERE id=$id");

    header("Location: add_products.php");
}
?>
<style>
/* Body */
body {
    font-family: sans-serif;
    margin: 20px;
    background: #fff;
    color: #000;
}

/* Form Container */
form {
    max-width: 500px;
    margin: auto;
    padding: 20px;
    border: 1px solid #000;
    border-radius: 5px;
    background: #f9f9f9;
}

/* Inputs, Selects, Textareas */
form input,
form select,
form textarea {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #000;
    border-radius: 3px;
    font-size: 16px;
    box-sizing: border-box;
}

/* Buttons */
form button {
    padding: 10px 20px;
    border: none;
    background: #000;
    color: #fff;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: 0.3s;
}

form button:hover {
    background: #444;
}

/* Headings */
h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Optional: Image preview */
img.preview {
    display: block;
    max-width: 200px;
    margin: 10px auto;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Link back to products */
a.back {
    display: inline-block;
    margin: 20px auto;
    text-align: center;
    text-decoration: none;
    color: #000;
    font-weight: bold;
    border: 1px solid #000;
    padding: 8px 15px;
    border-radius: 5px;
    transition: 0.3s;
}

a.back:hover {
    background: #000;
    color: #fff;
}
</style>


<h2>Edit Product</h2>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <input type="text" name="name" value="<?= $product['name'] ?>" required>
    <input type="text" name="brand" value="<?= $product['brand'] ?>" required>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
    <select name="gender" required>
        <option value="Male" <?= $product['gender']=='Male'?'selected':'' ?>>Male</option>
        <option value="Female" <?= $product['gender']=='Female'?'selected':'' ?>>Female</option>
        <option value="Unisex" <?= $product['gender']=='Unisex'?'selected':'' ?>>Unisex</option>
    </select>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
    <textarea name="description"><?= $product['description'] ?></textarea>

    <!-- First Image -->
    <label>Update Main Image:</label>
    <input type="file" name="image">
    <?php if(!empty($product['image'])): ?>
        <img src="images/<?= $product['image'] ?>" class="preview">
    <?php endif; ?>

    <!-- Second Image -->
    <label>Update Second Image:</label>
    <input type="file" name="image2">
    <?php if(!empty($product['image2'])): ?>
        <img src="images/<?= $product['image2'] ?>" class="preview">
    <?php endif; ?>

    <button type="submit" name="update">Update Product</button>
</form>
