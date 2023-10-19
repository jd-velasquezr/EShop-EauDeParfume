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

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);
   if($payment_status === 'completed'){
     $update_order_status = $conn->prepare("UPDATE `orders` SET payment_status = 'completed' WHERE id = ?");
     $update_order_status->execute([$order_id]);

     // Obtener el user_id que completó la orden
     $completed_order = $conn->prepare("SELECT user_id FROM orders WHERE id = ?");
     $completed_order->execute([$order_id]);
     $user_id = $completed_order->fetch(PDO::FETCH_ASSOC)['user_id'];


     // Obtener los productos de la orden
     $cart_items = $conn->prepare("SELECT name, quantity FROM cart_orders WHERE user_id = ?");
     $cart_items->execute([$user_id]);
     $cart_items = $cart_items->fetchAll(PDO::FETCH_ASSOC);

     // Restar la cantidad solicitada al stock de cada producto
     foreach ($cart_items as $cart_item) {
         $product_name = $cart_item['name'];
         $quantity = $cart_item['quantity'];

         $update_product = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE name = ?");
         $update_product->execute([$quantity, $product_name]);
     }

     $get_zero_stock_products = $conn->prepare("SELECT * FROM products WHERE stock_quantity = 0");
     $get_zero_stock_products->execute();
     $zero_stock_products = $get_zero_stock_products->fetchAll(PDO::FETCH_ASSOC);

    foreach ($zero_stock_products as $product) {
    $product_name = $product['name'];
    $product_price = $product['price'];
    $product_image = $product['image_01'];

    $insert_out_stock = $conn->prepare("INSERT INTO out_stock (name, price, image) VALUES (?, ?, ?)");
    $insert_out_stock->execute([$product_name, $product_price, $product_image]);
    }
   }

   $message[] = 'Estado de Pago actualizado!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>órdenes</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders">

<h1 class="heading">Órdenes Activas</h1>

<div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status='pending'");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Fecha de Orden : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Nombre : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Número : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Dirección : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Productos Totales : <span><?= $fetch_orders['total_products']; ?></span></p>
      <p> Precio total : <span>$<?= number_format($fetch_orders['total_price'], 0, '.', '.'); ?></span></p>
      <p> Método de pago : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="select">
            <option value="pending">Pendiente</option>
            <option value="completed">Completada</option>
         </select>
        <div class="flex-btn">
         <input type="submit" value="Actualizar" class="option-btn" name="update_payment">
         <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Borrar orden?');">Eliminar</a>
        </div>
      </form>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No hay órdenes aún</p>';
      }
   ?>

</div>

</section>

</section>












<script src="../js/admin_script.js"></script>

</body>
</html>
