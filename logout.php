<?php
session_start();
unset($_SESSION['email']);
unset($_SESSION['role']);
session_destroy();
header("Location:http://localhost/refugio-pet-clinic/");
exit;