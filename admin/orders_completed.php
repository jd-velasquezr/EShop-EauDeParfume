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
}

$message[] = 'Estado de Pago actualizado!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:orders_completed.php');
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

<h1 class="heading">Órdenes Completadas</h1>

<div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'completed'");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> Fecha de Orden : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Nombre : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Número : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Dirección : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Productos Totales :<p></p> <span><p><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Precio total : <span>$<?= number_format($fetch_orders['total_price'], 0, '.', '.'); ?></span> </p>
      <p> Método de pago : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="select">
            <option value="completed">Completada</option>
            <option value="pending">Pendiente</option>
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
