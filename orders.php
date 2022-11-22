<?php 
require_once './header.php'; 
include('inc/code-generator.php');


$query = "SELECT SUM(order_details.total) as total FROM `order_details` INNER join orders on order_details.order_id=orders.id where status='UNPAID' and orders.customer_id=:id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$result = $stmt->fetch();
$unpaid_orders = $result['total'];
if($unpaid_orders==null) $unpaid_orders=0;

$query = "SELECT SUM(order_details.total) as total FROM `order_details` INNER join orders on order_details.order_id=orders.id where status='PAID' and orders.customer_id=:id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$result = $stmt->fetch();
$spent = $result['total'];
if($spent==null) $spent=0;

$query = "SELECT COUNT(*) as count FROM `orders` where status='PAID' and customer_id=:id ";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$result = $stmt->fetch();
$paid_orders = $result['count'];


?>

<script type="text/javascript">
  $('#nav-ord').find('a').toggleClass('active');
</script>

<style type="text/css">
  #current_order_paginate{
    display: flex;
    padding-top: 0.75rem !important;
     padding-bottom: 0.75rem !important;
    justify-content: space-between;
    flex-wrap: wrap;
  }

   #current_order_paginate:before {
    content: "CART";
    font-size: 1rem;
    font-weight: 400;
  }

  #current_order_paginate .page-link {
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
        <div class="col-12 col-sm-6 col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hourglass-half"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">PENDING ORDER AMOUNT</span>
              <span class="info-box-number" id="header-gtot"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($unpaid_orders,2)?></span>
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
              <span class="info-box-text">COMPLETED ORDERS</span>
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
              <span class="info-box-text">TOTAL SPENT</span>
              <span class="info-box-number"><span class="mr-2"><i class="fa fa-peso-sign"></i></span><?=number_format($spent,2)?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        
      </div>
      <!-- /.row -->
    <div class="row mt-3">
      <div class="col-md-7">
       
        <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body">
                <table id="order-items-tbl" class="table table-bordered table-striped">
                  <thead>
                      <tr>
                        <th>ORDER CODE</th>
                        <th>AMOUNT</th>
                        <th>DATE</th>
                        <th>STATUS</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $get_order = $pdo->prepare("SELECT orders.id,code, customer_id , customers.name, (select sum(total) from order_details where order_details.order_id=orders.id) as total, DATE_FORMAT(`date`, '%d - %M %Y %h:%i %p') as date, status FROM `orders` inner join customers on orders.customer_id=customers.id where customers.id=:id and status='PAID' ORDER BY `date` DESC");
                      $get_order->bindParam(":id",$_SESSION['id']);
                      $get_order->execute();
                      while ($order = $get_order->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                        ?>

                        <tr>
                          <td><?=$order->code?></td>
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
      <!-- /.col -->

      <div class="col-md-5">
        
            <!-- TO DO List -->
            <div class="card shadow">

              <div class="card-body pt-0">
                      <table id="current_order" class="table m-0 table-striped">
                        <thead>
                          <tr>
                            <th>ITEM</th>
                            <th>QTY</th>
                            <th>PRICE</th>
                            <th>TOTAL</th>
                            <th><center><span class=""><i class="fa fa-trash"></i></span></center></th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $gtot=0;
                         $get_item = $pdo->prepare("SELECT order_details.id as order_did, product_id, products.name, qty,products.stock, products.price, ( qty * products.price) as total from order_details inner join orders on order_details.order_id=orders.id inner join products on order_details.product_id=products.id where orders.customer_id=:id and orders.status='UNPAID'");
                         $get_item->bindParam(":id",$_SESSION['id']);
                         $get_item->execute();
                         while ($item = $get_item->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr id="order-tr-<?=$item->order_did?>">
                            <input type="hidden" class="pid" value="<?=$item->product_id?>" name="">
                            <input type="hidden" class="qty" value="<?=$item->qty?>" name="">
                            <td><?=$item->name?></td>
                            <td><input type="number" min="1" max="<?=$item->qty+$item->stock?>" pid="<?=$item->product_id?>" qty-before="<?=$item->qty?>" target-id="<?=$item->order_did?>" class="item-qty" style="border: none; text-align: center; border-radius: 10px;" value="<?=$item->qty?>"></td>
                            <td id="price-<?=$item->order_did?>"><?=$item->price?></td>
                            <td class="total" id="total-<?=$item->order_did?>"><?=$item->total?></td>
                            <td><center><button target-id="<?=$item->order_did?>" type="button" title="Cancel" class=" btn btn-danger itemDelete " name="remove" >
                                <span class="fas fa-ban"></span>
                              </button></center>
                            </td>
                          </tr>
                          <?php  
                          $gtot+=$item->total;
                        }
                        ?>
                      </tbody>
                    </table>
                  <div class="card-footer mt-3">
                    <div class="form-row">
                      <div class="col-md-6">
                        <label >GRAND TOTAL:&nbsp;<span id="grandtotal"><i class="fa fa-peso-sign mr-2"></i><?=number_format($gtot,2)?></span></label>
                      </div>
                      <div class="col-md-6" style="text-align: right;"> 
                        <a type="button" href="shop.php" class="btn btn-warning" data-dismiss="modal"><span><i class="fa fa-store mr-2"></i></span>Shop</a>
                      </div>

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

    $('.item-qty').on('input',function(){
        var input = $(this);
        var did = input.attr("target-id");
        var max = parseInt(input.attr("max"));
        var val = parseInt(input.val());
        var qty = parseInt(input.attr("qty-before"));
        var pid = input.attr("pid");
        var price = parseFloat(input.closest('tr').find('#price-'+did).text());

        if(val<=max&&val!=0){
          $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
              changeQty: "qty",
              did: did.trim(),
              qty_before: qty,
              item_qty: val,
              pid: pid,
              total: price*val
            },
            beforeSend: function(){
              $("#pre-loader").css("display", "flex");
            },
            success: function(res){
              const data = JSON.parse(res);

              if(data.response == 'success'){
                toastr.success("Cart Updated!");
              } else{
                toastr.error("Error occurred!");
              }
              input.attr("qty-before",val);
              input.closest('tr').find('#total-'+did).text(price*val);
              calculate();
              $("#pre-loader").css("display", "none");
            }
          })
        } else{
          toastr.info("Quantity must be greater than 1 and lesser than "+(max+1));
        }

    });

    $('#order-items-tbl').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": [
            {
                text: '&nbsp;&nbsp;Payment History',
                className: 'orderBtn', 
                action: function ( e, dt, node, config ) {
                      location.href='payments.php';
                }
            }
        ]
    }).buttons().container().appendTo('#order-items-tbl_wrapper .col-md-6:eq(0)');

    var icon = $('<i>',{class: 'fa fa-peso-sign mr-2'});
    $('.orderBtn').addClass('btn btn-sm btn-outline-success bg-transparent').prepend(icon).css('border-radius','5px').hover(function(){
      $(this).toggleClass("bg-transparent bg-success");
    });

    $('.itemDelete').on('click', function(){
          //$(this).preventDefault();
          var details_id = $(this).attr("target-id");
          var tr =$("#order-tr-"+details_id);
          var pid = tr.find('.pid').val();
          var qty = tr.find('.qty').val();;

          $.ajax({
            url: 'ajax.php?delete='+details_id+'&pid='+pid+'&qty='+qty,
            type: 'GET',
            beforeSend: function(){
              $("#pre-loader").css("display","flex");
            },
            success: function(response) {
              const data = JSON.parse(response);
              if(data.response=='ok'){

                toastr.success('Item has been deleted.');
                tr.remove();
              } else{
                toastr.error('Error deleting item.');
              }
              $("#pre-loader").css("display","none");

              calculate();
            },
            error: function() {
              toastr.error('Item not found!');
            }

          });

        });



  });

  function calculate(){
    var gt = 0;
    $('.total').each(function(){
      gt+=parseFloat($(this).text());
    })

    $('#grandtotal').text(formatter.format(gt));
    $('#header-gtot').text(formatter.format(gt));
}


</script>

<script type="text/javascript">
  
 $("#current_order").DataTable({
      dom: 'pt',
       "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
       order: [[3, 'desc']],
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
      "emptyTable": "No items in cart. Shop now!"
    }
   });
</script>

<?php require_once './footer.php'; ?>