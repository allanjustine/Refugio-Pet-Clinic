<?php require_once './header.php'; 

include('inc/analytics.php');

function generatePassword() {

$chars = '0123456789abcdfhkmnprstvzABCDFHJKLMNPRSTVZ';
$shuffled = str_shuffle($chars);
$result = mb_substr($shuffled, 0, 8);

return $result;
}

function fill_owners()
{
  global $pdo;
  $output = '';
  $select_customer = $pdo->prepare("SELECT id, name from customers");
  $select_customer->execute();
  if ($select_customer->rowCount()) {
    while ($row = $select_customer->fetch(PDO::FETCH_OBJ)) {
      $output .= "<option value='{$row->id}'>{$row->name}</option>";
    }
  }
  return $output;
}

function fill_types()
{
  global $pdo;
  $output = '';
  $select_type = $pdo->prepare("SELECT distinct(trim(type)) as type from pets");
  $select_type->execute();
  if ($select_type->rowCount()) {
    while ($row = $select_type->fetch(PDO::FETCH_OBJ)) {
      $output .= "<option value='{$row->type}'>{$row->type}</option>";
    }
  }
  return $output;
}

?>
<script type="text/javascript">
  $('#nav-cus').find('a').toggleClass('active');
</script>

<?php  
if (isset($_POST['addCustomer'])) {


  $cus_name  = $_POST['cus_name'];
  $cus_addr = $_POST['cus_addr'];
  $cus_phone = $_POST['cus_phone'];
  $cus_email = trim($_POST['cus_email']);

  $pass = generatePassword();

    $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $cus_email);
    $check_email->execute();
  if (!$check_email->rowCount()) {

         $insert_user = $pdo->prepare("INSERT INTO users ( email, password, role ) VALUES( :email, :password, 'customer' )");
  $insert_user->bindParam(":email", $cus_email);
  $insert_user->bindParam(":password", $pass );
  $insert_user->execute();

  $to = $cus_email;
  $subject = "Pet Clinic Login Credentials";
  $body = "Use this password: ".$pass;
  $email = $cus_email;
  $status = '';

  
  if(mail($to,$subject,$body,$email)) {
    $status = "success";
  } else {
    $status = "failed";
  }
  

    //Insert Captured information to a database table
  $insert = $pdo->prepare("INSERT INTO customers ( name, address, phone, email ) VALUES( :name, :address, :phone, :email )");
  $insert->bindParam(":name", $cus_name);
  $insert->bindParam(":address", $cus_addr);
  $insert->bindParam(":phone", $cus_phone);
  $insert->bindParam(":email", $cus_email);

  $insert->execute();

  if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
    if ($status=="success") {

      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Customer Added Successfully",
            text: "<?php echo $cus_name ?>, Has Been Inserted\n An Email Sent For Login Credentials",
            icon: "success",
             buttons: {
               confirm: {
                text: "OK",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            },
            closeOnClickOutside: false
          }).then(function() {
              window.location = "customers.php";
          });
        });
      </script>
      <?php
    } else{
      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Customer Added Successfully",
            text: "<?php echo $cus_name ?>, Has Been Inserted\nPlease use this password: <?php echo $pass ?>",
            icon: "success",
             buttons: {
               confirm: {
                text: "OK",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            },
            closeOnClickOutside: false
          }).then(function() {
              window.location = "customers.php";
          });
        });
      </script>
      <?php

    }
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Add Customer Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  header("refresh:3,customers.php");
  }


    } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "User Email Already Exists",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  header("refresh:3,customers.php");
  }

 


}

if (isset($_POST['editCustomer'])) {

 $cus_id  = trim($_POST['cus_id']);
 $cus_name  = $_POST['cus_name'];
 $cus_addr = $_POST['cus_addr'];
 $cus_phone = $_POST['cus_phone'];
 $cus_email = trim($_POST['cus_email']);
 $cus_old_email = trim($_POST['cus_old_email']);

   $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $cus_email);
    $check_email->execute();
  if (!$check_email->rowCount()|| $cus_email == $cus_old_email) {


  $update_user = $pdo->prepare("UPDATE users set email=:email  where email=:old_email");
  $update_user->bindParam(":email", $cus_email);
  $update_user->bindParam(":old_email", $cus_old_email);
  $update_user->execute();

    //Insert Captured information to a database table
 $update = $pdo->prepare("UPDATE customers set name=:name, address=:address, phone=:phone, email=:email where id=".$cus_id."");
 $update->bindParam(":name", $cus_name);
 $update->bindParam(":address", $cus_addr);
 $update->bindParam(":phone", $cus_phone);
 $update->bindParam(":email", $cus_email);

 if ($update->execute()) {
            //echo "Application Submitted Successfully";
  ?>



  <script type="text/javascript">
    window.addEventListener("load", function() {
      swal({
        title: "Customer Updated Successfully",
        text: "<?php echo $cus_name ?>, Has Been Updated",
        icon: "success",
        showCancelButton: false,
        showConfirmButton: false,
        buttons: false
      });
    });
  </script>
  <?php
} else {
  ?>
  <script>
    window.addEventListener("load", function() {
      swal({
        title: "Error",
        text: "Update Customer Failed",
        icon: "error",
        showConfirmButton: false,
        showCancelButton: false,
        buttons: false
      });
    });
  </script>

  <?php
}
  } else {
  ?>
  <script>
    window.addEventListener("load", function() {
      swal({
        title: "Error",
        text: "User Email Already Exists",
        icon: "error",
        showConfirmButton: false,
        showCancelButton: false,
        buttons: false
      });
    });
  </script>

  <?php
}


header("refresh:3,customers.php");

}

if (isset($_POST['addPet'])) {

  $pet_name  = $_POST['pet_name'];
  $pet_owner = $_POST['pet_owner'];
  $pet_type = $_POST['pet_type'];

    //Insert Captured information to a database table
  $insert = $pdo->prepare("INSERT INTO pets ( name, owner_id, type ) VALUES( :name, :owner, :type )");
  $insert->bindParam(":name", $pet_name);
  $insert->bindParam(":owner", $pet_owner);
  $insert->bindParam(":type", $pet_type);

  $insert->execute();

  if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
    ?>



    <script type="text/javascript">
      window.addEventListener("load", function() {
        swal({
          title: "Pet Registered Successfully",
          text: "<?php echo $pet_name ?>, Has Been Registered",
          icon: "success",
          showCancelButton: false,
          showConfirmButton: false,
          buttons: false
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Pet Registration Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  }
  header("refresh:3,customers.php");
}

if (isset($_POST['editPet'])) {


  $pet_id  = $_POST['pet_id'];
  $pet_name  = $_POST['pet_name'];
  $pet_owner = $_POST['pet_owner'];
  $pet_type = $_POST['pet_type'];

    //Insert Captured information to a database table
  $update = $pdo->prepare("UPDATE pets set name=:name, owner_id=:owner, type=:type where id=".$pet_id."");
  $update->bindParam(":name", $pet_name);
  $update->bindParam(":owner", $pet_owner);
  $update->bindParam(":type", $pet_type);

  if ($update->execute()) {
            //echo "Application Submitted Successfully";
    ?>



    <script type="text/javascript">
      window.addEventListener("load", function() {
        swal({
          title: "Pet Updated Successfully",
          text: "<?php echo $pet_name ?>, Has Been Updated",
          icon: "success",
          showCancelButton: false,
          showConfirmButton: false,
          buttons: false
        });
      });
    </script>
    <?php
  } else {
    ?>
    <script>
      window.addEventListener("load", function() {
        swal({
          title: "Error",
          text: "Pet Registration Failed",
          icon: "error",
          showConfirmButton: false,
          showCancelButton: false,
          buttons: false
        });
      });
    </script>

    <?php
  }
  header("refresh:3,customers.php");
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
    <!--<div class="col-sm-6">
     <span class="d-flex" style="align-items: baseline;"><h1 class="">Dashboard</h1><small>Admin</small></span>
    </div> /.col 
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </div>--><!-- /.col -->
  </div><!-- /.row -->
</div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">


    <!-- Info boxes -->
    <div class="row mt-2">


      <!-- fix for small devices only -->
      <div class="clearfix hidden-md-up"></div>

      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">CUSTOMERS</span>
            <span class="info-box-number"><?=$verified+$unconfirmed?></span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">ACTIVE</span>
            <span class="info-box-number"><?=$verified?></span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-clock"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">UNVERIFIED</span>
            <span class="info-box-number"><?=$unconfirmed?></span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row mt-3">

      <div class="col-md-12">
        <div class="card card-primary card-outline card-outline-tabs">
          <div class="card-header p-0 border-bottom-0">
          <!--
            <ul class="nav nav-tabs" id="customersTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="customersTabPetowners" data-toggle="pill" href="#petowners" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">PET OWNERS</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="customersTabPets" data-toggle="pill" href="#pets" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">PETS</a>
              </li>
            </ul>
          -->
          </div>
          <div class="card-body">
            <div class="tab-content" id="customersTabContent">
              <div class="tab-pane fade show active" id="petowners" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">

               <div class="card b-0 " style="box-shadow: none !important;">

                <!-- /.card-header -->
                <div class="card-body">
                  <table id="owners-table" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>CUSTOMER NAME</th>
                        <th>ADDRESS</th>
                        <th>PHONE</th>
                        <th>EMAIL</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $get_customer = $pdo->prepare("SELECT customers.id, customers.name, customers.address, customers.phone, customers.email, users.status FROM `customers` inner join users on customers.email=users.email ORDER BY `id` DESC");
                      $get_customer->execute();
                      while ($customer = $get_customer->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                        ?>

                        <tr>
                          <input type="hidden" id="cus-id-<?=$customer->id?>" value="<?=$customer->id?>">
                          <td><input type="hidden" id="cus-name-<?=$customer->id?>" value="<?=$customer->name?>"><?=$customer->name?></td>
                          <td><input type="hidden" id="cus-address-<?=$customer->id?>" value="<?=$customer->address?>"><?=$customer->address?></td>
                          <td><input type="hidden" id="cus-phone-<?=$customer->id?>" value="<?=$customer->phone?>"><?=$customer->phone?></td>
                          <td><input type="hidden" id="cus-email-<?=$customer->id?>" value="<?=$customer->email?>"><?=$customer->email?></td>
                          <td>
                               <?php
                              if($customer->status=='PENDING CONFIRMATION'){
                                echo '<span class="badge badge-warning">UNVERIFIED</span>';
                              } else if($customer->status=='DEACTIVATED'){
                                echo '<span class="badge badge-danger">'.$customer->status.'</span>';
                              } else if($customer->status=='ACTIVE'){
                                echo '<span class="badge badge-success">'.$customer->status.'</span>';
                              } else {
                                echo '<span class="badge badge-info">'.$customer->status.'</span>';
                              }
                              
                            ?>
                          </td>
                          <td style="white-space:nowrap !important; width: 30px !important"><button onclick="showEditModal(<?=$customer->id?>)" class="btn btn-sm btn-info shadow" style="border-radius: 5px"><span><i class="fas fa-edit"></i></span>&nbsp;&nbsp;Update</button></td>
                        </tr>
                        <?php  
                      }
                      ?>

                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>

            <div class="tab-pane fade" id="pets" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">


             <div class="card b-0 " style="box-shadow: none !important;">

              <!-- /.card-header -->
              <div class="card-body">
                <table id="pets-table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>PET NAME</th>
                      <th>OWNER</th>
                      <th>TYPE</th>
                      <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $get_pet = $pdo->prepare("SELECT pets.id as pet_id, pets.name, pets.type, pets.owner_id , customers.name as owner FROM `pets` inner join customers on pets.owner_id = customers.id ");
                    $get_pet->execute();
                    while ($pet = $get_pet->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                      ?>

                      <tr>
                        <input type="hidden" id="pet-id-<?=$pet->pet_id?>" value="<?=$pet->pet_id?>">
                        <input type="hidden" id="pet-owner_id-<?=$pet->pet_id?>" value="<?=$pet->owner_id?>">
                        <td><input type="hidden" id="pet-name-<?=$pet->pet_id?>" value="<?=$pet->name?>"><?=$pet->name?></td>
                        <td><input type="hidden" id="pet-owner-<?=$pet->pet_id?>" value="<?=$pet->owner?>"><?=$pet->owner?></td>
                        <td><input type="hidden" id="pet-type-<?=$pet->pet_id?>" value="<?=$pet->type?>"><?=$pet->type?></td>
                        <td style="white-space:nowrap !important; width: 30px !important"><button onclick="showEditPet(<?=$pet->pet_id?>)" class="btn btn-sm btn-info shadow" style="border-radius: 5px"><span><i class="fas fa-edit"></i></span>&nbsp;&nbsp;Update</button></td>
                      </tr>
                      <?php  
                    }
                    ?>

                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div> <!--tab-pne-->


        </div> <!--tab-content-->
      </div>
      <!-- /.card -->
    </div>
  </div>
</div>
</div><!--/. container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<div class="modal fade" id="addPet">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Pet Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Pet Name</label>
                  <input type="text" name="pet_name" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label style="display: block;">Owner</label>
                  <select style="width: 100% !important" name="pet_owner"  class="form-control select select2" value="" required><?=fill_owners()?></select>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Type</label>
                  <input placeholder='' type='text' name="pet_type" class="form-control" list='type_list' required ><datalist id='type_list'><?=fill_types() ?></datalist>
                </div>
              </div>

              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="addPet" value="Register" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editPet">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Pet Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Pet Name</label>
                  <input type="text" id="pet-name" name="pet_name" class="form-control" required>
                  <input type="hidden" id="pet-id" name="pet_id" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label style="display: block;">Owner</label>
                  <select style="width: 100% !important" name="pet_owner" id="pet-owner"  class="form-control select select2" value="" required><?=fill_owners()?></select>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Type</label>
                  <input placeholder='' type='text' id="pet-type" name="pet_type" class="form-control" list='type_list' required ><datalist id='type_list'><?=fill_types() ?></datalist>
                </div>
              </div>

              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="editPet" value="Save" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="addCustomer">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Customer Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Name</label>
                  <input type="text" name="cus_name" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Address</label>
                  <input type="text" name="cus_addr"  class="form-control" value="" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Phone</label>
                  <input type="number" name="cus_phone" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Email</label>
                  <input type="email" name="cus_email"  class="form-control" value="" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="addCustomer" value="Add Customer" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editCustomer">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">Customer Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Name</label>
                  <input type="text" id="cus-name" name="cus_name" class="form-control" required>
                  <input type="hidden" id="cus-id" name="cus_id" class="form-control" required>
                  <input type="hidden" id="cus-old-email" name="cus_old_email" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Address</label>
                  <input type="text" id="cus-addr" name="cus_addr"  class="form-control" value="" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Phone</label>
                  <input type="number" id="cus-phone" name="cus_phone" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Email</label>
                  <input type="email" id="cus-email" name="cus_email"  class="form-control" value="" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="editCustomer" value="Save" class="btn btn-success" value="">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script type="text/javascript">
  function showEditModal(id){
    $('#cus-id').val($('#cus-id-'+id).val());
    $('#cus-name').val($('#cus-name-'+id).val());
    $('#cus-addr').val($('#cus-address-'+id).val());
    $('#cus-phone').val($('#cus-phone-'+id).val());
    $('#cus-email').val($('#cus-email-'+id).val());
    $('#cus-old-email').val($('#cus-email-'+id).val());

    $('#editCustomer').modal().show();
  }

  function showEditPet(id){
    console.log($('#pet-owner_id-'+id).val());
    $('#pet-id').val($('#pet-id-'+id).val());
    $('#pet-name').val($('#pet-name-'+id).val());
    $('#pet-owner').val($('#pet-owner_id-'+id).val()).trigger('change');
    $('#pet-type').val($('#pet-type-'+id).val());

    $('#editPet').modal().show();
  }
</script>

<!-- DataTables  & Plugins -->
<script src="<?=$baseurl ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=$baseurl ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
  $(function () {
    $('#owners-table').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": [
      {
        text: '&nbsp;&nbsp;Add Customer',
        className: 'addCusBtn', 
        action: function ( e, dt, node, config ) {
          $('#addCustomer').modal().show();

        }
      }
      ]
    }).buttons().container().appendTo('#owners-table_wrapper .col-md-6:eq(0)');

    var icon = $('<i>',{class: 'fas fa-plus'});
    $('.addCusBtn').addClass('btn btn-sm btn-outline-success bg-transparent').prepend(icon).css('border-radius','5px').hover(function(){
      $(this).toggleClass("bg-transparent bg-success");
    });

    $('#pets-table').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": [
      {
        text: '&nbsp;&nbsp;Register Pet',
        className: 'addPetBtn', 
        action: function ( e, dt, node, config ) {
          $('#addPet').modal().show();

        }
      }
      ]
    }).buttons().container().appendTo('#pets-table_wrapper .col-md-6:eq(0)');

    var icon = $('<i>',{class: 'fas fa-plus'});
    $('.addPetBtn').addClass('btn btn-sm btn-outline-success bg-transparent').prepend(icon).css('border-radius','5px').hover(function(){
      $(this).toggleClass("bg-transparent bg-success");
    });


  });


</script>

<script>
  $(document).ready(function(){
    var urlHashVal = window.location.hash;
    $('a[data-toggle="pill"]').on('show.bs.tab', function(e) {
      localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    if(urlHashVal.length){

      $('#customersTab a[href="' + urlHashVal + '"]').tab('show');
    } else {

      var activeTab = localStorage.getItem('activeTab');
      if(activeTab){
        $('#customersTab a[href="' + activeTab + '"]').tab('show');
      }
    }
  });



</script>

<?php require_once './footer.php'; ?>