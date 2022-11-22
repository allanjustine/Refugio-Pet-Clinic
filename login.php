<?php
//ob_start();
require "connect_db.php";

function generatePassword() {

$chars = '0123456789abcdfhkmnprstvzABCDFHJKLMNPRSTVZ';
$shuffled = str_shuffle($chars);
$result = mb_substr($shuffled, 0, 8);

return $result;
}

if(isset($_SESSION['email'])&&isset($_SESSION['role'])){
	header('Location:index.php');
	die();
}

if(isset($_POST["btn_login"])){
	$email= trim($_POST["txt_email"]);
	$password = trim($_POST["txt_password"]);
  //echo "<pre>",print_r($_POST),"</pre>";
	$get_user = $pdo->prepare("SELECT users.id as userid, users.email, users.password, users.role, users.status, customers.id, customers.name  FROM `users` inner JOIN customers on users.email=customers.email WHERE users.`email` = :email and `password` = :password UNION SELECT users.id as userid, users.email, users.password, users.role, users.status, employees.id, employees.name  FROM `users` inner JOIN employees on users.email=employees.email  WHERE users.`email` = :email and `password` = :password;");
	$get_user->bindParam(":email",$email);
	$get_user->bindParam(":password",md5($password));
	$get_user->execute();

   // $get_user->rowCount() can be used in if statement

	$row = $get_user->fetch(PDO::FETCH_OBJ);
//var_dump($row);
	if($get_user->rowCount()){
		if($row->email == $email && $row->password){

			if($row->status=='PENDING CONFIRMATION'){

				header('Location:verification.php?email='.$row->email);
				die();

			} 
			if ($row->status=='ACTIVE') {

				$_SESSION["id"] = $row->id;
				$_SESSION["email"] = $row->email;
				$_SESSION["role"] = $row->role;
				$_SESSION["name"] = $row->name;
	        //var_dump("Login Success");
				
				header('Location:index.php');

				$loginSuccess = "Login Successfull";
        // die();
			}


		}
	}else{
		$loginError = "Enter Valid Email and Password";
	}
}

if (isset($_POST['btn_signup'])) {


  $cus_name  = $_POST['name'];
  $cus_addr = $_POST['address'];
  $cus_phone = $_POST['phone'];
  $cus_email = trim($_POST['email']);

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

    /*
    	$_SESSION["id"] = $pdo->lastInsertId();
		$_SESSION["email"] = $cus_email;
		$_SESSION["role"] = 'customer';
	*/

    if ($status=="success") {

      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Registered Successfully",
            text: "<?php echo $cus_name ?>, Has Been Registered\n An Email Sent For Login Credentials",
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

              window.location = "login.php";
          });
        });
      </script>
      <?php
    } else{
      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Registered Successfully",
            text: "<?php echo $cus_name ?>, Has Been Registered\nPlease use this password: <?php echo $pass ?>",
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

              window.location = "login.php";
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
          text: "Registration Failed",
          icon: "error",
          buttons: {
               confirm: {
                text: "OK",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            }
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
          text: "Email Already Exists",
          icon: "error",
          buttons: {
               confirm: {
                text: "OK",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              }
            }
        });
      });
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
	<title>Pet Clinic | Login</title>
	<link rel="icon" type="image/x-icon" href="<?= $baseurl ?>assets/img/logo.png">
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome Icons -->
	<link rel="stylesheet" href="<?= $baseurl ?>assets/plugins/fontawesome-free/css/all.min.css">
	<!-- overlayScrollbars -->
	<link rel="stylesheet" href="<?= $baseurl ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
	
  <!-- ManyChat -->
  <script src="//widget.manychat.com/110357011896250.js" defer="defer"></script>
  <script src="https://mccdn.me/assets/js/widget.js" defer="defer"></script>
  
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

		.sign-in-container {
			left: 0;
			width: 50%;
			z-index: 2;
		}

		.container.right-panel-active .sign-in-container {
			transform: translateX(100%);
		}

		.sign-up-container {
			left: 0;
			width: 50%;
			opacity: 0;
			z-index: 1;
		}

		.container.right-panel-active .sign-up-container {
			transform: translateX(100%);
			opacity: 1;
			z-index: 5;
			animation: show 0.6s;
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

		.overlay-left {
			transform: translateX(-20%);
		}

		.container.right-panel-active .overlay-left {
			transform: translateX(0);
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
	<div id="pre-loader" class="loader">
    <div class="spinner"></div>
	</div>
	<div class="container" id="container">
		<div class="form-container sign-up-container">
			<form action="<?php basename($_SERVER["PHP_SELF"]); ?>" method="post">
				<h1>Create Account</h1>
				<input type="text" name="name" placeholder="Name"  required />
				<input type="text" name="address" placeholder="Address"  required />				
				<input type="number" name="phone" placeholder="Phone"  required />
				<input type="email" name="email" placeholder="Email" required  />
				<button type="submit" class="" name="btn_signup">Sign Up</button>
			</form>
		</div>
		<div class="form-container sign-in-container">
			<form action="<?php basename($_SERVER["PHP_SELF"]); ?>" method="post">
				<h1>Sign in</h1>
				<input type="email" name="txt_email" required placeholder="Email" />
				<input type="password" placeholder="Password" name="txt_password" required/>
				<a href="#" onclick="forgotPass()" >Forgot your password?</a>
				<button type="submit" class="" name="btn_login">Sign In</button>
			</form>
		</div>
		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-left">
					<h1>Welcome Back!</h1>
					<p>To keep connected with us please login with your personal info</p>
					<button class="ghost" id="signIn">Sign In</button>
				</div>
				<div class="overlay-panel overlay-right">
					<h1>Hello, Friend!</h1>
					<p>Enter your personal details and start journey with us</p>
					<button class="ghost" id="signUp">Sign Up</button>
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
		function forgotPass() {
			swal({
				title: "Forgot Password",
	          text: "New password will be sent to your email.",
	          icon: "info",
			  content: {
			    element: "input",
			    attributes: {
			      placeholder: "Enter your email",
			      type: "email",
			      id: "email-fp"
			    },
			  }
			}).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result) {

              $.ajax({
                url: 'ajax.php',
                data: {
                  email: $("#email-fp").val()
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

                    swal('Reset',`Password has been successfully reset but failed to sent new password to ${$('#email-fp').val()}. New Password is: ${data.pass}`,'info');
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
	<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' /* optional */
			});
		});

		<?php if(isset($loginSuccess,$_SESSION["username"]) && !empty($loginSuccess) && !empty($_SESSION["username"])){ ?>
			swal({
				title: "<?php echo $loginSuccess." ".$_SESSION["username"]; ?>",
				text: "Loading",
				icon: "success",
				buttons: false
			});
		<?php } ?>  
		<?php if(isset($loginError)){ ?>
			swal({
				title: "Login Error",
				text: "<?php echo $loginError; ?>",
				icon: "error",
				button: "Ok",
				showCancelButton: false,
				showConfirmButton: false
			});


		<?php } ?>
	</script>
	<script type="text/javascript">
		const signUpButton = document.getElementById('signUp');
		const signInButton = document.getElementById('signIn');
		const container = document.getElementById('container');

		signUpButton.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});

		signInButton.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});
	</script>
</body>
</html>
