<?php require_once './header.php'; 


$get_info = $pdo->prepare("SELECT employees.name,employees.address,employees.phone,employees.email,users.role, users.password,users.profile from employees inner join users on employees.email=users.email where employees.email=:email");
  $get_info->bindParam(":email",$_SESSION['email']);
  $get_info->execute();

  $info = $get_info->fetch();
?>

<script type="text/javascript">
  $('#nav-prof').find('a').toggleClass('active');
</script

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper p-3" style="background-color: white ;background: url(<?=$baseurl?>assets/img/bg.png) center center; ">
  <!-- Content Header (Page header) -->
  
<!-- /.content-header -->

  <!-- Main content -->
    <section class="content">
      <div class="container-fluid" >


        <div class="row" style="margin-top: 60px;">
            <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card shadow">
              <div class="card-body box-profile">
                  <style type="text/css">
                    .profile-pic-div .avatar-edit {
                      position: absolute;
                      right: 12px;
                      z-index: 1;
                      top: 10px;
                    }
                    .profile-pic-div .avatar-edit input {
                      display: none;
                    }
                    .profile-pic-div .avatar-edit input + label {
                      display: inline-block;
                      width: 34px;
                      height: 34px;
                      margin-bottom: 0;
                      border-radius: 100%;
                      background: #FFFFFF;
                      border: 1px solid transparent;
                      box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                      cursor: pointer;
                      font-weight: normal;
                      transition: all 0.2s ease-in-out;
                    }
                    .profile-pic-div .avatar-edit input + label:hover {
                      background: #f1f1f1;
                      border-color: #d6d6d6;
                    }
                    .profile-pic-div .avatar-edit input + label:after {
                      content: "\f040";
                      font-family: 'FontAwesome';
                      color: #757575;
                      position: absolute;
                      top: 10px;
                      left: 0;
                      right: 0;
                      text-align: center;
                      margin: auto;
                    }
                  </style>
              
                <div class="text-center profile-pic-div">
                 
                  <img class="profile-user-img img-fluid img-circle"  src="<?=$baseurl?>cashier/uploads/img/profile/<?=$info['profile']?>" alt="User profile picture" id="photo">
                  <form method="post" action="" enctype="multipart/form-data" id="myform">
                    <div class="avatar-edit">
                      <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" />
                      <label for="imageUpload"></label>
                    </div>
                  </form>
                </div>

                <h3 class="profile-username text-center"><?=$info['name']?></h3>

                <p class="text-muted text-center"><?=$info['role']?></p>
                 <hr>
                <div class="mb-3">
                  
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>

                <p class="text-muted"><?=$info['address']?></p>

                <hr>

                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>

                <p class="text-muted"><?=$info['email']?></p>

                <hr>

                <strong><i class="fas fa-phone mr-1"></i> Phone</strong>

                <p class="text-muted"><?=$info['phone']?></p>

                </div>

                <a href="#" id="logout-btn" class="btn btn-primary btn-block"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i><b>Logout</b></a>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card shadow">
              <div class="card-header">
                
                 <h3 class="card-title p-2">
                  <i class="fa fa-circle-info mr-2"></i>
                  <strong>ABOUT ME</strong>
                </h3>
                <!-- tools card -->
                <div class="card-tools mr-1">
                  
                  <button type="button" id="save-btn" class="btn btn-outline-success"><span class="fa fa-floppy-disk mr-2"></span>Save Changes</button>
                </div>
                <!-- /. tools -->


              </div><!-- /.card-header -->
              <div class="card-body">
                  <form class="form-horizontal" id="info-form">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputName" name="name" value="<?=$info['name']?>" placeholder="Name" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputAddress" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputAddress" name="address" value="<?=$info['address']?>" placeholder="Address" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputPhone" class="col-sm-2 col-form-label">Phone</label>
                        <div class="col-sm-10">
                          <input type="number" class="form-control" name="phone" id="inputPhone" value="<?=$info['phone']?>" placeholder="Phone" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" name="email" value="<?=$info['email']?>" id="inputEmail" placeholder="Email" required>
                          <input type="hidden" class="form-control" name="old+emal" id="inputOldEmail" placeholder="Email" value="<?=$_SESSION['email']?>" required>
                        </div>
                      </div>
                    </form>
                      <hr>
                      <div class="row" style="justify-content: space-between;">
                        <h6 class="card-title heading-small text-muted mb-4 p-2">Change Password</h6>
                       <div class="card-tools mr-1">
                        <button type="button" id="save-pass-btn" class="btn btn-outline-info btn-sm"><span class="fa fa-floppy-disk mr-2"></span>Save Password</button>
                      </div>
                      </div>
                        
                      <div class="form-group row">
                        <div class="col-md-4">
                          <label for="oldPassword" class="col-form-label">Old Password</label>
                          <div class="">
                            <input type="password" class="form-control" name="oldpass" id="oldPassword" placeholder="Old Password">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for="newPassword" class="col-form-label">New Password</label>
                          <div class="">
                            <input type="password" class="form-control" name="newpass" id="newPassword" placeholder="New Password">
                          </div>
                        </div>
                         <div class="col-md-4">
                          <label for="confirmPassword" class="col-form-label">Confirm Password</label>
                          <div class="">
                            <input type="password" class="form-control" name="cpass" id="confirmPassword" placeholder="Confirm Password">
                          </div>
                        </div>
                        
                      </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script defer src="https://cdn.crop.guide/loader/l.js?c=YXCMFZ"></script>
<script type="text/javascript">
  $("#save-btn").click(function(){

    if(
      $("#inputName").val().trim().length == 0 ||
      $("#inputAddress").val().trim().length == 0 ||
      $("#inputPhone").val().trim().length == 0 ||
      $("#inputEmail").val().trim().length == 0
    ) {
      swal({
            title: `Fields Can't Be Blank`,
            text: `Please Fill All Fields`,
            icon: "error",
             buttons: {
               confirm: {
                text: "Ok",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            },
            closeOnClickOutside: true
          })
    } else{
      $.ajax({
                url: 'ajax.php',
                data: {
                  userInfo: 'info',
                  name: $("#inputName").val().trim(),
                  address: $("#inputAddress").val().trim(),
                  phone: $("#inputPhone").val().trim(),
                  Email: $("#inputEmail").val().trim(),
                  old_email: $("#inputOldEmail").val().trim()

                },
                type: 'POST',
                beforeSend: function(){
                  $("#pre-loader").css("display", "flex");
                },
                success: function(res){
                  console.log(res);
                  const data = JSON.parse(res);

                  if(data.response == 'success'){

                    swal({
                      title: `Saved`,
                      text: `All Changes Has Been Saved`,
                      icon: "success",
                       buttons: {
                         confirm: {
                          text: "Ok",
                          value: true,
                          visible: true,
                          className: "",
                          closeModal: true
                        }
                      },
                      closeOnClickOutside: false
                    }).then((result) => {
                      /* Read more about isConfirmed, isDenied below */
                      if (result) {

                        if($("#inputEmail").val().trim() != $("#inputOldEmail").val().trim()){
                            swal({
                              title: `Notice`,
                              text: `You Have Changed Your Email\nYou Will Be Logged Out`,
                              icon: "info",
                               buttons: {
                                 confirm: {
                                  text: "Ok",
                                  value: true,
                                  visible: true,
                                  className: "",
                                  closeModal: true
                                }
                              },
                              closeOnClickOutside: false
                            }).then((result) => {
                               location.href = "logout.php";
                            });
                        } else{

                          location.reload();
                        }

                       
                      }
                    });

                  } else {

                    swal({
                      title: `Error`,
                      text: data.message ,
                      icon: "error",
                       buttons: {
                         confirm: {
                          text: "Ok",
                          value: true,
                          visible: true,
                          className: "",
                          closeModal: true
                        }
                      },
                      closeOnClickOutside: true
                    })
                  }

                  $("#pre-loader").css("display", "none");

                }
              });
    }

  });

  $("#save-pass-btn").click( function(){
    if(
      $("#oldPassword").val().trim().length == 0 ||
      $("#newPassword").val().trim().length == 0 ||
      $("#confirmPassword").val().trim().length == 0 
    ) {
      swal({
            title: `Fields Can't Be Blank`,
            text: `Please Fill All Fields`,
            icon: "error",
             buttons: {
               confirm: {
                text: "Ok",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            },
            closeOnClickOutside: true
          })
    } else{

      var oldpass = "<?=$info['password']?>";

      if(oldpass.trim()==$("#oldPassword").val().trim()) {

        if( $("#newPassword").val().trim()==$("#confirmPassword").val().trim()){

          $.ajax({
            url: 'ajax.php',
                data: {
                  changePass: 'pass',
                  user_email: '<?=$_SESSION['email']?>',
                  pass: $("#newPassword").val().trim(),

                },
                type: 'POST',
                beforeSend: function(){
                  $("#pre-loader").css("display", "flex");
                },
                success: function(res){

                  console.log(res);
                  const data = JSON.parse(res);

                  if(data.response == 'success'){
                      swal({
                        title: `Saved`,
                        text: 'Password Changed Successfully' ,
                        icon: "success",
                        buttons: {
                         confirm: {
                          text: "Ok",
                          value: true,
                          visible: true,
                          className: "",
                          closeModal: true
                        }
                      },
                      closeOnClickOutside: true
                    });

                    $("#oldPassword").val('');
                    $("#newPassword").val('');
                    $("#confirmPassword").val('');
                  } else {
                       swal({
                        title: `Error`,
                        text: 'Unable To Change Password' ,
                        icon: "error",
                        buttons: {
                         confirm: {
                          text: "Ok",
                          value: true,
                          visible: true,
                          className: "",
                          closeModal: true
                        }
                      },
                      closeOnClickOutside: true
                    })
                  }


                  $("#pre-loader").css("display", "none");
                }

          });

        } else{
          swal({
            title: `Error`,
            text: 'Passwords Do Not Match' ,
            icon: "error",
            buttons: {
             confirm: {
              text: "Ok",
              value: true,
              visible: true,
              className: "",
              closeModal: true
            }
          },
          closeOnClickOutside: true
        })
        }

      } else {
         swal({
          title: `Error`,
          text: 'Incorrect Password' ,
          icon: "error",
          buttons: {
           confirm: {
            text: "Ok",
            value: true,
            visible: true,
            className: "",
            closeModal: true
          }
        },
        closeOnClickOutside: true
      })
     }
    }

  });

  $("#logout-btn").click(function(){
    swal({
            title: `Proceed to Logout?`,
            text: ``,
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

              location.href = "logout.php";
             
            }
          });
    });
</script>
<script>
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#photo').attr('src', e.target.result);
        $('#photo').hide();
        $('#photo').fadeIn(650);

        $('#sidebar-img').attr('src', e.target.result);
        $('#sidebar-img').hide();
        $('#sidebar-img').fadeIn(650);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imageUpload").change(function() {
      readURL(this);
      var fd = new FormData();
      var files = $('#imageUpload')[0].files[0];
      fd.append('file', files);
       console.log(files);
      $.ajax({
          url: 'ajax.php',
          type: 'post',
          data: fd,
          contentType: false,
          processData: false,
          beforeSend: function(){
            $("#pre-loader").css("display","flex");
          },
          success: function(response){
            console.log(response);
              if(response != 0){
                  toastr.success("Profile picture has changed.");
              }
              else{
                  toastr.error("Error occurred while changing your profile picture!")
              }
            $("#pre-loader").css("display","none");
          }
      });
  });
</script>

<?php require_once './footer.php'; ?>