<?php 
require_once './header.php'; 

$query = "SELECT SUM(order_details.total) as total FROM `order_details` INNER join orders on order_details.order_id=orders.id where orders.status='PAID' and orders.customer_id=:id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$result = $stmt->fetch();
$spent = $result['total'];
if($spent==null) $spent=0;

$query = "SELECT SUM(services.bill) as total FROM `services` INNER join events on services.event_id=events.id where services.status='PAID' and events.customer_id=:id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$result = $stmt->fetch();
$services = $result['total'];
if($services==null) $services=0;

if(isset($_POST['viewOrder'])) {


  $id = $_POST['order_id'];
  $order_code = $_POST['order_code'];
  $payment_no = $_POST['payment_no'];
  $payment_method = $_POST['payment_method'];


  $update = $pdo->prepare("UPDATE orders set status='PAID', date_paid=now(), ref_num=:payment_no, payment_method=:payment_method where id=:id ");
  $update->bindParam(":payment_no",$payment_no);
  $update->bindParam(":payment_method",$payment_method);
  $update->bindParam(":id",$id);

  if($update->execute()){

        ?>
 
      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Order Paid Successfully",
            text: "Order <?php echo $order_code ?>, Has Been Paid",
            icon: "success",
            showCancelButton: false,
            showConfirmButton: false,
            buttons: false
          });
        });
      </script>
      <?php

     header('refresh:2;orders-print.php#'.$id);
    } else {
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Error",
            text: "Order Payment Failed",
            icon: "error",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
     header('refresh:2;payorders.php');
    }


}

?>

<script type="text/javascript">
  $('#nav-pay').find('a').toggleClass('active');
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
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cart-arrow-down"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ORDERS EXPENSES</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($spent,2)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-hand-holding-medical"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">CLINICAL SERVICES EXPENSES</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($services,2)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-coins"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">TOTAL SPENT</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($services+$spent,2)?></span>
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

            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="customersTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="" data-toggle="pill" href="#ordersTab" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">ORDERS</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="" data-toggle="pill" href="#servicesTab" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">CLINICAL SERVICES</a>
                </li>
              </ul>
            </div>
              <!-- /.card-header -->
            <div class="card-body">

              <div class="tab-content" id="customersTabContent">
                <div class="tab-pane fade show active" id="ordersTab" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                   <table id="ordersTbl" class="table datatable table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>ORDER CODE</th>
                          <th>AMOUNT</th>
                          <th>DATE</th>
                          <th>STATUS</th>
                          <th>ACTIONS</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $get_order = $pdo->prepare("SELECT orders.id, orders.payment_method, orders.ref_num,code, customer_id , customers.name, (select sum(total) from order_details where order_details.order_id=orders.id) as total, DATE_FORMAT(`date`, '%d - %M %Y %h:%i %p') as date, status FROM `orders` inner join customers on orders.customer_id=customers.id where orders.customer_id=:id and status='PAID' ORDER BY `date` DESC");
                        $get_order->bindParam(":id", $_SESSION['id']);
                        $get_order->execute();
                        while ($order = $get_order->fetch(PDO::FETCH_OBJ)) {
                                      // var_dump($product);
                          ?>

                          <tr>
                            <input type="hidden" id="order-id-<?=$order->id?>" value="<?=$order->id?>">
                            <input type="hidden" id="order-cus_id-<?=$order->id?>" value="<?=$order->customer_id?>">
                            <input type="hidden" id="order-method-<?=$order->id?>" value="<?=$order->payment_method?>">
                            <input type="hidden" id="order-ref-<?=$order->id?>" value="<?=$order->ref_num?>">
                            <td><input type="hidden" id="order-code-<?=$order->id?>" value="<?=$order->code?>"><?=$order->code?></td>
                            <td><input type="hidden" id="order-total-<?=$order->id?>" value="<?=$order->total?>"><span><i class="fas fa-peso-sign"></i></span>&nbsp;&nbsp;<?=number_format($order->total,2)?></td>
                            <td><input type="hidden" id="order-date-<?=$order->id?>" value="<?=$order->date?>"><?=$order->date?></td>
                            <td><input type="hidden" id="order-status-<?=$order->id?>" value="<?=$order->status?>">

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

                            </td>
                            <td style="white-space:nowrap !important; width: 30px !important"><div style="display: flex;"><button onclick="viewOrder(<?=$order->id?>)" class="btn btn-sm btn-warning mr-2 shadow" style="border-radius: 5px"><span><i class="fa fa-eye mr-2"></i></span>View</button><a href="orders-print.php#<?=$order->id?>" type="button" class="btn btn-sm btn-info shadow" ><span class="fa fa-print mr-2"></span>Print</a></div></td>
                          </tr>
                          <?php  
                        }
                        ?>
                      </tbody>
                      <tfoot>

                      </tfoot>
                  </table>

                </div>
                <div class="tab-pane fade " id="servicesTab" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                  <table id="apptTbl" class="table datatable table-bordered table-striped">
                      <thead> 
                          <tr>
                            <th>APPT CODE</th>
                            <th>TITLE</th>
                            <th>DATE</th>
                            <th>BILL</th>
                            <th>STATUS</th>
                            <th style="width: 10px !important">ACTIONS</th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $get_event = $pdo->prepare("SELECT services.id as service_id,services.ref_num, services.payment_method,services.status,services.bill, events.appt_code, events.customer_id, customers.name, events.title, events.description, DATE_FORMAT(events.start, '%d - %M %Y %h:%i %p') as start from services inner join events on services.event_id=events.id inner join customers on events.customer_id=customers.id where customers.id=:id and services.status='PAID'  ORDER BY `start` ASC");
                         $get_event->bindParam(":id",$_SESSION['id']);
                         $get_event->execute();
                         while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr>
                            <input type="hidden" id="service-status-<?=$event->service_id?>" value="<?=$event->status?>">
                            <input type="hidden" id="service-description-<?=$event->service_id?>" value="<?=$event->description?>">
                            <input type="hidden" id="service-id-<?=$event->service_id?>" value="<?=$event->service_id?>">
                            <input type="hidden" id="service-paym-<?=$event->service_id?>" value="<?=$event->payment_method?>">
                            <input type="hidden" id="service-ref-<?=$event->service_id?>" value="<?=$event->ref_num?>">
                            <td><input type="hidden" id="service-code-<?=$event->service_id?>" value="<?=$event->appt_code?>"><?=$event->appt_code?></td> 
                            <td><input type="hidden" id="service-title-<?=$event->service_id?>" value="<?=$event->title?>"><?=$event->title?></td> 
                            <td><input type="hidden" id="service-start-<?=$event->service_id?>" value="<?=$event->start?>"><?=$event->start?></td>
                            <td><input type="hidden" id="service-bill-<?=$event->service_id?>" value="<?=$event->bill?>"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($event->bill,2)?></td>
                            
                            <td>
                                 <?php
                              if($event->status=='UNPAID'){
                                echo '<span class="badge badge-warning">'.$event->status.'</span>';
                              } else if($event->status=='CANCELLED'){
                                echo '<span class="badge badge-danger">'.$event->status.'</span>';
                              } else if($event->status=='PAID'){
                                echo '<span class="badge badge-success">'.$event->status.'</span>';
                              } else {
                                echo '<span class="badge badge-info">'.$event->status.'</span>';
                              }
                              
                            ?>

                            </td>  

                            <td style="white-space:nowrap !important; width: 30px !important"><div style="display: flex;"><button onclick="payService(<?=$event->service_id?>)" class="btn btn-sm btn-warning mr-2 shadow" style="border-radius: 5px"><span><i class="fa fa-eye mr-2"></i></span>View</button><a href="services-print.php#<?=$event->service_id?>" type="button" class="btn btn-sm btn-info shadow" ><span class="fa fa-print mr-2"></span>Print</a></div></td>                      

                          </tr>
                          <?php  
                        }
                        ?>
                      </tbody>
                    <tfoot>

                    </tfoot>
                </table>
                </div>
              </div>
            </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
  </div><!--/. container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->


 <div class="modal fade" id="viewOrder">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Order Info&nbsp;&nbsp;</h3 > <span class="badge badge-success" id="status-badge">PAID</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body" id="order-card">
                <div class="row d-flex" style="width: 100%">
                    <label style="display: block !important;">Order Code</label>
                    <input style="display: block !important; width: 100%; background-color: transparent;" type="text" id="order-code" name="order_code"  class="form-control"  readonly>
                </div>
                <br>

                  <div class="form-row" >
                    <table id="order-items-tbl" class=" table table-bordered table-striped" >
                       <thead >
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                    </thead>
                    <tbody id="orders-tbody" style="">
                    </tbody>
                    <tfoot >
                        <th colspan="4" style="text-align: right;"><label class="mr-5">GRAND TOTAL:</label><span id="grandtotal">0.00</span></th>
                    </tfoot>
                </table>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" >Payment Method</label>
                        <input type="text" style="background-color: white;" class="form-control" id="payment-method" readonly>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" id="pament-no-lbl" >Reference #</label>
                        <input type="text" name="payment_no" class="form-control" style="background-color: white;"id="payment-no" readonly>
                      </div>
                    </div>
                    
                  </div>

                <div class="card-footer mt-3">
                  <div class="form-row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6" style="text-align: right;" id="order-btns">
                      <a href="" type="button" id="order-print-btn" class="btn btn-info" ><span class="fa fa-print mr-2"></span>Print</a>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>

                  </div>
                </div>
            </div>
          </div>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->


 <div class="modal fade" id="payService">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Clinical Service Info&nbsp;&nbsp;</h3 > <span class="badge badge-success" id="status-badge">PAID</span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body" id="order-card">
              <form method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="service_id" value="" id="service-id" class="form-control" readonly>
                <div class="form-row d-flex" style="width: 100%">
                  <div class="col-md-12 col-sm-12 col-xs-12" style="width: 100%">
                    <div class="input-group" style="display: block;">
                      <label style="display: block !important;">Appt. Code</label>
                      <input style="display: block !important; width: 100%; background-color: transparent;" type="text" id="service-code" name="service_code"  class="form-control"  readonly>
                    </div>
                  </div>
                </div>
                <br>

                  <div class="form-row" >
                    <div class="col-md-12">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" >Title</label>
                        <input style="display: block !important; width: 100%; background-color: transparent;" type="text" id="service-title" name=""  class="form-control"  readonly>
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="form-row" >
                    <div class="col-md-12">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" >Description</label>
                        <textarea style="display: block !important; width: 100%; background-color: transparent;" rows="3" id="service-desc" name=""  class="form-control"  readonly></textarea>
                      </div>
                    </div>
                  </div><br>

                  <div class="form-row" >
                    <div class="col-md-12">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" >Total Bill</label>
                        <input style="display: block !important; width: 100%; background-color: transparent;" type="text" id="service-bill" name=""  class="form-control"  readonly>
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" >Payment Method</label>
                        <input type="text" style="background-color: white;" class="form-control" id="service-paym" readonly>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" id="pament-no-lbl" >Reference #</label>
                        <input type="text" name="payment_nos" class="form-control" style="background-color: white;"id="service-ref" readonly>
                      </div>
                    </div>
                    
                  </div>

                <div class="card-footer mt-3">
                  <div class="form-row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6" style="text-align: right;" id="service-btns">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>

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

    $('#ordersTbl').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#ordersTbl_wrapper .col-md-6:eq(0)');

    $('#apptTbl').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#apptTbl_wrapper .col-md-6:eq(0)');

  });


</script>
<script type="text/javascript">

  
  function viewOrder(id){

    $('#order-code').val($('#order-code-'+id).val());

    $("#payment-method").val($("#order-method-"+id).val());
    $("#payment-no").val($("#order-ref-"+id).val());
    $("#order-print-btn").attr("href",'orders-print.php#'+id)

    $('#grandtotal').text('0.00');

    $('#orders-tbody').empty();

    $.ajax({
      url: 'ajax.php?order='+id,
      type: 'GET',
      beforeSend: function (){
         $("#pre-loader").css("display","flex");
      },
      success: function(response) { 
        const data = JSON.parse(response);
        var field = '';
        var gtotal = 0;
        data.forEach(order => {    
          
          field += `
            <tr id="">
              <td class='td_item'>${order.name}</td>
              <td class="td_qty" >${order.qty} </td>
              <td class="td_price">${formatter.format(order.price).replace(/^(\D+)/, '$1 ')}</td>
              <td class="td_total"><b>${formatter.format(order.total).replace(/^(\D+)/, '$1 ')}</b></td>
            </tr

        `;

        gtotal+= parseFloat(order.total);
         
        });
        $('#orders-tbody').append(field);
        $("#grandtotal").text(formatter.format(gtotal).replace(/^(\D+)/, '$1 '));

         $("#pre-loader").css("display","none");
      }

    });

      $('#viewOrder').modal().show();
  }

   function payService(id){

    $("#pay-btn").remove();
    $("#service-print-btn").remove();

    $('#service-id').val($('#service-id-'+id).val());
    $('#cus-id').val($('#service-name-'+id).val());
    $('#service-code').val($('#service-code-'+id).val());
    $('#service-bill').val($('#service-bill-'+id).val());
    $('#service-title').val($('#service-title-'+id).val());
    $('#service-desc').val($('#service-description-'+id).val());
    $('#service-paym').val($('#service-paym-'+id).val());
    $('#service-ref').val($('#service-ref-'+id).val());
    $("#service-btns").prepend(`<a href="services-print.php#${id}" type="button" id="service-print-btn" class="btn btn-info" ><span class="fa fa-print mr-2"></span>Print</a>`);
    $("span#status-badge").html('PAID');

      $('#payService').modal().show();
  }




</script>


<?php require_once './footer.php'; ?>