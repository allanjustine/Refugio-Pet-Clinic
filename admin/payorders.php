<?php 
require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');

   if(isset($_POST['payOrder'])) {


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
  $('#nav-payor').find('a').toggleClass('active');
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
              <span class="info-box-text">SALES REVENUE</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($sales,2)?></span>
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
              <span class="info-box-text">SERVICES REVENUE</span>
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
              <span class="info-box-text">TOTAL REVENUE</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format(($sales+$services),2)?></span>
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
        <div class="card">
              
          <div class="card-header">
            <h3 class="card-title">ORDERS HISTORY</h3>
          </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                      <tr>
                        <th>ORDER CODE</th>
                        <th>CUSTOMER</th>
                        <th>AMOUNT</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $get_order = $pdo->prepare("SELECT orders.id, orders.payment_method, orders.ref_num,code, customer_id , customers.name, (select sum(total) from order_details where order_details.order_id=orders.id) as total, DATE_FORMAT(`date`, '%d - %M %Y %h:%i %p') as date, status FROM `orders` inner join customers on orders.customer_id=customers.id ORDER BY `date` DESC");
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
                          <td><input type="hidden" id="order-cus-<?=$order->id?>" value="<?=$order->name?>"><?=$order->name?></td>
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
                          <td style="white-space:nowrap !important; width: 30px !important"><div style="display: flex;"><button onclick="payOrder(<?=$order->id?>)" class="btn btn-sm btn-warning mr-2 shadow" style="border-radius: 5px"><span><i class="fa fa-eye mr-2"></i></span>View</button><a href="orders-print.php#<?=$order->id?>" type="button" class="btn btn-sm btn-info shadow" ><span class="fa fa-print mr-2"></span>Print</a></div></td>
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
    </div>
  </div><!--/. container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->


 <div class="modal fade" id="payOrder">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Order Info&nbsp;&nbsp;</h3 > <span class="badge" id="status-badge"></span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body" id="order-card">
              <form method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="order_id" value="" id="order-id" class="form-control" readonly>
                <div class="form-row d-flex" style="width: 100%">
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <label style="display: block !important;">Customer</label>
                    <input style="display: block !important; width: 100%; background-color: transparent;" type="text" name="cus_name" class="form-control" id="cus-id" value="" readonly></select>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <div class="input-group" style="display: block;">
                      <label style="display: block !important;">Order Code</label>
                      <input style="display: block !important; width: 100%; background-color: transparent;" type="text" id="order-code" name="order_code"  class="form-control"  readonly>
                    </div>
                  </div>
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
                    <tbody id="edit-order-tbody" style="">
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
                        <select name="payment_method" id="payment-method" class="form-control" onchange="refChange(this.value)" required>
                          <option value="Cash">Cash</option>
                          <option value="GCash">GCash</option>
                          <option value="Card">Card</option>
                          <option value="Check">Check</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        <label style="display: block; width: 100%;" id="pament-no-lbl" >Reference #</label>
                        <input type="text" name="payment_no" oninput="this.value = this.value.toUpperCase()" onkeypress="return event.charCode != 32" class="form-control" placeholder="Enter Reference Number" value="N/A" id="payment-no" readonly required>
                      </div>
                    </div>
                    
                  </div>

                <div class="card-footer mt-3">
                  <div class="form-row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6" style="text-align: right;" id="order-btns">
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

    $('#example1').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  });


</script>
<script type="text/javascript">

  function refChange(str){
    if(str == 'Cash'){
      $("#payment-no").attr("readonly","readonly");
      $("#payment-no").val("N/A");
    } else{

      $("#payment-no").removeAttr("readonly");
      $("#payment-no").val("");
    }
  }
  
  function payOrder(id){

    $("#pay-btn").remove();
    $("#order-print-btn").remove();

    $('#order-id').val($('#order-id-'+id).val());
    $('#cus-id').val($('#order-cus-'+id).val());
    $('#order-code').val($('#order-code-'+id).val());

    if($('#order-status-'+id).val()=='PAID'){
      $("span#status-badge").html('PAID');
      $("#order-btns").prepend(`<a href="orders-print.php#${id}" type="button" id="order-print-btn" class="btn btn-info" ><span class="fa fa-print mr-2"></span>Print</a>`);
      $("span#status-badge").removeClass("badge-warning");
      $("span#status-badge").addClass("badge-success");
      $("#payment-method").attr("disabled","disabled");
      $("#payment-method").val($("#order-method-"+id).val());
      $("#payment-no").val($("#order-ref-"+id).val());
      $("#payment-no").attr("readonly","readonly");


    } else {
      $("span#status-badge").html('UNPAID');
       $("#order-btns").prepend(`<button type="submit" name="payOrder" value="" id="pay-btn" class="btn btn-warning"><span class="fa fa-hand-holding-dollar mr-2"></span>Pay</button>`);
      $("span#status-badge").removeClass("badge-success");
      $("span#status-badge").addClass("badge-warning");
      $("#payment-method").removeAttr("disabled");
      $("#payment-method").val("Cash");
      $("#payment-no").val("N/A");
    }

    $('#grandtotal').text('0.00');

    $('#edit-order-tbody').empty();

    $.ajax({
      url: 'ajax.php?order='+$('#order-id').val().trim(),
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
        $('#edit-order-tbody').append(field);
        $("#grandtotal").text(formatter.format(gtotal).replace(/^(\D+)/, '$1 '));

         $("#pre-loader").css("display","none");
      }

    });

      $('#payOrder').modal().show();
  }



</script>


<?php require_once './footer.php'; ?>