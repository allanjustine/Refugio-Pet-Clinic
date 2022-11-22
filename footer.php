 <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-light">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright&copy;<?php echo date('Y'); ?><a href="http://localhost/refugio-pet-clinic/">Pet Clinic</a>.</strong>
    All rights reserved.
   
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery UI -->
<script src="<?= $baseurl?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Bootstrap -->
<script src="<?= $baseurl?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= $baseurl?>assets/js/adminlte.min.js"></script>

<!-- Toastr -->
<script src="<?= $baseurl?>assets/plugins/toastr/toastr.min.js"></script>

<script src="<?= $baseurl?>assets/plugins/sweetalert2/sweetalert.js"></script>
<script src="<?= $baseurl?>assets/plugins/select2/dist/js/select2.full.min.js"></script>


<script type="text/javascript">
  $("#add-event-customer").select2();

  $('.select2').select2();

  var formatter = new Intl.NumberFormat('tl-PH', {
      style: 'currency',
      currency: 'PHP',
    });

  function logout(){
    swal({
            title: `Proceed to Logout?`,
            text: ``,
            icon: "info",
             buttons: {
               confirm: {
                text: "Continue",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              },
              cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
              }
            },
            closeOnClickOutside: true
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result) {

              location.href = "logout.php";
             
            }
          });
  }

</script>


</body>
</html>
