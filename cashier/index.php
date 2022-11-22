<?php require_once './header.php'; 
  include ('inc/analytics.php');
?>
<script type="text/javascript">
  $('#nav-dash').find('a').toggleClass('active');
</script>

<style type="text/css">
  #dash-sched-today-tbl_paginate{
    display: flex;
    padding-top: 0.75rem !important;
     padding-bottom: 0.75rem !important;
    justify-content: space-between;
    flex-wrap: wrap;
  }

   #dash-sched-today-tbl_paginate:before {
    content: "TODAY'S APPOINTMENTS";
    font-size: 1rem;
    font-weight: 400;
  }

  #dash-sched-today-tbl_paginate .page-link {
    padding: 0.2rem 0.5rem;
  }
</style>

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
      <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ORDERS</span>
              <span class="info-box-number"><?=$unpaid_orders?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-day"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">APPOINTMENTS</span>
              <span class="info-box-number"><?=$left_today?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-coins"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">TOTAL REVENUE</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format(($sales+$services),2)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">CUSTOMERS</span>
              <span class="info-box-number"><?=$customers?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row mt-3">
        <div class="col-md-8">
         <!-- TABLE: LATEST ORDERS -->
         <div class="card shadow">
          <div class="card-header border-transparent">
            <h3 class="card-title">RECENT ORDERS</h3>

            <div class="col text-right">
              <a href="orders.php" class="btn btn-xs btn-info elevation-1" style="border-radius: 5px;">See all</a>
            </div>
          </div>
          <!-- /.card-header -->
          <!-- /.card-header -->
              <div class="card-body pt-0">
                <table id="" class="datatable table table-bordered table-striped">
                  <thead>
                      <tr>
                        <th>ORDER CODE</th>
                        <th>CUSTOMER</th>
                        <th>AMOUNT</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $get_order = $pdo->prepare("SELECT orders.id,code, customer_id , customers.name, (select sum(total) from order_details where order_details.order_id=orders.id) as total, DATE_FORMAT(`date`, '%d - %M %Y %h:%i %p') as date, status FROM `orders` inner join customers on orders.customer_id=customers.id ORDER BY `date` DESC limit 5");
                      $get_order->execute();
                      while ($order = $get_order->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                        ?>

                        <tr>
                          <td><?=$order->code?></td>
                          <td><?=$order->name?></td>
                          <td><span><i class="fas fa-peso-sign"></i></span>&nbsp;&nbsp;<?=number_format($order->total,2)?></td>
                          <td><?=$order->date?></td>
                          <td>

                            <?php
                              if($order->status=='UNPAID'){
                                echo '<span class="badge badge-warning">'.$order->status.'</span>';
                              } else if($order->status=='CANCELLED'){
                                echo '<span class="badge badge-danger">'.$order->status.'</span>';
                              } else if($order->status=='PAID'){
                                echo '<span class="badge badge-success">'.$order->status.'</span>';
                              } else {
                                echo '<span class="badge badge-info">'.$order->status.'</span>';
                              }
                              
                            ?>

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
        <!-- /.card -->
      </div>
      <!-- /.col -->
      <div class="col-md-4">


            <!-- PRODUCT LIST -->
            <div class="card">
             <div class="card-header border-transparent">
            <h3 class="card-title">TOP SELLING PRODUCTS</h3>

          </div>
              <!-- /.card-header -->
              <div class="card-body pt-0 pb-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                  <?php
                      $get_products = $pdo->prepare("SELECT products.name, products.description,products.img, sum(order_details.qty) as count FROM `order_details` INNER join products on products.id=order_details.product_id GROUP by products.id order by count desc LIMIT 4;");
                      $get_products->execute();
                      while ($product = $get_products->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                        ?>

                  <li class="item">
                    <div class="product-img">
                      <img src="<?=$baseurl?>admin/uploads/img/products/<?=$product->img?>" alt="<?=$product->img?>" class="img-size-50">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title"><?=$product->name?>
                        <span class="badge badge-warning float-right"><?=$product->count?></span></a>
                      <span class="product-description"><?=$product->description?>
                      </span>
                    </div>
                  </li>
                  <!-- /.item -->

                        </tr>
                        <?php  
                      }
                      ?>

                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                <a href="products.php" class="uppercase">View All Products</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->

      </div>
    </div>
    <!-- /.row -->
    <div class="row mt-3">
      <div class="col-md-8">
        <!-- TABLE: LATEST ORDERS -->
        <div class="card shadow">
          <div class="card-header border-transparent">
            <h3 class="card-title">RECENT APPOINTMENTS</h3>

          </div>
          <!-- /.card-header -->
           <div class="card-body pt-0">
                <table id="" class="datatable table table-bordered table-striped">
                  <thead>
                          <tr>
                            <th>APPT CODE</th>
                            <th>CUSTOMER</th>
                            <th>TITLE</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $get_event = $pdo->prepare("SELECT events.id,events.status, events.appt_code, customers.name, events.title,  DATE_FORMAT(events.start, '%d - %M %Y %h:%i %p') as start from events inner join customers on events.customer_id=customers.id  ORDER BY `start` DESC LIMIT 5");
                         $get_event->execute();
                         while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr>
                            <td><?=$event->appt_code?></td> 
                            <td><?=$event->name?></td> 
                            <td><?=$event->title?></td> 
                            <td><?=$event->start?></td>
                            
                            <td>
                                 <?php
                              if($event->status=='PENDING'){
                                echo '<span class="badge badge-warning">'.$event->status.'</span>';
                              } else if($event->status=='CANCELLED'){
                                echo '<span class="badge badge-danger">'.$event->status.'</span>';
                              } else if($event->status=='DONE'){
                                echo '<span class="badge badge-success">'.$event->status.'</span>';
                              } else {
                                echo '<span class="badge badge-info">'.$event->status.'</span>';
                              }
                              
                            ?>

                            </td>  
                  

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
        <!-- /.card -->
      </div>
      <div class="col-md-4">
        
            <!-- TO DO List -->
            <div class="card shadow">

              <div class="card-body pt-0">
                      <table id="dash-sched-today-tbl" class="table m-0 table-striped">
                        <thead>
                          <tr>
                            <th>CUSTOMER</th>
                            <th>EVENT</th>
                            <th>TIME</th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $get_event = $pdo->prepare("SELECT events.id, events.appt_code, events.customer_id, customers.name, events.title, events.description, events.start, events.end from events inner join customers on events.customer_id=customers.id where status='PENDING' and start >= concat(curdate(),' ','00:00:00') and start <= concat(curdate(),' ','23:59:59') ");
                         $get_event->execute();
                         while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr>
                            <td><?=$event->name?></td>
                            <td><?=$event->title?></td>
                            <td><?=date("h:i a",strtotime(explode(" ", $event->start)[1]))?></td>
                            

                          </tr>
                          <?php  
                        }
                        ?>
                      </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
      </div>
    </div>
  </div><!--/. container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->


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

<script type="text/javascript">
  
  $(".datatable").DataTable({
    "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      order: [[3, 'desc']],
      "info": false,
      "autoWidth": false,
      "responsive": true
  });
 $("#dash-sched-today-tbl").DataTable({
      dom: 'pt',
       "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
       order: [[2, 'desc']],
      "info": false,
      "autoWidth": false,
      "responsive": true,
      "oLanguage": {
       "oPaginate": {
         "sPrevious": "«",
         "sNext": "»"
       }
     },
      "language": {
      "emptyTable": "No appointments left to attend today. Good job!"
    },
    "pageLength": 5
   });

</script>

<?php require_once './footer.php'; ?>