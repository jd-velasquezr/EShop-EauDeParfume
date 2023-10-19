<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo"><img src="images/EDP.png" alt="Eau de Parfum" width="225" height="55"></a>

      <nav class="navbar">
         <a href="home.php">Inicio</a>
         <a href="home.php#categorias-de-perfumes">Categorías de Perfumes</a>
         <a href="orders.php">Órdenes</a>
         <a href="shop.php">Catálogo Completo</a>
         <!--<a href="contact.php">Contacto</a> -->
      </nav>

      <div class="icons">
         <?php
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_counts = $count_wishlist_items->rowCount();

            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_counts = $count_cart_items->rowCount();
         ?>
         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="search_page.php"><i class="fas fa-search"></i></a>
         <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $total_wishlist_counts; ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_counts; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile["name"]; ?></p>
         <a href="update_user.php" class="btn">Actualizar perfil</a>
         <div class="flex-btn">
            <a href="user_register.php" class="option-btn">Registrarse</a>
            <a href="user_login.php" class="option-btn">Iniciar Sesión</a>
         </div>
         <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">logout</a>
         <?php
            }else{
         ?>
         <p>Identficarse:</p>
         <div class="flex-btn">
            <a href="user_register.php" class="option-btn">Registrarse</a>
            <a href="user_login.php" class="option-btn">Iniciar Sesión</a>
         </div>
         <?php
            }
         ?>


      </div>

   </section>

</header>
