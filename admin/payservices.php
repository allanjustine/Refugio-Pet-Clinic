

<?php 
require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');

   if(isset($_POST['payService'])) {


  $id = $_POST['service_id'];
  $service_code = $_POST['service_code'];
  $payment_no = $_POST['payment_no'];
  $payment_method = $_POST['payment_method'];


  $update = $pdo->prepare("UPDATE services set status='PAID', date_paid=now(), ref_num=:payment_no, payment_method=:payment_method where id=:id ");
  $update->bindParam(":payment_no",$payment_no);
  $update->bindParam(":payment_method",$payment_method);
  $update->bindParam(":id",$id);

  if($update->execute()){

        ?>
 
      <script type="text/javascript">
        window.addEventListener("load", function() {
          swal({
            title: "Clinical Service Paid Successfully",
            text: "Order <?php echo $service_code ?>, Has Been Paid",
            icon: "success",
            showCancelButton: false,
            showConfirmButton: false,
            buttons: false
          });
        });
      </script>
      <?php

     header('refresh:2;services-print.php#'.$id);
    } else {
      ?>
      <script>
        window.addEventListener("load", function() {
          swal({
            title: "Error",
            text: "Clinical Service Payment Failed",
            icon: "error",
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false
          });
        });
      </script>

      <?php
     header('refresh:2;payservices.php');
    }


}

?>

<script type="text/javascript">
  $('#nav-payser').find('a').toggleClass('active');
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
            <h3 class="card-title">CLINICAL SERVICES HISTORY</h3>
          </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                          <tr>
                            <th>APPT CODE</th>
                            <th>CUSTOMER</th>
                            <th>TITLE</th>
                            <th>DATE</th>
                            <th>Bill</th>
                            <th>STATUS</th>
                            <th style="width: 10px !important">ACTIONS</th>
                          </tr>
                        </thead>
                        <tbody id="event-today-list-tbody">
                         <?php
                         $get_event = $pdo->prepare("SELECT services.id as service_id,services.status,services.bill, events.appt_code, events.customer_id, customers.name, events.title, events.description, DATE_FORMAT(events.start, '%d - %M %Y %h:%i %p') as start from services inner join events on services.event_id=events.id inner join customers on events.customer_id=customers.id  ORDER BY `start` ASC");
                         $get_event->execute();
                         while ($event = $get_event->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
                          ?>

                          <tr>
                            <input type="hidden" id="service-status-<?=$event->service_id?>" value="<?=$event->status?>">
                            <input type="hidden" id="service-description-<?=$event->service_id?>" value="<?=$event->description?>">
                            <input type="hidden" id="service-id-<?=$event->service_id?>" value="<?=$event->service_id?>">
                            <td><input type="hidden" id="service-code-<?=$event->service_id?>" value="<?=$event->appt_code?>"><?=$event->appt_code?></td> 
                            <td><input type="hidden" id="service-name-<?=$event->service_id?>" value="<?=$event->name?>"><?=$event->name?></td> 
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


 <div class="modal fade" id="payService">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Clinical Service Info&nbsp;&nbsp;</h3 > <span class="badge" id="status-badge"></span>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body" id="order-card">
              <form method="POST" enctype="multipart/form-data">
                 <input type="hidden" name="service_id" value="" id="service-id" class="form-control" readonly>
                <div class="form-row d-flex" style="width: 100%">
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
                    <label style="display: block !important;">Customer</label>
                    <input style="display: block !important; width: 100%; background-color: transparent;" type="text" name="cus_name" class="form-control" id="cus-id" value="" readonly></select>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12" style="width: 50%">
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
  
  function payService(id){

    $("#pay-btn").remove();
    $("#service-print-btn").remove();

    $('#service-id').val($('#service-id-'+id).val());
    $('#cus-id').val($('#service-name-'+id).val());
    $('#service-code').val($('#service-code-'+id).val());
    $('#service-bill').val($('#service-bill-'+id).val());
    $('#service-title').val($('#service-title-'+id).val());
    $('#service-desc').val($('#service-description-'+id).val());


    if($('#service-status-'+id).val()=='PAID'){
      $("span#status-badge").html('PAID');
      $("#service-btns").prepend(`<a href="services-print.php#${id}" type="button" id="service-print-btn" class="btn btn-info" ><span class="fa fa-print mr-2"></span>Print</a>`);
      $("span#status-badge").removeClass("badge-warning");
      $("span#status-badge").addClass("badge-success");
      $("#payment-method").attr("disabled","disabled");

    } else {
      $("span#status-badge").html('UNPAID');
       $("#service-btns").prepend(`<button type="submit" name="payService" value="" id="pay-btn" class="btn btn-warning"><span class="fa fa-hand-holding-dollar mr-2"></span>Pay</button>`);
      $("span#status-badge").removeClass("badge-success");
      $("span#status-badge").addClass("badge-warning");

      $("#payment-method").removeAttr("disabled");
    }


      $('#payService').modal().show();
  }



</script>


<?php require_once './footer.php'; ?>