<?php

require_once './connect_db.php';

if(isset($_FILES['file']['name'])){
    /* Getting file name */
    $filename = $_FILES['file']['name'];
      
    /* Location */
    $location = "uploads/img/profile/".$filename;
    $uploadOk = 1;
      
    if($uploadOk == 0){
       echo 0;
    }else{
       /* Upload file */
       if(move_uploaded_file($_FILES['file']['tmp_name'], $location)){
            echo $location;
            $update = $pdo->prepare("UPDATE  users set profile=:profile where email =:email");
            $update->bindParam(":profile", $filename);
            $update->bindParam(":email", $_SESSION['email']);
            $update->execute();
       }else{
            echo 0;
        }    
    }
}

if(isset($_GET['item'])) {

    $q = $_GET['item'];
    $get_data = $pdo->prepare("SELECT price,stock from products where id =".$q."");
    $get_data->execute();

    $array = $get_data->fetchAll( PDO::FETCH_ASSOC );
    echo json_encode($array);
}

if (isset($_POST["delCus"])){
    $id = $_POST["id"];
    $email = $_POST["delEmail"];

    $get_user = $pdo->prepare("SELECT customer_id from events WHERE customer_id =:id UNION SELECT customer_id from orders where customer_id=:id UNION SELECT id from users WHERE status='ACTIVE' and email=:email ");
    $get_user->bindParam(":id",$id);
    $get_user->bindParam(":email",$email);
    $get_user->execute();

    $row = $get_user->fetch(PDO::FETCH_OBJ);
    if($get_user->rowCount()){

        echo json_encode(array("response" => "exist"));
    } else {

        $delete = $pdo->prepare("DELETE from users where email=:email");
        $delete->bindParam(":email",$email);
        $delete->execute();

        $delete_cus = $pdo->prepare("DELETE from customers where id=:id");
        $delete_cus->bindParam(":id",$id);
        $delete_cus->execute();

        echo json_encode(array("response" => "no records"));
    }
}

if(isset($_GET['order'])) {

    $q = $_GET['order'];
    $get_data = $pdo->prepare("SELECT order_details.id as order_did, product_id, products.name, qty,products.stock, products.price, ( qty * products.price) as total from order_details inner join products on order_details.product_id=products.id where order_id =".$q."");
    $get_data->execute();

    $array = $get_data->fetchAll( PDO::FETCH_ASSOC );
    echo json_encode($array);
}


if(isset($_GET['delete'])) {

    $q = $_GET['delete'];
    $qty = $_GET['qty'];
    $pid = $_GET['pid'];

    $update = $pdo->prepare("UPDATE products set stock=(stock+:qty) where id=:pid");
    $update->bindParam(":qty", $qty);
    $update->bindParam(":pid", $pid);
    $update->execute();

    $delete = $pdo->prepare("DELETE from order_details where id =:id");
    $delete->bindParam(":id", $q);
    $delete->execute();

    if($delete->rowCount()>0){

        echo json_encode(array("response" => "ok"));
    } else {
        echo json_encode(array("response" => "error"));
    }
}

if(isset($_GET['cancel'])) {

    $q = $_GET['cancel'];
    
    $delete = $pdo->prepare("DELETE from orders where id =:id");
    $delete->bindParam(":id", $q);
    $delete->execute();

    if($delete->rowCount()>0){

        echo json_encode(array("response" => "ok"));
    } else {
        echo json_encode(array("response" => "error"));
    }
}


if(isset($_POST['Event'])) {

    $id = $_POST['Event'][0];
    $start = $_POST['Event'][1];
    $end = $_POST['Event'][2];
    
    $update = $pdo->prepare("UPDATE  events set start=:start, end=:end where id =:id");
    $update->bindParam(":id", $id);
    $update->bindParam(":start", $start);
    $update->bindParam(":end", $end);
    

    if($update->execute()){

        echo json_encode(array("response" => "ok"));
    } else {
        echo json_encode(array("response" => "error"));
    }
}

if(isset($_POST['dateChange'])) {

    $get_data = $pdo->prepare("SELECT events.id, events.appt_code, events.customer_id, customers.name, events.title, events.description, events.start,DATE_FORMAT(events.start, '%h:%i %p') as time, events.end from events inner join customers on events.customer_id=customers.id where status='PENDING' and start >= concat(curdate(),' ','00:00:00') and start <= concat(curdate(),' ','23:59:59') ORDER BY `start` ASC");
    $get_data->execute();

    $array = $get_data->fetchAll( PDO::FETCH_ASSOC );
    echo json_encode($array);
}

if(isset($_POST['orderInfo'])) {

    $id = $_POST['orderInfo'];

    $get_data = $pdo->prepare("SELECT orders.id, orders.code, orders.status, orders.payment_method, orders.ref_num, customers.name,  DATE_FORMAT(orders.date_paid, '%d - %M %Y %h:%i %p') as date_paid, DATE_FORMAT(orders.date, '%d - %M %Y %h:%i %p') as order_date from orders left join customers on orders.customer_id=customers.id  where orders.id=:id");
    $get_data->bindParam(":id",$id);
    $get_data->execute();

    $array = $get_data->fetchAll( PDO::FETCH_ASSOC );
    echo json_encode($array);
}

if(isset($_POST['serviceInfo'])) {

    $id = $_POST['serviceInfo'];

    $get_data = $pdo->prepare("SELECT services.id as service_id,services.status,services.bill, services.ref_num, services.payment_method, events.appt_code, events.customer_id, customers.name, events.title, events.description, DATE_FORMAT(services.date_paid, '%d - %M %Y %h:%i %p') as date_paid, DATE_FORMAT(events.start, '%d - %M %Y %h:%i %p') as start from services inner join events on services.event_id=events.id inner join customers on events.customer_id=customers.id where services.id=:id");
    $get_data->bindParam(":id",$id);
    $get_data->execute();

    $array = $get_data->fetchAll( PDO::FETCH_ASSOC );
    echo json_encode($array);
}

if(isset($_POST['note'])) {

    $id = $_POST['event_id'];
    $note = $_POST['note'];

    $insert = $pdo->prepare("INSERT into decline_info (event_id,note) values (:event_id,:note)");
    $insert->bindParam(":event_id", $id);
    $insert->bindParam(":note",$note);
    $insert->execute();

    if (isset($_POST['cancel'])){

        $update = $pdo->prepare("UPDATE  events set status='CANCELLED' where id =:id");
    } else {

        $update = $pdo->prepare("UPDATE  events set status='DECLINED' where id =:id");
    }

    $update->bindParam(":id", $id);
    

    if($update->execute()){

        echo json_encode(array("response" => "ok"));
    } else {
        echo json_encode(array("response" => "error"));
    }
}

if(isset($_POST['bill'])) {

    $id = $_POST['id'];
    $bill = $_POST['bill'];

    if($bill==0){
        $insert = $pdo->prepare("INSERT into services (event_id,bill,status,ref_num) values (:event_id,:bill,'PAID','ZERO BILLING')");
    } else {
        $insert = $pdo->prepare("INSERT into services (event_id,bill) values (:event_id,:bill)");
    }

    $insert->bindParam(":event_id", $id);
    $insert->bindParam(":bill",$bill);
    $insert->execute();

    
    $update = $pdo->prepare("UPDATE  events set status='DONE' where id =:id");
    $update->bindParam(":id", $id);
    

    if($update->execute()){

        echo json_encode(array("response" => "ok"));
    } else {
        echo json_encode(array("response" => "error"));
    }
}

function generatePassword() {

$chars = '0123456789abcdfhkmnprstvzABCDFHJKLMNPRSTVZ';
$shuffled = str_shuffle($chars);
$result = mb_substr($shuffled, 0, 8);

return $result;
}

if(isset($_POST['email'])) {

    $email = $_POST['email'];
    $pass = trim(generatePassword());

    $update = $pdo->prepare("UPDATE  users set password=:password where email =:email");
    $update->bindParam(":password", $pass);
    $update->bindParam(":email", $email);

    if($update->execute()){

      $subject = "Pet Clinic Login Credentials";
      $body = "Use this password: ".$pass;
      $status = '';

        if(mail($email,$subject,$body,$email)){
             echo json_encode(array("response" => "ok"));
        } else {
            echo json_encode(array("response" => "not sent","pass" => $pass));
        }
         //echo json_encode(array("response" => "not sent","pass" => $pass));
     
    } else {
        echo json_encode(array("response" => "error"));
    }
}

if(isset($_POST['changePass'])) {

    $email = $_POST['user_email'];
    $pass = trim($_POST['pass']);
    $pass = password_hash($pass, PASSWORD_DEFAULT);
    
    $update = $pdo->prepare("UPDATE  users set password=:password where email =:email");
    $update->bindParam(":password", $pass);
    $update->bindParam(":email", $email);

    if($update->execute()){
        echo json_encode(array("response" => "success"));
     
    } else {
        echo json_encode(array("response" => "error"));
    }
}

if(isset($_POST['userInfo'])) {

    $name = $_POST['name'];
    $email = $_POST['Email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $old_email = $_POST['old_email'];


    $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $email);
    $check_email->execute();
    if (!$check_email->rowCount() || $email == $old_email) {



      $update_user = $pdo->prepare("UPDATE users set email=:email where email=:old_email");
      $update_user->bindParam(":email", $email);
      $update_user->bindParam(":old_email", $old_email );
      $update_user->execute();

        //Insert Captured information to a database table
     $update = $pdo->prepare("UPDATE employees set name=:name, address=:address, phone=:phone, email=:email where email=:old_email ");
     $update->bindParam(":name", $name);
     $update->bindParam(":address", $address);
     $update->bindParam(":phone", $phone);
     $update->bindParam(":email", $email);
     $update->bindParam(":old_email", $old_email);

    if ($update->execute()) {
        echo json_encode(array("response" => "success"));
    } else{
        echo json_encode(array("response" => "error","message" => "Error Occurred While Saving"));
    }


    } else {
        echo json_encode(array("response" => "error","message" => "Email Already Exists"));
    }

    
}


?>