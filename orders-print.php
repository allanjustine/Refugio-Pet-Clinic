<?php 
require_once './header.php'; 

?>


<script type="text/javascript">
  $('#nav-pay').find('a').toggleClass('active');
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" >
 <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
   
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content" style="padding-top: 10px;">
  <div class="container-fluid">

    <div class="row mt-3">
      <div class="col-md-12">
        <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row mt-3">
                  <div class="col-md-6 d-flex" >
                    <div class="img-fluid">
                      <img src="<?=$baseurl?>/assets/img/logo.jpg"  style="height: 200px;">
                    </div>
                    <address class="mt-5">
                      <h1 class="card-title"><strong>PET CLINIC</strong></h1>
                      <br>
                      <br>
                      Tubigon Mkt Rd, Tubigon 6329<br>
                      Bohol, Philippines<br>
                      Phone: (555) 539-1037<br>
                      Email: admin@petclinic.com
                    </address>
                  </div>
                  <div class="col-md-6 pr-10">
                    <div class="mt-5 " style="text-align: right ;margin-right: 40px;">

                      <b>Invoice #<span id="inv-no">007612</span></b><br><br>
                      <b>Invoice To: </b><span id="customer">968-34567</span><br>
                      <b>Order ID: </b><span id="order-code">4F3S8J</span><br>
                      <b>Order Date: </b><span id="order-date">2/22/2014</span><br>
                      <b>Date Paid: </b><span id="order-date-paid">2/22/2014</span><br>
                                  
                    </div>
                    
                  </div>
                </div>

                <div class="row mt-3 p-5">
                 <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ITEM</th>
                      <th>QTY</th>
                      <th>PRICE</th>
                      <th>TOTAL</th>
                    </tr>
                  </thead>
                  <tbody id="order-tbody">
                    
                  </tbody>
                  <tfoot>

                  </tfoot>
                </table>
              </div>
                  

              <div class="row  pl-5 pr-5 bg-default">
                
                      <div class="col-6"></div>
                      <div class="col-6">
                        <div class="d-flex">
                          <strong class="mr-3" style="width: 50%; text-align: right;">GRANDTOTAL</strong>
                          <p style="width: 50%; text-align: right;" id="grandtotal-p">0.00</p>
                        </div>
                        <div class="d-flex">
                          <strong class="mr-3" style="width: 50%; text-align: right;">PAYMENT METHOD</strong>
                          <p style="width: 50%; text-align: right;" id="method-p">CASH</p>
                        </div>
                        <div class="d-flex">
                          <strong class="mr-3" style="width: 50%; text-align: right;">REFERENCE #</strong>
                          <p style="width: 50%; text-align: right;" id="ref-p">N/A</p>
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

<script type="text/javascript">

  var id = window.location.hash.replace('#',"").trim();

  if(id.length>0){
    $.ajax({
    url: 'ajax.php',
    data: {
      orderInfo: id
    },
    type: 'POST',
    beforeSend: function(){
      $("#pre-loader").css("display","flex");
    },
    success: function(res){
      const data = JSON.parse(res);

      if(data[0].status == 'PAID') {
        $("#inv-no").text(addLeadingZeros(data[0].id, 5));
        $("#order-date-paid").text(data[0].date_paid);
        $("#method-p").text(data[0].payment_method);
        $("#ref-p").text(data[0].ref_num);
      } else {
        $("#inv-no").html('<span class="badge badge-danger">UNPAID</span');
        $("#method-p").html('<span class="badge badge-danger">UNPAID</span');
        $("#ref-p").html('<span class="badge badge-danger">UNPAID</span');
        $("#order-date-paid").html('<span class="badge badge-danger">UNPAID</span');
      }

        $("#order-code").text(data[0].code);
        $("#order-date").text(data[0].order_date);
        $("#customer").text(data[0].name);


      $.ajax({
        url: 'ajax.php?order='+id,
        type: "GET",
        beforeSend: function(){
          $("#pre-loader").css("display","flex");
        },
        success: function (res){
          const data = JSON.parse(res);
          let html='';
          var tot = 0;
          data.forEach(order =>{
            tot+=parseFloat(order.total);
            html = `
            <tr>
            <td>${order.name}</td>
            <td>${order.qty}</td>
            <td>${formatter.format(order.price).replace(/^(\D+)/, '$1 ')}</td>
            <td>${formatter.format(order.total).replace(/^(\D+)/, '$1 ')}</td>
            </tr>
            `;
            $("#order-tbody").append(html);
          });
          $("#grandtotal-p").text(formatter.format(tot).replace(/^(\D+)/, '$1 '));

          $("#pre-loader").css("display","none");

           window.print();
         }
       });
      $("#pre-loader").css("display","none");
    }
  });
  } else {
    location.href = "payments.php";
  }

  


  function addLeadingZeros(num, totalLength) {
    return String(num).padStart(totalLength, '0');
  }



</script>

<?php require_once './footer.php'; ?>