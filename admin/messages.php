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

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mensajes</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">

<h1 class="heading">Mensajes</h1>

<div class="box-container">

   <?php
      $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      if($select_messages->rowCount() > 0){
         while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
   <p> ID de Usuario : <span><?= $fetch_message['user_id']; ?></span></p>
   <p> Nombre : <span><?= $fetch_message['name']; ?></span></p>
   <p> Email : <span><?= $fetch_message['email']; ?></span></p>
   <p> Número : <span><?= $fetch_message['number']; ?></span></p>
   <p> Mensaje : <span><?= $fetch_message['message']; ?></span></p>
   <a href="messages.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">Borrar</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No hay mensajes</p>';
      }
   ?>

</div>

</section>












<script src="../js/admin_script.js"></script>

</body>
</html>
