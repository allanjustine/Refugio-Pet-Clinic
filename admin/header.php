<?php require "connect_db.php";
if (!isset($_SESSION["email"]) || $_SESSION["role"] != "admin") {
    header("Location:".$baseurl);
    die();
    
}

$get_info = $pdo->prepare("SELECT users.profile from users where email=:email");
  $get_info->bindParam(":email",$_SESSION['email']);
  $get_info->execute();

  $info = $get_info->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=.8">
  <title>Pet Clinic | Admin</title>
  <link rel="icon" type="image/x-icon" href="<?= $baseurl ?>assets/img/logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.css' rel='stylesheet'>
  <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/all.css' rel='stylesheet'>
  
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/daterangepicker/daterangepicker.css">
  <!-- fullCalendar -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/fullcalendar/main.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= $baseurl?>assets/css/custom.css">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- SweetAlert -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/sweetalert2/sweetalert2.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/toastr/toastr.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= $baseurl?>assets/plugins/select2/dist/css/select2.min.css">

  <!-- jQuery -->
  <script src="<?=$baseurl;?>assets/plugins/jquery/jquery.min.js"></script>

  <!-- Bootstrap -->
  <script src="<?= $baseurl?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

  <style type="text/css">
    
    ul.striped-list > li:nth-of-type(odd) {
      background-color: transparent;
    }

    .modal.fade .modal-dialog.modal-dialog-zoom {-webkit-transform: translate(0,0)scale(.5);transform: translate(0,0)scale(.5);}
    .modal.show .modal-dialog.modal-dialog-zoom {-webkit-transform: translate(0,0)scale(1);transform: translate(0,0)scale(1);}

    .product.show {
      animation: scaleUp 0.25s ease forwards;
    }

    @keyframes scaleUp {
      0% { transform: scale(0.5); }
      100% { transform: scale(1); }

    }

    .modal-content{
      border-radius: 15px;
    }

    .loader {

      background-color: rgba(255,255,255,0.1);
      display: none;
      justify-content: center;
      align-items: center;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 9999;
      width: 100%;
      height: 100%;
      overflow: visible;
    }

    .spinner {
      height: 5vh;
      width: 5vh;
      border: 6px solid rgba(0, 174, 239, 0.2);
      border-top-color: rgba(0, 174, 239, 0.8);
      border-radius: 100%;
      animation: rotation 0.6s infinite linear 0.25s;

      /* the opacity is used to lazyload the spinner, see animation delay */
      /* this avoid the spinner to be displayed when visible for a very short period of time */
      opacity: 0;
    }

    @keyframes rotation {
      from {
        opacity: 1;
        transform: rotate(0deg);
      }
      to {
        opacity: 1;
        transform: rotate(359deg);
      }
    }
    
    .swal-text,.swal-footer{
      text-align: center;
    }

  </style>
</head>
<div id="pre-loader" class="loader">
    <div class="spinner"></div>
</div>
<body class="hold-transition  sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed text-sm">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!--
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" id="fullscreen-btn" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
      -->
      <li class="nav-item mr-3">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-user mr-2"></i>
          <?=explode(' ',$_SESSION['name'])[0]?>
        </a>
        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right mr-3">
          <div class=" dropdown-header noti-title">
            <h6 class="text-overflow m-0" style="text-align: left;">Welcome!</h6>
          </div>
          <a href="myprofile.php" class="dropdown-item">
            <i class="fa fa-user mr-2"></i>
            <span>My profile</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" onclick="logout()" class="dropdown-item">
            <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>
            <span>Logout</span>
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= $baseurl?>" class="brand-link">
      <img src="<?= $baseurl?>assets/img/logo.png" alt="Pet Clinic Logo" class="brand-image img-circle elevation-0" style="opacity: .8">
      <span class="brand-text font-weight-light"><b>Pet </b>Clinic</span>

    </a>

    <!-- Sidebar -->
    <div class="sidebar">
     <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img id="sidebar-img" src="<?= $baseurl?>admin/uploads/img/profile/<?=$info['profile']?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Welcome, <?=explode(' ',$_SESSION['name'])[0]?></a>
        </div>
      </div>

      <!-- SidebarSearch Form 
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
    -->


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column " data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item mt-2" id="nav-dash">
            <a href="<?= $baseurl.'admin';?>" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class=""></i>
              </p>
            </a>
          </li>
          <li class="nav-item mt-2" id="nav-appt">
            <a href="appointments.php" class="nav-link">
              <i class="nav-icon fas fa-calendar-day"></i>
              <p>
                Appointments
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item mt-2" id="nav-prod">
            <a href="products.php" class="nav-link">
              <i class="nav-icon fas fa-bone"></i>
              <p>
                Products
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item mt-2" id="nav-ord">
            <a href="orders.php" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Orders
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item mt-2" id="nav-cus">
            <a href="customers.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Customers
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-header">BILLINGS AND PAYMENTS</li> 
          <li class="nav-item mt-2" id="nav-payser">
            <a href="payservices.php" class="nav-link">
              <i class="nav-icon fas fa-hand-holding-medical"></i>
              <p>
                Services
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item mt-2" id="nav-payor">
            <a href="payorders.php" class="nav-link">
              <i class="nav-icon fas fa-cart-arrow-down"></i>
              <p>
                Orders
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>

          <li class="nav-header">USER MANAGEMENT</li>
          <li class="nav-item mt-2" id="nav-users">
            <a href="users.php" class="nav-link">
              <i class="nav-icon fas fa-user-gear"></i>
              <p>
                Users
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
