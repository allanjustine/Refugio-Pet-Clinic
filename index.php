<?php
require_once 'connect_db.php';

if(isset($_SESSION['email'])&&isset($_SESSION['role'])){
  if($_SESSION['role']=='admin'){
    header('Location:'.$baseurl.'admin');

  } else if($_SESSION['role']=='cashier'){
    header('Location:'.$baseurl.'cashier');

  } else {
    header('Location:dashboard.php');

  }
} else {
  header('Location:login.php');
}

?>