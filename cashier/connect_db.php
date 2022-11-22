<?php
ob_start();

date_default_timezone_set('Asia/Singapore');

$baseurl="http://localhost/refugio-pet-clinic/";
if(session_status()==PHP_SESSION_NONE){
    session_start(); 
}
try{
	$pdo = new pdo("mysql:host=localhost;dbname=pet-clinic","root","");
}catch(Exception $e){
	//var_dump($e->getMessage());

}

?>
