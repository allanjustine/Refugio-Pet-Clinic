<?php

$query = "SELECT COUNT(*) as count FROM `products` where category='Pet Food' and status='INSTOCK' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$pet_food = $result['count'];


$query = "SELECT COUNT(*) as count FROM `products` where category='Medicine' and status='INSTOCK' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch(); 
$medicine = $result['count'];


$query = "SELECT COUNT(*) as count FROM `products` where category='Vitamins' and status='INSTOCK' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$vitamins = $result['count'];


$query = "SELECT COUNT(*) as count FROM `products` where category not in ('Vitamins','Medicine','Pet Food') and status='INSTOCK' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$other_cat = $result['count'];

$query = "SELECT COUNT(*) as count FROM `products` where status!='INSTOCK' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$issue = $result['count'];


$query = "SELECT COUNT(*) as count FROM `pets` ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$pets = $result['count'];

$query = "SELECT COUNT(*) as count FROM `orders` inner join order_details on orders.id=order_details.order_id where orders.status='UNPAID' and orders.id in (select order_id from order_details) ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$unpaid_orders = $result['count'];

$query = "SELECT COUNT(*) as count FROM `orders` where status='PAID';";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$paid_orders = $result['count'];

$query = "SELECT COUNT(*) as count FROM `events` where status='FOR APPROVAL' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$approval = $result['count'];


$query = "SELECT COUNT(*) as count FROM `users` where role='admin' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$admins = $result['count'];


$query = "SELECT COUNT(*) as count FROM `users` where role='cashier' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$cashiers = $result['count'];


$query = "SELECT COUNT(*) as count FROM `users` where role='customer' and status ='ACTIVE' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$verified = $result['count'];


$query = "SELECT COUNT(*) as count FROM `events` where customer_id!=0  and status='DONE' and start >= concat(curdate(),' ','00:00:00') and start <= concat(curdate(),' ','23:59:59')";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$attended_today = $result['count'];

$query = "SELECT COUNT(*) as count FROM `events` where customer_id!=0  and status='PENDING' and start >= concat(curdate(),' ','00:00:00') and start <= concat(curdate(),' ','23:59:59')";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$left_today = $result['count'];

$query = "SELECT COUNT(*) as count FROM `events` where customer_id!=0  and status='FOR APPROVAL' ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$for_approval = $result['count'];

$query = "SELECT count(*) as count FROM `users` where role='customer' and status='PENDING CONFIRMATION';";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$unconfirmed = $result['count'];

//AND MONTH(orders.date_paid) = MONTH(CURRENT_DATE())
$query = "SELECT sum(order_details.total) as sum FROM order_details inner join orders on order_details.order_id=orders.id WHERE orders.status='PAID' and YEAR(orders.date_paid) = YEAR(CURRENT_DATE()) ;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$sales = $result['sum'];
if($sales==null) $sales=0;

//AND MONTH(date_paid) = MONTH(CURRENT_DATE())
$query = "SELECT sum(bill) as sum FROM services WHERE status='PAID' and YEAR(date_paid) = YEAR(CURRENT_DATE()) ;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();
$services = $result['sum'];
if($services==null) $services=0;

?>