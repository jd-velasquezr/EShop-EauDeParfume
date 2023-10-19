<?php

include '../components/connect.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit();
}
$admin_id = $_SESSION['admin_id'];
$select_admin = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_admin->execute([$admin_id]);
$fetch_profile = $select_admin->fetch();

if(isset($_POST['update'])){

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $stock_quantity = $_POST['stock_quantity'];
   $stock_quantity = filter_var($stock_quantity, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   
   
   
      if ($stock_quantity == 0) {
      $select_product = $conn->prepare("SELECT name, price, image_01 FROM products WHERE id = ?");
      $select_product->execute([$pid]);
      $product = $select_product->fetch();
      
      $product_name = $product['name'];
      $product_price = $product['price'];
      $product_image = $product['image_01'];

      $insert_out_stock = $conn->prepare("INSERT INTO out_stock (name, price, image) VALUES (?, ?, ?)");
      $insert_out_stock->execute([$product_name, $product_price, $product_image]);
   }
   
   
   

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, stock_quantity = ?, details = ?, category = ? WHERE id = ?");
   $update_product->execute([$name, $price, $stock_quantity, $details, $category, $pid]);


   $old_image_01 = $_POST['old_image_01'];
   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/'.$image_01;

   if(!empty($image_01)){
      if($image_size_01 > 2000000){
         $message[] = 'Imagen muy grande!';
      }else{
         $update_image_01 = $conn->prepare("UPDATE `products` SET image_01 = ? WHERE id = ?");
         $update_image_01->execute([$image_01, $pid]);
         move_uploaded_file($image_tmp_name_01, $image_folder_01);
         unlink('../uploaded_img/'.$old_image_01);
         $message[] = 'Imagen 1 actualizada!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualizar Producto</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">

   <h1 class="heading">actualizar producto</h1>

   <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
      <div class="image-container">
         <div class="main-image">
            <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         </div>
         <div class="sub-image">
            <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         </div>
      </div>
      <span>Actualizar nombre</span>
      <input type="text" name="name" required class="box" maxlength="100" placeholder="Ingrese el nombre del producto" value="<?= $fetch_products['name']; ?>">
      <span>Actualizar precio (Colocar sin puntos ni comas)</span>
      <input type="number" class="box" required name="price" placeholder="(Precio del Producto)" value="<?= $fetch_products['price']; ?>">
      <span>Actualizar Stock</span>
      <input type="number" name="stock_quantity" required class="box" maxlength="100" placeholder="Ingrese el número de stock" value="<?= $fetch_products['stock_quantity']; ?>">
      <span>Actualizar detalles</span>
      <textarea name="details" class="box" required cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      <span>Actualizar categoría</span>
      <textarea name="category" class="box" required cols="10" rows="10"><?= $fetch_products['category']; ?></textarea>
      <span>Actualizar Imagen 01</span>
      <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
      <div class="flex-btn">
         <input type="submit" name="update" class="btn" value="Actualizar">
         <a href="products.php" class="option-btn">Volver al Dashboard</a>
      </div>
   </form>

   <?php
         }
      }else{
         echo '<p class="empty">Producto no encontrado!</p>';
      }
   ?>

</section>












<script src="../js/admin_script.js"></script>

</body>
</html>
