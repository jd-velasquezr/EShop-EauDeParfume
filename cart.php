<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'Cantidad de productos actualizada.';
}

$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
if($select_cart->rowCount() > 0){
   while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
      $select_product = $conn->prepare("SELECT stock_quantity FROM `products` WHERE id = ?");
      $select_product->execute([$fetch_cart['pid']]);
      $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
      if ($fetch_cart['quantity'] > $fetch_product['stock_quantity']) {
         $is_disabled = false;
         $message[] = 'Pero, La cantidad de productos en su carrito supera el stock disponible! Revise el stock.';
         break; // Salir del loop una vez que se encuentra un producto con stock insuficiente
      }
      else{
        $is_disabled = true;
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
   <title>Carrito de compras</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

   <h3 class="heading">Carrito de Compras</h3>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){

       // Obtener el stock del producto
       $select_product = $conn->prepare("SELECT stock_quantity FROM `products` WHERE id = ?");
       $select_product->execute([$fetch_cart['pid']]);
       $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);

       // Muestra los detalles del producto en el carrito
   ?>



   <form action="" method="post" class="box">
     <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
     <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
     <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
     <div class="name"><?= $fetch_cart['name']; ?></div>
     <div class="stock" style="font-size: 15px">Stock: <?= $fetch_product['stock_quantity']; ?></div>
     <div class="flex">
       <div class="price">$<?= number_format($fetch_cart['price'], 0, '.', '.'); ?></div>
       <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="<?= $fetch_cart['quantity']; ?>">
       <button type="submit" class="fas fa-edit" name="update_qty"></button>
     </div>
     <?php
     // Multiplica el precio por la cantidad y asigna el resultado a $sub_total
     $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];

     // Muestra el número formateado en la página web
     ?>
     <div class="sub-total">
       Sub Total: <span>$<?= number_format($sub_total, 0, '.', '.'); ?></span>
     </div>
     <input type="submit" value="Borrar Ítem" onclick="return confirm('Eliminar del carrito?');" class="delete-btn" name="delete">
   </form>
   <?php
   $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">Su carrito está vacío</p>';
   }
   ?>
   </div>



   <div class="cart-total">
      <p>Total Neto: <span>$<?= number_format($grand_total, 0, '.', '.'); ?></span></p>
      <a href="shop.php" class="option-btn">Continuar Comprando</a>
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('Borrar todo del carrito?');">Borrar todos los ítems</a>
      <a href="checkout.php" class="btn <?= ($is_disabled == true)?'': 'disabled'; ?> <?= ($grand_total > 1)?'':'disabled'; ?>" name="checkout" > Proceder a Checkout</a>
   </div>


</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>


</body>
</html>
