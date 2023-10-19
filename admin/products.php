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

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/'.$image_01;

   $stock_quantity = $_POST['stock_quantity'];
   $stock_quantity = filter_var($stock_quantity, FILTER_SANITIZE_STRING);

   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'El nombre del producto ya existe!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, image_01, stock_quantity, category) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$name, $details, $price, $image_01, $stock_quantity, $category]);

      if($insert_products){
         if($image_size_01 > 2000000){
            $message[] = 'Tamaño de Imagen muy grande!';
         }else{
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            $message[] = 'Nuevo Producto añadido!';
         }

      }

   }

};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/'.$fetch_delete_image['image_01']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   header('location:products.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">+ Productos</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <span>Nombre del Producto</span>
            <input type="text" class="box" required maxlength="100" placeholder="(Nombre del Producto)" name="name">
         </div>
         <div class="inputBox">
            <span>Precio del Producto (Colocar sin puntos ni comas)</span>
            <input type="number" min="0" class="box" required max="9999999999" placeholder="(Precio del Producto)" onkeypress="if(this.value.length == 10) return false;" name="price">
         </div>
        <div class="inputBox">
            <span>Imagen 1</span>
            <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
        </div>
        <div class="inputBox">
          <span>Cantidad en Stock</span>
          <input type = "number" name="stock_quantity" placeholder="(Cantidad en Stock)" class="box" required maxlength="500" cols="30" rows="10" onchange="validateStockQuantity(this)">
        </div>
        <div class="inputBox">
          <span>Categoría</span>
          <input type = "text" name="category" placeholder="Categoría: men/women/unisex" class="box" required maxlength="500" cols="30" rows="10">
        </div>
         <div class="inputBox">
            <span>Detalles del Producto</span>
            <textarea name="details" placeholder="(Detalles del Producto)" class="box" required maxlength="500" cols="30" rows="10"></textarea>
         </div>

         <script>
         function validateStockQuantity(textarea) {
           if (textarea.value === '0') {
             alert('La cantidad en stock no puede ser cero!');
             textarea.value = ''; // Borrar el valor del área de texto
             textarea.focus(); // Colocar el cursor en el área de texto
           }
         }
         </script>

      </div>

      <input type="submit" value="Agregar Producto" class="btn" name="add_product" id = "búsqueda">
   </form>

</section>



<section class="show-products">

   <h1 class="heading">Buscar Productos</h1>
   <section class="search-form">
      <form action="#búsqueda" method="post">
         <input type="text" name="search_box" placeholder="Busque aquí..." maxlength="100" class="box" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>
   </section>
   <style>
  .search-form {
    display: flex;
    justify-content: center;
  }
</style>
   <div class="box-container">

   <?php
     if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
     $search_box = $_POST['search_box'];
     $search_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'");
     $search_products->execute();
     if($search_products->rowCount() > 0){
      while($fetch_product = $search_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="price">$<span><?= number_format($fetch_product['price'], 0, '.', '.'); ?></span></div>
      <div class="number" style="font-size: 15px">Stock: <?= $fetch_product['stock_quantity']; ?></div>
      <div class="details"><span><?= $fetch_product['details']; ?></span></div>
      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_product['id']; ?>" class="option-btn">Actualizar</a>
         <a href="products.php?delete=<?= $fetch_product['id']; ?>" class="delete-btn" onclick="return confirm('Eliminar este producto?');">Eliminar</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="error">No se encontraron Productos!</p>';
      }
   }
   ?>

<style>
  .error {
    color: red;
    font-weight: bold;
    font-size: 2em; /* Ajuste el tamaño de fuente a su preferencia */
    text-align: center;
  }
</style>

</div>

</section>




<section class="show-products">

   <h1 class="heading">Productos Añadidos</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="price">$<span><?= number_format($fetch_products['price'], 0, '.', '.'); ?></span></div>
      <div class="number" style="font-size: 15px">Stock: <?= $fetch_products['stock_quantity']; ?></div>
      <div class="details"><span><?= $fetch_products['details']; ?></span></div>
      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Actualizar</a>
         <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Eliminar este producto?');">Eliminar</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No hay productos aún!</p>';
      }
   ?>

   </div>

</section>








<script src="../js/admin_script.js"></script>

</body>
</html>
