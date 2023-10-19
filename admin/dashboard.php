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

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin dashboard</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">

   <div class="box-container">

      <div class="box">
         <h3>Admin. Actual</h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">Modificar Perfil</a>
      </div>

      <div class="box">
          <?php
              $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'completed'");
              $select_completes->execute();
              $total_completes = $select_completes->rowCount();
          ?>
          <h3><?= $total_completes; ?><span></span></h3>
          <p>Órdenes Completadas</p>
          <a href="orders_completed.php" class="btn">Ver Órdenes</a>
      </div>


      <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'pending'");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount()
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>Órdenes Pendientes</p>
         <a href="placed_orders.php" class="btn">Ver Órdenes</a>
      </div>

      <div class="box">
         <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            $number_of_products = $select_products->rowCount()
         ?>
         <h3><?= $number_of_products; ?></h3>
         <p>Productos Añadidos</p>
         <a href="products.php" class="btn">Ver Productos</a>
      </div>

      <div class="box">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $number_of_users = $select_users->rowCount()
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>Usuarios Normales</p>
         <a href="users_accounts.php" class="btn">Ver Usuarios</a>
      </div>

      <div class="box">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount()
         ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>Administradores</p>
         <a href="admin_accounts.php" class="btn">Ver Admin. Registrados</a>
      </div>

      <div class="box">
         <?php
            $select_messages = $conn->prepare("SELECT * FROM `messages`");
            $select_messages->execute();
            $number_of_messages = $select_messages->rowCount()
         ?>
         <h3><?= $number_of_messages; ?></h3>
         <p>Nuevos Mensajes</p>
         <a href="messages.php" class="btn">Ver Mensajes</a>
      </div>

      <div class="box">
         <?php
            $zero_stock = $conn->prepare("SELECT * FROM `out_stock`");
            $zero_stock->execute();
            $number_of_zeros = $zero_stock->rowCount()
         ?>
         <h3><?= $number_of_zeros; ?></h3>
         <p>Productos sin Stock</p>
         <a href="out_stock.php" class="btn">Ver Productos</a>
      </div>

   </div>

</section>












<script src="../js/admin_script.js"></script>

</body>
</html>
