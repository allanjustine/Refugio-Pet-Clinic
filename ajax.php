<?php

require_once './connect_db.php';
include('inc/code-generator.php');

if(isset($_FILES['file']['name'])){
    /* Getting file name */
    $filename = $_FILES['file']['name'];
      
    /* Location */
    $location = "assets/img/profile/".$filename;
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

    $insert = $pdo->prepare("INSERT into services (event_id,bill) values (:event_id,:bill)");
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

    $check_email = $pdo->prepare("SELECT * FROM `users` where `email` = :email");
    $check_email->bindParam(":email", $email);
    $check_email->execute();

    if ($check_email->rowCount()) {

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
         
        } else {
            echo json_encode(array("response" => "error"));
        }
    } else{
        echo json_encode(array("response" => "not found"));
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

if(isset($_POST['updateSched'])){
    
    $query = "SELECT DATE_FORMAT(`start`, '%d - %M %Y %h:%i %p') as date, status FROM `events` where customer_id=:id and start>=now() and  status!='DECLINED' and status!='CANCELLED' and isPersonal!='true' order by date asc LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result==null) {
      $appt='NO APPOINTMENT SCHEDULED!';
    } else{
      if($result['status']=='FOR APPROVAL'){
        $appt = $result['date'].'<span class="badge badge-info ml-2">'.$result['status'].'</span>';

      } else if ($result['status']=='PENDING'){
        $appt = $result['date'].'<span class="badge badge-warning ml-2">'.$result['status'].'</span>';

      } else if ($result['status']=='DONE'){
        $appt = $result['date'].'<span class="badge badge-success ml-2">'.$result['status'].'</span>';
      } else{
        $appt = $result['date'].'<span class="badge badge-danger ml-2">'.$result['status'].'</span>';
      }
    }

    echo json_encode(array("sched" => $appt));

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
     $update = $pdo->prepare("UPDATE customers set name=:name, address=:address, phone=:phone, email=:email where email=:old_email ");
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

if(isset($_POST['changeQty'])){

    $order_did = $_POST['did'];
    $qty_before = $_POST['qty_before'];
    $item_qty = $_POST['item_qty'];
    $item_id = $_POST['pid'];
    $total = $_POST['total'];

    if( $qty_before > $item_qty ){
        $add_qty = $qty_before - $item_qty;

        $update_qty = $pdo->prepare("UPDATE products set stock=(stock+:add_qty) where id=:id");
        $update_qty->bindParam(":add_qty", $add_qty);
        $update_qty->bindParam(":id", $item_id);
        $update_qty->execute();
    } else {
        $sub_qty = $item_qty - $qty_before ;

        $update_qty = $pdo->prepare("UPDATE products set stock=(stock-:sub_qty) where id=:id");
        $update_qty->bindParam(":sub_qty", $sub_qty);
        $update_qty->bindParam(":id", $item_id);
        $update_qty->execute();
    }

    $update = $pdo->prepare("UPDATE order_details set qty=:qty, total=:total where id=:id");
    $update->bindParam(":qty", $item_qty);
    $update->bindParam(":total", $total);
    $update->bindParam(":id", $order_did);

    if($update->execute()){
      echo json_encode(array("response" => "success"));
    } else{
        echo json_encode(array("response" => "error","message" => "Error Occurred While Saving"));
    }
}

if(isset($_POST['addToCart'])){
    $flag = true;

    $pid = $_POST['pid'];
    $qty = $_POST['qty'];
    $total = $_POST['total'];

     $get_orders = $pdo->prepare("SELECT * from orders where customer_id=:id and status='UNPAID'");
      $get_orders->bindParam(":id",$_SESSION['id']);
      $get_orders->execute();

  $order = $get_orders->fetch(PDO::FETCH_OBJ);

  if($get_orders->rowCount()){

    $get_product = $pdo->prepare("SELECT * from order_details inner join orders on order_details.order_id=orders.id where order_details.product_id=:pid and orders.customer_id=:id and orders.status='UNPAID'");
      $get_product->bindParam(":pid",$pid);
      $get_product->bindParam(":id",$_SESSION['id']);
      $get_product->execute();

      $detail = $get_product->fetch(PDO::FETCH_OBJ);

      if($get_product->rowCount()){

        $update_order = $pdo->prepare("UPDATE order_details set qty=(qty+:qty), total=(total+:total) where product_id=:pid and order_id=:order_id ");
        $update_order->bindParam(":qty",$qty);
        $update_order->bindParam(":total",$total);
        $update_order->bindParam(":pid",$pid);
        $update_order->bindParam(":order_id",$detail->order_id);

        if($update_order->execute()){

            $flag = true;
        } else{
            $flag = false;
        }


      } else {

        $insert_item = $pdo->prepare("INSERT INTO order_details ( order_id, product_id, qty, total ) VALUES( :order_id, :order_item, :order_qty, :order_total )");
        $insert_item->bindParam(":order_id", $order->id);
        $insert_item->bindParam(":order_item", $pid);
        $insert_item->bindParam(":order_qty", $qty);
        $insert_item->bindParam(":order_total", $total);
        $insert_item->execute();

        if($insert_item->rowCount()){
            $flag = true;
        } else{
            $flag = false;
        }

      }


  } else {
    $code = $alpha.'-'.$beta;
    
    $insert = $pdo->prepare("INSERT INTO orders ( code, customer_id ) VALUES( :order_code, :order_cus )");
    $insert->bindParam(":order_code", $code);
    $insert->bindParam(":order_cus", $_SESSION['id']);

    $insert->execute();

    $order_id= $pdo->lastInsertId();

    $insert_item = $pdo->prepare("INSERT INTO order_details ( order_id, product_id, qty, total ) VALUES( :order_id, :order_item, :order_qty, :order_total )");
    $insert_item->bindParam(":order_id", $order_id);
    $insert_item->bindParam(":order_item", $pid);
    $insert_item->bindParam(":order_qty", $qty);
    $insert_item->bindParam(":order_total", $total);
    $insert_item->execute();

    if($insert_item->rowCount()){
            $flag = true;
        } else{
            $flag = false;
        }
  }

  if ($flag) {
        $update_qty = $pdo->prepare("UPDATE products set stock=(stock-:qty) where id=:id");
        $update_qty->bindParam(":qty", $qty);
        $update_qty->bindParam(":id", $pid);
        
        if($update_qty->execute()){

            echo json_encode(array("response" => "success"));
        } else {
            echo json_encode(array("response" => "error","message" => "Update Error"));
        }


    } else{
        echo json_encode(array("response" => "error","message" => "Error Occurred While Saving"));
    }

}

?>