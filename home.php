<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Inicio</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="home-bg">

<section class="home">

   <div class="swiper home-slider">

   <div class="swiper-wrapper">

      <div class="swiper-slide slide">
         <div class="image">
            <img src="images/clear.png" alt="">
            <a id="categorias-de-perfumes"></a>
         </div>
         <div class="content">
            <span>Perfumes a precios Incomparables!</span>
            <h3>Edición 2022 - 2023</h3>
            <a href="shop.php" class="btn">Revise el Catálogo!</a>

         </div>
      </div>
   </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

</div>

<h1 class="heading"> </h1>

<h1 class="heading">Categorías</h1>

<section class="category">


   <div class="swiper category-slider">

   <div class="swiper-wrapper">

   <a href="category.php?category=men" class="swiper-slide slide">
      <img src="images/hombre.png" alt="">
      <h3>Perfumes para Hombre</h3>
   </a>

   <a href="category.php?category=women" class="swiper-slide slide">
      <img src="images/mujer.png" alt="">
      <h3>Perfumes para Mujer</h3>
   </a>

   <a href="category.php?category=unisex" class="swiper-slide slide">
      <img src="images/unisex.png" alt="">
      <h3>Perfumes </h3>
      <h3>Unisex</h3>
   </a>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>

<section class="home-products">

   <h1 class="heading">Productos Recomendados</h1>

   <div class="swiper products-slider">

   <div class="swiper-wrapper">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY RAND() LIMIT 6");
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="swiper-slide slide">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="stock" value="<?= $fetch_product['stock_quantity']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="stock" style="font-size: 15px">Stock: <?= $fetch_product['stock_quantity']; ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= number_format($fetch_product['price'], 0, '.', '.'); ?><span></span></div>
         <input type="number" name="qty" class="qty" min="1" max="2" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">No hay productos añadidos</p>';
   }
   ?>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>









<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
    },
});

 var swiper = new Swiper(".category-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

var swiper = new Swiper(".products-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      550: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>
