<?php

require 'connect_db.php' ;

if(isset($_SESSION['email'])&&isset($_SESSION['role'])){
    header('Location:index.php');
    die();
}

if(isset($_GET['email'])){
    $email = $_GET['email'];

    $get_user = $pdo->prepare("SELECT * from users where email=:email");
    $get_user->bindParam(":email",$email);
    $get_user->execute();

    if($get_user->rowCount()){
        $user = $get_user->fetch(PDO::FETCH_OBJ);

        if($user->status == 'ACTIVE') {
            ?>
              <script type="text/javascript">
                window.addEventListener("load", function() {
                  swal({
                    title: "Already Verified",
                    text: "Account email: <?php echo $email ?>, has already been verified.",
                    icon: "success",
                     buttons: {
                       confirm: {
                        text: "Login",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: true
                      }
                    },
                    closeOnClickOutside: false
                  }).then(function() {

                      window.location = "login.php";
                  });
                });
              </script>
            <?php
        }

    } else{
        ?>
              <script type="text/javascript">
                window.addEventListener("load", function() {
                  swal({
                    title: "Account not Found",
                    text: "Cannot find account email: <?php echo $email ?> in our records.",
                    icon: "error",
                     buttons: false,
                    closeOnClickOutside: false
                  });
                });
              </script>
            <?php

        header('refresh:3,login.php');
    }
} else{
    header('Location:login.php');
    die();
}

if(isset($_POST["btnupdate"])){
    $email = $_GET['email'];

    $oldpassword = trim($_POST["temp_pass"]);
    $newpassword = trim($_POST["new_pass"]);
    $confirmpassword = trim($_POST["confirm_pass"]);

    $select_user = $pdo->prepare("SELECT * from `users` where  `email` = :email and `password` = :oldpassword");

    $select_user ->bindParam(":email",$email);
    $select_user -> bindParam(":oldpassword",md5($oldpassword));
    $select_user -> execute();
    // var_dump("I am here");
    // var_dump($select_user->rowCount());
    if($select_user->rowCount()){
        $row = $select_user->fetch(PDO::FETCH_OBJ);
        // var_dump($row);
        $id = $row->id;
        if($newpassword == $confirmpassword){
          $update_user = $pdo->prepare("UPDATE `users` SET `password`=:newpassword, `status`='ACTIVE' where `id`=:id");
          $update_user->bindParam(":newpassword",md5($newpassword));
          $update_user->bindParam(":id",$id);

         /* if($update_user->rowCount()){ */
        if($update_user->execute()){
              ?>
              <script>
                window.addEventListener("load",showSweetAlert);
                function showSweetAlert(){
                    swal({
                        title:"Account Verified",
                        text:"Your account has been successfully verified.",
                        icon:"success",
                        buttons:false
                    });
                }
              </script>

              <?php 
              header("refresh:2;login.php");
          }else{
              ?>
              <script type="text/javascript">
                window.addEvenListener("load",showSweetAlert);
                function showSweetAlert(){
                    swal({
                        title:"Operation Failed",
                        text:"Please Try After Some Time",
                        icon:"error",
                        button:"Try Again"
                    });
                }
              </script>
              <?php 
          }
        }else{
            //echo "Please Enter New Password and Confirm Password";
            ?>
            <script>
                window.addEventListener("load",showSweetAlert);
                function showSweetAlert(){
                    swal({
                        title:"Password Don't Match",
                        text:"New Password and Confirm Password Should Be Same",
                        icon:"error",
                        button: "Retry"
                    });
                }
            </script>
            <?php 
        }
    }else{
        //echo "Enter Correct Temporary Password";
        ?>
        <script type="text/javascript">
            window.addEventListener("load", showSweetAlert);
            function showSweetAlert(){
                swal({
                    title: "Temporary Password Error",
                    text: "Enter Correct Temporary Password",
                    icon: "error",
                    button: "Retry",
                });
            }
        </script>
        <?php 
    }

  }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pet Clinic | Verification</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= $baseurl ?>assets/plugins/fontawesome-free/css/all.min.css">
    
  <!-- ManyChat -->
  <script src="//widget.manychat.com/110357011896250.js" defer="defer"></script>
  <script src="https://mccdn.me/assets/js/widget.js" defer="defer"></script>
  
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= $baseurl ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <style type="text/css">
        
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        h2 {
            text-align: center;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid #3395ff;
            background-color: #3395ff;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
            0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .verify-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .container.right-panel-active .verify-container {
            transform: translateX(100%);
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container{
            transform: translateX(-100%);
        }

        .overlay {
            background: #0056b3;
            background: -webkit-linear-gradient(to right, #4da3ff, #0056b3);
            background: linear-gradient(to right, #4da3ff, #0056b3);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        .swal-text,.swal-footer{
          text-align: center;
        }

        footer {
            background-color: #222;
            color: #fff;
            font-size: 14px;
            bottom: 0;
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 999;
        }

        footer p {
            margin: 10px 0;
        }

        footer i {
            color: red;
        }

        footer a {
            color: #3c97bf;
            text-decoration: none;
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
    </style>
</head>
<body style="background-color: white ;background: url(<?=$baseurl?>assets/img/bg.png) center center; ">
    <div class="container" id="container">
        <div class="form-container verify-container">
            <form action="<?php basename($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>Verify Account</h1>
                <input type="password" name="temp_pass" placeholder="Temporary Password"  required />                
                <input type="password" name="new_pass" placeholder="New Password"  required />
                <input type="password" name="confirm_pass" placeholder="Confirm New Password" required  />
                <a href="#" onclick="resendPass('<?=$email?>')" >Resend Password</a>
                <button type="submit" class="" name="btnupdate">Verify</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Please verify your account to use our services. Check your email (inbox or spam) for the temporary password.</p>
                    <button onclick="location.href='login.php'" class="ghost" id="back">Login</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>
            <strong>Copyright &copy; <?php echo date('Y'); ?><a href="http://localhost/refugio-pet-clinic/"> Pet Clinic</a>.</strong>
            All rights reserved.
        </p>
    </footer>
    <!-- /.login-box -->

    <!-- jQuery 3 -->
    <script src="<?=$baseurl;?>assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?= $baseurl?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- iCheck -->
    <script src="<?php echo $baseurl; ?>assets/plugins/iCheck/icheck.min.js"></script>
    <script src="<?= $baseurl?>assets/plugins/sweetalert2/sweetalert.js"></script>
        <script type="text/javascript">
        function resendPass(email) {
            swal({
                title: "Resend Password?",
              text: "New temporary password will be sent to your email: "+email,
              icon: "info",
               buttons: {
               confirm: {
                text: "Resend",
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
                  email: email
                },
                type: 'POST',
                beforeSend: function(){
                  $("#pre-loader").css("display", "flex");
                },
                success: function(res){
                  //console.log(res);
                  const data = JSON.parse(res);

                  if(data.response == 'ok'){

                    swal('Email Sent','New password was sent to your email','success')
                  } else if ( data.response == "not sent") {

                    swal('Reset',`Password has been successfully reset but failed to sent new password to ${email}. New Password is: ${data.pass}`,'info');
                  } else if( data.response == "not found"){
                    swal('Not Found','Email not found in the our records','error')
                  }else {

                    swal('Error','Error occurred while reseting your password','error')                  }

                  $("#pre-loader").css("display", "none");

                }
              });
             
            }
          });;
        }
    </script>
</body>
</html>
