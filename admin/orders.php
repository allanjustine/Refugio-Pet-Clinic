<?php 
require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');

function fill_product()
{
    global $pdo;
    $output = '';
    $select_product = $pdo->prepare("SELECT id,code, name from products where status='INSTOCK' ");
    $select_product->execute();
    if ($select_product->rowCount()) {
        while ($row = $select_product->fetch(PDO::FETCH_OBJ)) {
            $output .= "<option value='{$row->id}'>{$row->name}</option>";
        }
    }
    return $output;
}

function fill_customers()
{
    global $pdo;
    $output = '';
    $select_customer = $pdo->prepare("SELECT id, name from customers where id not in (select customer_id from orders where status='UNPAID')");
    $select_customer->execute();
    if ($select_customer->rowCount()) {
        while ($row = $select_customer->fetch(PDO::FETCH_OBJ)) {
            $output .= "<option value='{$row->id}'>{$row->name}</option>";
        }
    }
    return $output;
}


if (isset($_POST['addOrder']) ) {

  if(isset($_POST['item_id'])){

    $order_cus  = $_POST['cus_name'];
    $order_code = $_POST['order_code'];

    $insert = $pdo->prepare("INSERT INTO orders ( code, customer_id ) VALUES( :order_code, :order_cus )");
    $insert->bindParam(":order_code", $order_code);
    $insert->bindParam(":order_cus", $order_cus);

    $insert->execute();

    $order_id= $pdo->lastInsertId();

    $order_item = $_POST['item_id'];
    $order_qty = $_POST['qty'];
    $order_total = $_POST['total'];

    $insert = $pdo->prepare("INSERT INTO order_details ( order_id, product_id, qty, total ) VALUES( :order_id, :order_item, :order_qty, :order_total )");
    $insert->bindParam(":order_id", $order_id);

    for($i=0;$i<count($order_item);$i++){

      $insert->bindParam(":order_item", $order_item[$i]);
      $insert->bindParam(":order_qty", $order_qty[$i]);
      $insert->bindParam(":order_total", $order_total[$i]);

      $insert->execute();

      $update = $pdo->prepare("UPDATE products set stock=(stock-:qty) where id=:item_id");
      $update->bindParam(":qty", $order_qty[$i]);
      $update->bindParam(":item_id",$order_item[$i]);

      $update->execute();

    }


    if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
      ?>



      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Order Recorded Successfully",
            text: "Order <?php echo $order_code ?>, Has Been Recorded",
            icon: "success",
            showCancelButton: false,
            showConfirmButton: false,
            buttons: false
          });
        });
      </script>
      <?php
    } else {
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Error",
            text: "Order Recording Failed",
            icon: "error",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
    }
  } else{

    {
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Error",
            text: "Please Add Items",
            icon: "error",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
    }

  }


  header("refresh:1,orders.php");

}

if (isset($_POST['editOrder'])) {

    $order_code = $_POST['order_code'];
    $order_id = $_POST['order_id'];

  if(isset($_POST['new_item_id']) || isset($_POST['order_did'])) {

    if(isset($_POST['order_did'])) {
      $order_did = $_POST['order_did'];

      $item_before = $_POST['item_before'];
      $qty_before = $_POST['qty_before'];
      $total_before = $_POST['total_before']; 

      $item_id = $_POST['item_id'];
      $item_qty = $_POST['qty'];
      $item_total = $_POST['total'];  

      for($i=0;$i<count($item_before);$i++) {

        if( $item_before[$i] != $item_id[$i] ) {
          $update = $pdo->prepare("UPDATE products set stock=(stock+:qty_before) where id=:id");
          $update->bindParam(":qty_before", $qty_before[$i]);
          $update->bindParam(":id", $item_before[$i]);
          $update->execute();

          $update_qty = $pdo->prepare("UPDATE products set stock=(stock-:qty) where id=:id");
          $update_qty->bindParam(":qty", $item_qty[$i]);
          $update_qty->bindParam(":id", $item_id[$i]);
          $update_qty->execute();

        } else {

          if( $qty_before[$i] > $item_qty[$i] ){
            $add_qty = $qty_before[$i] - $item_qty[$i];

            $update_qty = $pdo->prepare("UPDATE products set stock=(stock+:add_qty) where id=:id");
            $update_qty->bindParam(":add_qty", $add_qty);
            $update_qty->bindParam(":id", $item_before[$i]);
            $update_qty->execute();
          }

          if( $qty_before[$i] < $item_qty[$i] ) {
            $sub_qty = $item_qty[$i] - $qty_before[$i] ;

            $update_qty = $pdo->prepare("UPDATE products set stock=(stock-:sub_qty) where id=:id");
            $update_qty->bindParam(":sub_qty", $sub_qty);
            $update_qty->bindParam(":id", $item_before[$i]);
            $update_qty->execute();
          }

        }

        $update_order = $pdo->prepare("UPDATE order_details set product_id=:item_id, qty=:item_qty, total=:item_total where id=:order_did");
        $update_order->bindParam(":item_id",$item_id[$i]);
        $update_order->bindParam(":item_qty", $item_qty[$i]);
        $update_order->bindParam(":item_total", $item_total[$i]);
        $update_order->bindParam(":order_did", $order_did[$i]);
        $update_order->execute();

      }
    }

    if(isset($_POST['new_item_id'])) {

      $new_item_id = $_POST['new_item_id'];
      $new_item_qty = $_POST['new_item_qty'];
      $new_item_total = $_POST['new_item_total'];

      $insert = $pdo->prepare("INSERT INTO order_details ( order_id, product_id, qty, total ) VALUES( :order_id, :order_item, :order_qty, :order_total )");
      $insert->bindParam(":order_id", $order_id);

      for($i=0;$i<count($new_item_id);$i++){

        $insert->bindParam(":order_item", $new_item_id[$i]);
        $insert->bindParam(":order_qty", $new_item_qty[$i]);
        $insert->bindParam(":order_total", $new_item_total[$i]);
        $insert->execute();

        $update = $pdo->prepare("UPDATE products set stock=(stock-:qty) where id=:id");
        $update->bindParam(":qty", $new_item_qty[$i]);
        $update->bindParam(":id", $new_item_id[$i]);
        $update->execute();

      }
    }


    $update_info = $pdo->prepare("UPDATE orders set `date`=now() where id=:order_id");
    $update_info->bindParam(":order_id", $order_id);

    if ($update_info->execute()) {
            //echo "Application Submitted Successfully";
      ?>

      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Order Updated Successfully",
            text: "Order <?php echo $order_code ?>, Has Been Updated",
            icon: "success",
            showCancelButton: false,
            showConfirmButton: false,
            buttons: false
          });
        });
      </script>
      <?php
    } else {
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Error",
            text: "Order Update Failed",
            icon: "error",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
    }

  } else{


    $update_delete = $pdo->prepare("DELETE from orders where id=:id");
    $update_delete->bindParam(":id",$order_id);

    if($update_delete->execute()){
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Notice",
            text: "Order <?=$order_code?> Has Been Removed",
            icon: "info",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
    }

  }

   header("refresh:2,orders.php");
  
}

?>

<script type="text/javascript">
  $('#nav-ord').find('a').toggleClass('active');
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
      <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hourglass-half"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">PENDING</span>
              <span class="info-box-number"><?=$unpaid_orders?>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-to-slot"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">COMPLETED</span>
              <span class="info-box-number"><?=$paid_orders?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-peso-sign"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">SALES</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format(($sales),2)?></span>
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
                      $get_order = $pdo->prepare("SELECT orders.id,code, customer_id , customers.name, (select sum(total) from order_details where order_details.order_id=orders.id) as total, DATE_FORMAT(`date`, '%d - %M %Y %h:%i %p') as date, status FROM `orders` inner join customers on orders.customer_id=customers.id where orders.status='UNPAID' ORDER BY `date` DESC");
                      $get_order->execute();
                      while ($order = $get_order->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                        ?>

                        <tr>
                          <input type="hidden" id="order-id-<?=$order->id?>" value="<?=$order->id?>">
                          <input type="hidden" id="order-cus_id-<?=$order->id?>" value="<?=$order->name?>">
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
                          <td style="white-space:nowrap !important; width: 30px !important"><button onclick="showEditOrder(<?=$order->id?>)" class="btn btn-sm btn-info shadow" style="border-radius: 5px"><span><i class="fas fa-edit"></i></span>&nbsp;&nbsp;Update</button></td>
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

 <div class="modal fade" id="addOrder">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Order Info</h3 >
              <button type="button" class="close mr-2" data-dismiss="modal" aria-label="Close">
                <span class="fas fa-xmark float-right" aria-hidden="true"></span>
              </button>
            </div>
            <div class="card-body" id="order-card">
              <form method="POST" enctype="multipart/form-data">

                <div class="form-row d-flex" style="width: 100%">
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <label style="display: block !important;">Customer</label>
                    <select style="width: 100%" name="cus_name" class="form-control select2" value="" required><?=fill_customers()?></select>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <div class="input-group" style="display: block;">
                      <div class="" style="display: block;">
                        <label style="width: 80%;">Order Code</label>
                        <span class="float-right" style=" width: 20%;text-align: right;"><a href="#" role="button" id="clear-order"><small>Clear All</small></a></span>
                      </div>
                      <input style="display: block !important; width: 100%" type="text" name="order_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="" required>
                    </div>
                  </div>
                </div>
               
                  <div class="form-row mt-3" style="overflow-x: auto;" >

                    <table id="order-items-tbl" class=" table table-bordered table-striped" >
                       <thead >
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                        <th style="width: 10px">
                            <center><button type="button" name="add" class="btn btn-success btnAdd btn-sm"> <span class="fas fa-plus"></span> </button></center>
                        </th>

                    </thead>
                    <tbody id="order-items-tbody" style="">
                    </tbody>
                    <tfoot >
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                        <th style="width: 10px">
                            <center><button type="button" id="addField" name="add" class="btn btn-success btnAdd btn-sm"> <span class="fas fa-plus"></span> </button></center>
                        </th>
                    </tfoot>
                </table>
                  </div>

                <div class="card-footer mt-3">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label >GRAND TOTAL:&nbsp;<span id="grandtotal">0.00</span></label>
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                      <input type="submit" name="addOrder" value="Place Order" class="btn btn-warning" value=""> 
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

 <div class="modal fade" id="editOrder">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Order Info</h3 >
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body" id="order-card">
              <form method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="order_id" value="" id="order-id" class="form-control" required>
                <div class="form-row d-flex" style="width: 100%">
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <label style="display: block !important;">Customer</label>
                    <input style="width: 100%; background-color: white;" type="text" name="cus_name" class="form-control" id="cus-id" value="" required readonly>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <div class="input-group" style="display: block;">
                      <label style="display: block !important;">Order Code</label>
                      <input style="display: block !important; width: 100%; background-color: white;" type="text" id="order-code" name="order_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="" required readonly>
                    </div>
                  </div>
                </div>
                <br>

                  <div class="form-row" >
                    <table id="order-items-tbl" class=" table table-responsive table-bordered table-striped" >
                       <thead >
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                        <th style="width: 10px">
                            <center><button type="button" name="add" class="btn btn-success editBtnAdd btn-sm"> <span class="fas fa-plus"></span> </button></center>
                        </th>

                    </thead>
                    <tbody id="edit-order-tbody" style="">
                    </tbody>
                    <tfoot >
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                        <th style="width: 10px">
                            <center><button type="button" id="addField" name="add" class="btn btn-success editBtnAdd btn-sm"> <span class="fas fa-plus"></span> </button></center>
                        </th>
                    </tfoot>
                </table>
                  </div>

                <div class="card-footer mt-3">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label >GRAND TOTAL:&nbsp;<span id="grandtotal-edit">0.00</span></label>
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                      <input type="submit" name="editOrder" value="Save" class="btn btn-info" value=""> 
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
      "buttons": [
            {
                text: '&nbsp;&nbsp;Create Order',
                className: 'orderBtn', 
                action: function ( e, dt, node, config ) {
                      $('#addOrder').modal().show();
                }
            }
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    var icon = $('<i>',{class: 'fas fa-plus'});
    $('.orderBtn').addClass('btn btn-sm btn-outline-success bg-transparent').prepend(icon).css('border-radius','5px').hover(function(){
      $(this).toggleClass("bg-transparent bg-success");
    });


  });


</script>
<script type="text/javascript">
  
  function showEditOrder(id){


    var counter = 0;

    $('#order-id').val($('#order-id-'+id).val());
    $('#cus-id').val($('#order-cus_id-'+id).val());
    $('#order-code').val($('#order-code-'+id).val());
    $('#grandtotal-edit').text('0.00');

    $('#edit-order-tbody').empty();
    $.ajax({
      url: 'ajax.php?order='+$('#order-id').val().trim(),
      type: 'GET',
              beforeSend: function(){
                $("#pre-loader").css("display","flex");
              },
      success: function(response) { 
        const data = JSON.parse(response);
        var field = '';
        data.forEach(order => {    
          counter++;

          field = `
            <tr id="">
              <input type="hidden" class="" name="order_did[]" value="${order.order_did}">
              <input type="hidden" class="" name="item_before[]" value="${order.product_id}">
              <input type="hidden" class="" name="total_before[]" value="${order.total}">
              <td class='td_item'><select style="width: 300px" name="item_id[]" class="form-control select2 select-item-id no-${counter}" value="" required><?=fill_product()?></select>
              </td>
              <td class="td_qty" ><input style="width: 60px" onkeydown="return (event.keyCode!=13);" value="${order.qty}" max="${order.qty+order.stock}" type="number" min="1" step="1" class="form-control qty" placeholder="Qty" name="qty[]" required>
              </td>
              <td class="td_price"><input style="width: 60px" onkeydown="return (event.keyCode!=13);"type="text" min="1" value="${order.price}" name="price[]" class="form-control price" placeholder="Price" readonly required>
              </td>
              <td class="td_total"><input style="width: 60px" onkeydown="return (event.keyCode!=13);"  type="text" min="1" name="total[]" placeholder="Total Amount" class="form-control total total-edit" value="${order.total}" required readonly></td>
              <td style="width: 10px">
                <center>
                  <input type="hidden" class="pid" value="${order.product_id}">
                  <input type="hidden" name="qty_before[]" class="order-qty" value="${order.qty}">
                  <button target-id="${order.order_did}" type="button" title="Void" class=" btn btn-warning itemDelete " name="remove"" >
                    <span class="fas fa-ban"></span>
                  </button>
                </center>
              </td>

            </tr

        `;
          $('#edit-order-tbody').append(field);
          $('.select2').select2();
          $('#edit-order-tbody .no-'+counter).val(order.product_id).trigger('change'); 

        });

        $('.itemDelete').on('click', function(){
          //$(this).preventDefault();
            var tr =$(this).closest('tr');
            var details_id = $(this).attr("target-id");
            var pid = $(this).parent().find('.pid').val();
            var qty = $(this).parent().find('.order-qty').val();;

            $.ajax({
              url: 'ajax.php?delete='+details_id+'&pid='+pid+'&qty='+qty,
              type: 'GET',
              beforeSend: function(){
                $("#pre-loader").css("display","flex");
              },
              success: function(response) {
                const data = JSON.parse(response);
                if(data.response=='ok'){

                  toastr.success('Item has been removed.');
                  tr.remove();
                } else{
                  toastr.error('Error deleting item.');
                }
                $("#pre-loader").css("display","none");
              },
              error: function() {
                toastr.error('Item not found!');
              }
           
           });

          });

          calculateEdit();
          compute();
          $("#pre-loader").css("display","none");

      },
      error: function() {
       alert("File Not Found");
     }
   
   });

      $('#editOrder').modal().show();
      $('#editOrder').on('hidden.bs.modal',function(){
        if($("#edit-order-tbody .fa-ban").length < counter ){
          if($("#edit-order-tbody .fa-ban").length == 0 ) {
            $.ajax({
              url: 'ajax.php?cancel='+$('#order-id').val().trim(),
              type: 'GET',
              beforeSend: function(){
                $("#pre-loader").css("display","flex");
              },
              success: function(res){
                const d = JSON.parse(res);
                if(d.response=="ok"){
                  swal({
                      title: "Notice",
                      text: "Order "+$('#order-code').val()+" Has Been Removed",
                      icon: "info",
                      showConfirmButton: false,
                      showCancelButton: false,
                      buttons: false,
                      timer: 2000
                    }).then(() => {
                      location.reload();
                    });
                }
                $("#pre-loader").css("display","none"); 
              },
              error: function(){
                toastr.error('Problem occurred.')
              }
            });
          } 

        }
      })
  }



</script>

<script>

  $('.btnAdd').on("click", function() {

    var field = `
    <tr>
    <td class='td_item'><select style="width: 300px" name="item_id[]" class="form-control select2 select-item-id" value="" required><option selected value>Select Item</option><?=fill_product()?></select>
    </td>
    <td class="td_qty" style="width: 100px"><input onkeydown="return (event.keyCode!=13);" value="1" type="number" min="1" step="1" class="form-control qty" placeholder="Qty" name="qty[]" required>
    </td>
    <td class="td_price"><input onkeydown="return (event.keyCode!=13);"type="number" min="1" value="0" name="price[]" class="form-control price" placeholder="Price" readonly required>
    </td>
    <td class="td_total"><input onkeydown="return (event.keyCode!=13);"  type="number" min="1" name="total[]" placeholder="Total Amount" class="form-control total" value="0" required readonly></td>
    <td style="width: 10px"><center><button type="button" class=" btn btn-danger btnRemove " name="remove"" ><span class="fas fa-times"></span></button></center></td>

    </tr

    `;

    $("#order-items-tbody").append(field);

    $('.select2').select2();
    compute();

  });
       

  $('.editBtnAdd').on("click", function() {

    var field = `
    <tr>
    <td class='td_item'><select style="width: 300px" name="new_item_id[]" class="form-control select2 select-item-id" value="" required><option selected value>Select Item</option><?=fill_product()?></select>
    </td>
    <td class="td_qty"><input style="width: 60px" onkeydown="return (event.keyCode!=13);" value="0" type="number" min="1" step="1" class="form-control qty" placeholder="Qty" name="new_item_qty[]" required>
    </td>
    <td class="td_price"><input onkeydown="return (event.keyCode!=13);"type="number" min="1" value="0" name="new_price[]" class="form-control price" placeholder="Price" readonly required>
    </td>
    <td class="td_total"><input onkeydown="return (event.keyCode!=13);"  type="number" min="1" name="new_item_total[]" placeholder="Total Amount" class="form-control total total-edit" value="0" required readonly></td>
    <td style="width: 10px"><center><button type="button" class=" btn btn-danger btnRemove " name="remove"" ><span class="fas fa-times"></span></button></center></td>

    </tr

    `;

    $("#edit-order-tbody").append(field);
    $('.select2').select2();

    compute();


  });

$(document).on("click", ".btnRemove", function() {

  $(this).closest("tr").remove();
});


 function calculate(){
    var gt = 0;
    $('.total').each(function(){
      gt+=parseFloat($(this).val());
    })

    $('#grandtotal').text(formatter.format(gt));
}

function calculateEdit(){
    
    var gte = 0;
    $('.total-edit').each(function(){
      gte+=parseFloat($(this).val());
    })

    $('#grandtotal-edit').text(formatter.format(gte));
}


$('#clear-order').on('click', function(){

  $('#order-items-tbody').empty();
  $('#grandtotal').text(formatter.format(0));
})

function compute(){
  $(".select-item-id").on("change", function(e) {

    var tr = $(this).closest("tr");
    var item = $(this).val().trim();

    if(item.length>0) { 
     $.ajax({
      url: 'ajax.php?item='+item,
      type: 'GET',
      beforeSend: function(){
        $("#pre-loader").css("display","flex");
      },
      success: function(response) { 
        const data = JSON.parse(response);
        data.forEach(product => {
          tr.find('.td_price').find(".price").val(product.price).trigger('change');
          tr.find('.td_qty').find(".qty").attr("max",product.stock);
          calculateEdit();
          calculate();
        });
        $("#pre-loader").css("display","none");
      },
      error: function() {
       alert("File Not Found");
     }

   });
   } else {
    tr.find('.td_price').find(".price").val(0).trigger('change');
    calculateEdit();
    calculate();
  }

});

  $(document).on("change", ".qty,.price", function(e) {
    var tr = $(this).closest("tr");
    var qty = tr.find(".qty").val();
    var sellingprice = tr.find(".price").val();
    var totalPrice = parseFloat(qty) * parseFloat(sellingprice);
    tr.find(".total").val(totalPrice);

    calculateEdit();
    calculate();
  }); 
}

</script>


<?php require_once './footer.php'; ?>