<?php
$host="localhost"; $user="root"; $pass=""; $dbname="happy_sprays";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){ die("DB connection failed: ".$conn->connect_error); }

// Handle Delete
if(isset($_GET['delete'])){
    $id=intval($_GET['delete']);
    $conn->query("DELETE FROM perfumes WHERE id=$id");
}

// PAGINATION
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page-1)*$limit;

$result=$conn->query("SELECT * FROM perfumes ORDER BY id DESC LIMIT $start, $limit");
$totalRes = $conn->query("SELECT COUNT(*) as total FROM perfumes");
$totalRow = $totalRes->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products List - Happy Sprays</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f9f9f9;
    color: #333;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #222;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
th, td {
    padding: 12px;
    text-align: center;
}
th {
    background: #f4f4f4;
    color: #333;
    font-weight: bold;
    border-bottom: 2px solid #ddd;
}
td {
    border-bottom: 1px solid #eee;
}
tr:nth-child(even) {
    background: #fafafa;
}
a.delete {
    color: #d9534f;
    text-decoration: none;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background 0.2s;
}
a.delete:hover {
    background: #d9534f;
    color: #fff;
}
a.edit {
    color: #0275d8;
    text-decoration: none;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background 0.2s;
}
a.edit:hover {
    background: #0275d8;
    color: #fff;
}
img {
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.pagination {
    text-align: center;
    margin: 20px 0;
}
.pagination a {
    padding: 8px 14px;
    margin: 2px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 5px;
    background: #fff;
    transition: all 0.2s;
}
.pagination a:hover {
    background: #333;
    color: #fff;
}
.pagination a.active {
    background: #333;
    color: #fff;
    font-weight: bold;
}
.back-btn {
    display: inline-block;
    margin: 10px 0 20px;
    padding: 10px 18px;
    border: none;
    background: #333;
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    border-radius: 6px;
    transition: background 0.2s;
}
.back-btn:hover {
    background: #000;
}
</style>
</head>

<body>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

<div id="productsTable">
<h2>Existing Products</h2>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Inspired Scent</th>
    <th>Price</th>
    <th>Gender</th>
    <th>Stock</th>
    <th>Images</th>
    <th>Actions</th>
</tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['brand'] ?></td>
    <td>$<?= $row['price'] ?></td>
    <td><?= $row['gender'] ?></td>
    <td><?= $row['stock'] ?></td>
    <td>
        <?php if(!empty($row['image'])): ?>
            <img src="images/<?= $row['image'] ?>" width="60">
        <?php endif; ?>
        <?php if(!empty($row['image2'])): ?>
            <img src="images/<?= $row['image2'] ?>" width="60">
        <?php endif; ?>
    </td>
    <td>
        <a class="edit" href="add_products.php?edit=<?= $row['id'] ?>">Edit</a> | 
        <a class="delete" href="products_list.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<div class="pagination">
    <?php for($i=1; $i<=$totalPages; $i++): ?>
        <a href="products_list.php?page=<?= $i ?>" class="<?= ($i==$page)?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
</div>

<script>
function toggleProducts(){
    const table=document.getElementById("productsTable");
    table.style.display = (table.style.display==="none"||table.style.display==="") ? "block" : "none";
}
</script>

</body>
</html>
