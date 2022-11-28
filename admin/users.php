<?php require_once './header.php';
include('inc/analytics.php');

function generatePassword() {

$chars = '0123456789abcdfhkmnprstvzABCDFHJKLMNPRSTVZ';
$shuffled = str_shuffle($chars);
$result = mb_substr($shuffled, 0, 8);

return $result;
}

 
if (isset($_POST['addUser'])) {

  $emp_name  = $_POST['emp_name'];
  $emp_addr = $_POST['emp_addr'];
  $emp_phone = $_POST['emp_phone'];
  $emp_email = $_POST['emp_email'];
  $emp_role = $_POST['emp_role'];

  $pass = generatePassword();

   $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $emp_email);
    $check_email->execute();
  if (!$check_email->rowCount()) {


  $insert_emp = $pdo->prepare("INSERT INTO users ( email, password, role ) VALUES( :email, :password, :role )");
  $insert_emp->bindParam(":email", $emp_email);
  $insert_emp->bindParam(":password", password_hash($pass, PASSWORD_DEFAULT) );
  $insert_emp->bindParam(":role", $emp_role );
  $insert_emp->execute();

  $to = $emp_email;
  $subject = "Pet Clinic Login Credentials";
  $body = "Use this password: ".$pass;
  $email = $emp_email;
  $status = '';

  
  if(mail($to,$subject,$body,$email)) {
    $status = "success";
  } else {
    $status = "failed";
  }
  

    //Insert Captured information to a database table
  $insert = $pdo->prepare("INSERT INTO employees ( name, address, phone, email ) VALUES( :name, :address, :phone, :email )");
  $insert->bindParam(":name", $emp_name);
  $insert->bindParam(":address", $emp_addr);
  $insert->bindParam(":phone", $emp_phone);
  $insert->bindParam(":email", $emp_email);

  $insert->execute();

  if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
    if ($status=="success") {

      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "User Added Successfully",
            text: "<?php echo $emp_name ?>, Has Been Inserted\n An Email Sent For Login Credentials",
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
              window.location = "users.php";
          });
        });
      </script>
      <?php
    } else{
      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "User Added Successfully",
            text: "<?php echo $emp_name ?>, Has Been Inserted\nPlease use this password: <?php echo $pass ?>",
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
            window.location = "users.php";
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


  header("refresh:3,users.php");
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


  header("refresh:3,users.php");
  }


}

if (isset($_POST['editUser'])) {

 $emp_id  = $_POST['emp_id'];
  $emp_name  = $_POST['emp_name'];
  $emp_addr = $_POST['emp_addr'];
  $emp_phone = $_POST['emp_phone'];
  $emp_email = trim($_POST['emp_email']);
  $emp_role = $_POST['emp_role'];
  $emp_old_email = trim($_POST['emp_old_email']);


   $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $emp_email);
    $check_email->execute();
  if (!$check_email->rowCount() || $emp_email == $emp_old_email) {



  $update_user = $pdo->prepare("UPDATE users set email=:email, role=:role  where email=:old_email");
  $update_user->bindParam(":email", $emp_email);
  $update_user->bindParam(":role", $emp_role );
  $update_user->bindParam(":old_email", $emp_old_email );
  $update_user->execute();

    //Insert Captured information to a database table
 $update = $pdo->prepare("UPDATE employees set name=:name, address=:address, phone=:phone, email=:email where id=:id ");
 $update->bindParam(":name", $emp_name);
 $update->bindParam(":address", $emp_addr);
 $update->bindParam(":phone", $emp_phone);
 $update->bindParam(":email", $emp_email);
 $update->bindParam(":id", $emp_id);

 if ($update->execute()) {
            //echo "Application Submitted Successfully";
  ?>



  <script type="text/javascript">
    window.addEventListener("load", function() {
      swal({
        title: "User Updated Successfully",
        text: "<?php echo $emp_name ?>, Has Been Updated",
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
        text: "Update User Failed",
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


header("refresh:3,users.php");
}

 ?>

<script type="text/javascript">
  $('#nav-users').find('a').toggleClass('active');
</script>

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
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-doctor"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ADMINS</span>
              <span class="info-box-number"><?=$admins?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-tag"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">CASHIERS</span>
              <span class="info-box-number"><?=$cashiers?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">CUSTOMERS</span>
              <span class="info-box-number"><?=$verified+$unconfirmed?></span>
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

           <div class="card-body">
            <table id="users-table" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>STAFF NAME</th>
                  <th>ADDRESS</th>
                  <th>PHONE</th>
                  <th>EMAIL</th>
                  <th>ROLE</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $get_employee = $pdo->prepare("SELECT employees.id, employees.name, employees.address, employees.phone, employees.email, users.role FROM `employees` inner join users on employees.email=users.email where users.role!='customer' and users.email!=:cur_email ORDER BY `id` DESC");
                $get_employee->bindParam(":cur_email",$_SESSION['email']);
                $get_employee->execute();
                while ($employee = $get_employee->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                  ?>

                  <tr>
                    <input type="hidden" id="emp-id-<?=$employee->id?>" value="<?=$employee->id?>">
                    <td><input type="hidden" id="emp-name-<?=$employee->id?>" value="<?=$employee->name?>"><?=$employee->name?></td>
                    <td><input type="hidden" id="emp-address-<?=$employee->id?>" value="<?=$employee->address?>"><?=$employee->address?></td>
                    <td><input type="hidden" id="emp-phone-<?=$employee->id?>" value="<?=$employee->phone?>"><?=$employee->phone?></td>
                    <td><input type="hidden" id="emp-email-<?=$employee->id?>" value="<?=$employee->email?>"><?=$employee->email?></td>
                    <td><input type="hidden" id="emp-role-<?=$employee->id?>" value="<?=$employee->role?>">

                      <?php 
                      if($employee->role=="admin"){
                        echo '<span class="badge badge-primary">ADMIN</span>';
                      } else{
                         echo '<span class="badge badge-warning">CASHIER</span>';
                      }

                      ?>

                    </td>
                    <td style="white-space:nowrap !important; width: 30px !important"><button onclick="showEditModal(<?=$employee->id?>)" class="btn btn-sm btn-info shadow" style="border-radius: 5px"><span><i class="fas fa-edit"></i></span>&nbsp;&nbsp;Update</button></td>
                  </tr>
                  <?php  
                }
                ?>
              </tbody>
              <tfoot>

              </tfoot>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
</div>
</div><!--/. container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="addUser">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content" style="">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">User Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Name</label>
                  <input type="text" name="emp_name" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Address</label>
                  <input type="text" name="emp_addr"  class="form-control" value="" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Phone</label>
                  <input type="number" name="emp_phone" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Email</label>
                  <input type="email" name="emp_email"  class="form-control" value="" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Role</label>
                  <select name="emp_role"  class="form-control" value="" required>
                    <option value="cashier">Cashier</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-6">
                  <input type="submit" name="addUser" value="Add User" class="btn btn-success" value="">
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

<div class="modal fade" id="editUser">
  <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
    <div class="modal-content">
      <div class="modal-body">
        <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
          <div class="card-header border-0" >
            <h3 style="display: inline !important">User Info</h3 >
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="float-right" aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12">
                  <label>Name</label>
                  <input type="text" id="emp-name" name="emp_name" class="form-control" required>
                  <input type="hidden" id="emp-id" name="emp_id" class="form-control" required>
                  <input type="hidden" id="emp-old-email" name="emp_old_email" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Address</label>
                  <input type="text" id="emp-addr" name="emp_addr"  class="form-control" value="" required>
                </div>
              </div>
              <br> 
              <div class="form-row">
                <div class="col-md-12">
                  <label>Phone</label>
                  <input type="number" id="emp-phone" name="emp_phone" class="form-control" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Email</label>
                  <input type="email" id="emp-email" name="emp_email"  class="form-control" value="" required>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12">
                  <label>Role</label>
                  <select name="emp_role" id="emp-role"  class="form-control" value="" required>
                    <option value="cashier">Cashier</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>
              <br>
              <div class="form-row">
                <div class="col-md-12" style="text-align: left">

                  <!--<button type="button" id="reset-btn" class="btn btn-warning" data-dismiss="modal">Reset Password</button>-->
                  <input type="submit" name="editUser" value="Save" class="btn btn-success" value="">
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
    $('#emp-id').val($('#emp-id-'+id).val());
    $('#emp-name').val($('#emp-name-'+id).val());
    $('#emp-addr').val($('#emp-address-'+id).val());
    $('#emp-phone').val($('#emp-phone-'+id).val());
    $('#emp-email').val($('#emp-email-'+id).val());
    $('#emp-role').val($('#emp-role-'+id).val());
    $('#emp-old-email').val($('#emp-email-'+id).val());

    $('#editUser').modal().show();
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


    $('#users-table').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": [
      {
        text: '&nbsp;&nbsp;Add User',
        className: 'addUserBtn', 
        action: function ( e, dt, node, config ) {
          $('#addUser').modal().show();

        }
      }
      ]
    }).buttons().container().appendTo('#users-table_wrapper .col-md-6:eq(0)');

    var icon = $('<i>',{class: 'fas fa-plus'});
    $('.addUserBtn').addClass('btn btn-sm btn-outline-success bg-transparent').prepend(icon).css('border-radius','5px').hover(function(){
      $(this).toggleClass("bg-transparent bg-success");
    });


    $("#reset-btn").click(function(){
      swal({
            title: `Reset password for ${$('#emp-name').val()}?`,
            text: `New Password Will Be Sent to ${$('#emp-old-email').val()}`,
            icon: "info",
             buttons: {
               confirm: {
                text: "Continue",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              },
              cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
              }
            },
            closeOnClickOutside: false
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result) {

              $.ajax({
                url: 'ajax.php',
                data: {
                  email: $('#emp-old-email').val()
                },
                type: 'POST',
                beforeSend: function(){
                  $("#pre-loader").css("display", "flex");
                },
                success: function(res){
                    console.log($('#emp-old-email').val());
                  console.log(res);
                  const data = JSON.parse(res);

                  if(data.response == 'ok'){

                    toastr.success(`New Password Has Been Sent to ${$('#emp-old-email').val()}`);
                  } else if ( data.response == "not sent") {

                    toastr.info(`Failed to Sent New Password to ${$('#emp-old-email').val()}. New Password is: ${data.pass}`);
                  } else {

                    toastr.error(`Password Reset Failed`);
                  }

                  $("#pre-loader").css("display", "none");

                }
              });
             
            }
          });
    });


  });


</script>


<?php require_once './footer.php'; ?>