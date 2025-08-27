<?php
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

// Handle Add Product
if(isset($_POST['submit'])){
    $name=$_POST['name'];
    $brand=$_POST['brand'];
    $price=$_POST['price'];
    $gender=$_POST['gender'];
    $stock=isset($_POST['stock'])?$_POST['stock']:0;
    $description=isset($_POST['description'])?$_POST['description']:'';
    $ml_size=isset($_POST['ml_size'])?$_POST['ml_size']:'';

    // First image
    $image=$_FILES['image']['name'];
    $target="images/".basename($image);
    if(!empty($image)) move_uploaded_file($_FILES['image']['tmp_name'],$target);

    // Second image (OPTIONAL na)
    $image2=!empty($_FILES['image2']['name']) ? $_FILES['image2']['name'] : null;
    if($image2){
        $target2="images/".basename($image2);
        move_uploaded_file($_FILES['image2']['tmp_name'],$target2);
    }

    $sql="INSERT INTO perfumes (name,brand,price,gender,image,image2,description,stock,ml_size)
          VALUES ('$name','$brand','$price','$gender','$image','$image2','$description','$stock','$ml_size')";
    $conn->query($sql);
}

// Handle Edit (get product to edit)
$editProduct=null;
if(isset($_GET['edit'])){
    $id=intval($_GET['edit']);
    $res=$conn->query("SELECT * FROM perfumes WHERE id=$id");
    $editProduct=$res->fetch_assoc();
}

// Handle Update
if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    $name=$_POST['name'];
    $brand=$_POST['brand'];
    $price=$_POST['price'];
    $gender=$_POST['gender'];
    $stock=$_POST['stock'];
    $description=$_POST['description'];
    $ml_size=$_POST['ml_size'];

    $updateQuery="UPDATE perfumes SET name='$name', brand='$brand', price='$price',
                   gender='$gender', stock='$stock', description='$description', ml_size='$ml_size'";

    if(!empty($_FILES['image']['name'])){
        $image=$_FILES['image']['name'];
        $target="images/".basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'],$target);
        $updateQuery.=", image='$image'";
    }
    if(!empty($_FILES['image2']['name'])){
        $image2=$_FILES['image2']['name'];
        $target2="images/".basename($image2);
        move_uploaded_file($_FILES['image2']['tmp_name'],$target2);
        $updateQuery.=", image2='$image2'";
    }
    $updateQuery.=" WHERE id=$id";
    $conn->query($updateQuery);
    header("Location: add_products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Add / Edit Products</title>
<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 30px;
        color: #333;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        font-weight: 600;
    }
    form {
        max-width: 520px;
        margin: auto;
        padding: 25px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    form input, 
    form select, 
    form textarea {
        width: 100%;
        padding: 8px 10px;
        margin: 8px 0 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }
    form textarea {
        min-height: 90px;
        resize: vertical;
    }
    form label {
        font-size: 14px;
        font-weight: 500;
        color: #555;
        display: block;
        margin-top: 5px;
    }
   form button {
    display: block;
    width: 100%;           /* buong lapad ng form */
    padding: 12px 18px;    /* mas malaki padding */
    border: none;
    background: #333;
    color: #fff;
    cursor: pointer;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    text-transform: uppercase;
    letter-spacing: 1px;
}
form button:hover {
    background: #555;
}

    img {
        border-radius: 6px;
        margin-top: 8px;
        border: 1px solid #eee;
    }
    a.manage-link {
        display: block;
        text-align: center;
        margin: 25px auto;
        padding: 8px 16px;
        border: 1px solid #333;
        background: #fff;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        border-radius: 6px;
        max-width: 180px;
        transition: all 0.3s;
    }
    a.manage-link:hover {
        background: #333;
        color: #fff;
    }
</style>
</head>

<body>

<?php if($editProduct): ?>
    <h2>Edit Product</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
        <input type="text" name="name" value="<?= $editProduct['name'] ?>" required>
        <input type="text" name="brand" value="<?= $editProduct['brand'] ?>" required>
        <input type="number" step="0.01" name="price" value="<?= $editProduct['price'] ?>" required>
        <input type="text" name="ml_size" value="<?= $editProduct['ml_size'] ?>" placeholder="Bottle Size (e.g. 50ml)">
        <select name="gender" required>
            <option value="Male" <?= $editProduct['gender']=='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= $editProduct['gender']=='Female'?'selected':'' ?>>Female</option>
            <option value="Unisex" <?= $editProduct['gender']=='Unisex'?'selected':'' ?>>Unisex</option>
        </select>
        <input type="number" name="stock" value="<?= $editProduct['stock'] ?>" required>
        <textarea name="description"><?= $editProduct['description'] ?></textarea>
        <label>Update Main Image:</label>
        <input type="file" name="image">
        <?php if($editProduct['image']): ?><img src="images/<?= $editProduct['image'] ?>" width="60"><?php endif; ?>
        <label>Update Second Image (Optional):</label>
        <input type="file" name="image2">
        <?php if($editProduct['image2']): ?><img src="images/<?= $editProduct['image2'] ?>" width="60"><?php endif; ?>
        <button type="submit" name="update">Update Product</button>
    </form>
<?php else: ?>
    <h2>Add New Product</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="brand" placeholder="Inspired Scent" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="text" name="ml_size" placeholder="Bottle Size (e.g. 50ml)">
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Unisex">Unisex</option>
        </select>
        <input type="number" name="stock" placeholder="Stock" required>
        <textarea name="description" placeholder="Description"></textarea>
        <label>Upload Main Image:</label>
        <input type="file" name="image" required>
        <label>Upload Second Image (Optional):</label>
        <input type="file" name="image2">
        <button type="submit" name="submit">Add Product</button>
    </form>
<?php endif; ?>

<a href="admin_dashboard.php" class="manage-link">Go to Dashboard</a>

</body>
</html>
