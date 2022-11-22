<?php 
require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');
?>

<script type="text/javascript">
	$('#nav-prod').find('a').toggleClass('active');
</script>

<?php  
  if(isset($_POST['deleteProduct'])){

    $prod_id  = $_POST['prod_id'];
    $prod_code  = $_POST['prod_code'];

    $update = $pdo->prepare("UPDATE products set status='TRASH' where id=:id");
    $update->bindParam(":id",$prod_id);

    if ($update->execute()) {
            //echo "Application Submitted Successfully";
            ?>

            <script type="text/javascript">
                window.addEventListener("load", function() {
                    swal({
                        title: "Product Deleted Successfully",
                        text: "<?php echo $prod_code ?>, Has Been Deleted",
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
                        text: "Delete Product Failed",
                        icon: "error",
                        showConfirmButton: false,
                        showCancelButton: false,
                        buttons: false
                    });
                });
            </script>

        <?php
        }
        header("refresh:2,products.php");


  }

	if (isset($_POST['addProduct'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_cat']) || empty($_POST['prod_price']) || empty($_POST['prod_stock'])) {
    $err = "Blank Values Not Accepted";
  } else {
  	$prod_code  = $_POST['prod_code'];
    $prod_name = $_POST['prod_name'];
    $prod_desc = $_POST['prod_desc'];
    $prod_cat = $_POST['prod_cat'];
    
    $prod_img = $_FILES['prod_img']['name'];
    move_uploaded_file($_FILES["prod_img"]["tmp_name"], "uploads/img/products/" . $_FILES["prod_img"]["name"]);
    
    $prod_stock = $_POST['prod_stock'];
    $prod_price = $_POST['prod_price'];

    //Insert Captured information to a database table
    $insert = $pdo->prepare("INSERT INTO products ( code, name, img, description, category, price, stock ) VALUES(:code, :name, :img, :description, :category, :price, :stock)");
    $insert->bindParam(":code", $prod_code);
    $insert->bindParam(":name", $prod_name);
    $insert->bindParam(":img", $prod_img);
    $insert->bindParam(":description", $prod_desc);
    $insert->bindParam(":category", $prod_cat);
    $insert->bindParam(":price", $prod_price);
    $insert->bindParam(":stock", $prod_stock);

    $insert->execute();

     if ($insert->rowCount()) {
            //echo "Application Submitted Successfully";
            ?>



            <script type="text/javascript">
                window.addEventListener("load", function() {
                    swal({
                        title: "Product Added Successfully",
                        text: "<?php echo $prod_name ?>, Has Been Inserted",
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
                        text: "Add Product Failed",
                        icon: "error",
                        showConfirmButton: false,
                        showCancelButton: false,
                        buttons: false
                    });
                });
            </script>

        <?php
        }
        header("refresh:2,products.php");
  }
}

if (isset($_POST['editProduct'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_cat']) || empty($_POST['prod_price']) || empty($_POST['prod_stock'])) {
    $err = "Blank Values Not Accepted";
  } else {
  	
    	$prod_code  = $_POST['prod_code'];
	  	$prod_id  = $_POST['prod_id'];
	    $prod_name = $_POST['prod_name'];
	    $prod_desc = $_POST['prod_desc'];
	    $prod_cat = $_POST['prod_cat'];
			$prod_img = $_FILES['prod_img']['name'];
			$prod_stock = $_POST['prod_stock'];
	    $prod_price = $_POST['prod_price'];


	    if($_FILES['prod_img']['name'] != "") {
		  	move_uploaded_file($_FILES["prod_img"]["tmp_name"], "uploads/img/products/" . $_FILES["prod_img"]["name"]);
		    
		    $update = $pdo->prepare("UPDATE products set code=:code, name=:name, img=:img, description=:description, category=:category, price=:price, stock=:stock where id=".$prod_id);

		    $update->bindParam(":code", $prod_code);
		    $update->bindParam(":name", $prod_name);
		    $update->bindParam(":img", $prod_img);
			  $update->bindParam(":description", $prod_desc);
		    $update->bindParam(":category", $prod_cat);
		    $update->bindParam(":price", $prod_price);
		    $update->bindParam(":stock", $prod_stock);
		  } else {
				$update = $pdo->prepare("UPDATE products set code=:code, name=:name, description=:description, category=:category, price=:price, stock=:stock where id=".$prod_id);
				$update->bindParam(":code", $prod_code);
		    $update->bindParam(":name", $prod_name);
			  $update->bindParam(":description", $prod_desc);
		    $update->bindParam(":category", $prod_cat);
		    $update->bindParam(":price", $prod_price);
		    $update->bindParam(":stock", $prod_stock);
			}

     if ($update->execute()) {
            //echo "Application Submitted Successfully";
            ?>



            <script type="text/javascript">
                window.addEventListener("load", function() {
                    swal({
                        title: "Product Updated Successfully",
                        text: "<?php echo $prod_name ?>, Has Been Updated",
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
                        text: "Update Product Failed",
                        icon: "error",
                        showConfirmButton: false,
                        showCancelButton: false,
                        buttons: false
                    });
                });
            </script>

        <?php
        }
        header("refresh:2,products.php");
  }
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
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
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-fish"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">PET FOOD</span>
              <span class="info-box-number"><?=$pet_food?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-pills"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">VITAMINS</span>
              <span class="info-box-number"><?=$vitamins?></span>
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
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-syringe"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">MEDICINE</span>
              <span class="info-box-number"><?=$medicine?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-paw"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">OTHERS</span>
              <span class="info-box-number"><?=$other_cat?></span>
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
            <div  style="display: flex;">
              <div class="col-md-6">
                <a  href="#" data-target="#addProduct" class="btn btn-sm btn-outline-success" role="button" data-toggle="modal" style="border-radius: 5px;" aria-haspopup="" aria-expanded="">
                    <i class="fas fa-plus"></i>&nbsp;Add New Product
                  </a>
              </div>
              <div class="col-md-6" >
                <div class="input-group input-group-sm mr-2" style="width: 100%;">
                  <input type="text" name="table_search" onkeyup="searchProduct(this.value)" id="search-bar" class="form-control float-riseght" placeholder="Search">

                  <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                  <div style="display: flex;justify-content: center; align-items: center;" class="ml-2">
                    <div class="btn-group">
                      <button type="" class="btn p-0" data-toggle="dropdown">
                      <span><i class="fa-solid fa-bars-staggered"></i></span>
                      </button>
                      <style type="text/css">
                        .custom-control-label{
                          font-weight: 500 !important;
                        }
                      </style>
                      <div class="dropdown-menu dropdown-menu-right" style="padding: 10px;" onclick="event.stopPropagation();"n role="menu">
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input filter-cb" type="checkbox" value="Pet Food" id="cb-1" checked>
                          <label for="cb-1" class="custom-control-label">Pet Food</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input filter-cb" type="checkbox" id="cb-2" value="Vitamins" checked>
                          <label for="cb-2" class="custom-control-label">Vitamins</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input filter-cb" type="checkbox" id="cb-3" value="Medicine" checked>
                          <label for="cb-3" class="custom-control-label">Medicine</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input filter-cb" type="checkbox" id="cb-4" value="Others" checked>
                          <label for="cb-4" class="custom-control-label">Others</label>
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
					<!-- /.card-header -->
					<div class="card-body p-0">
						<div  id="product-container" class="" style="
						padding: 50px;
						display: grid;
						grid-template-columns: repeat(auto-fill, 200px);
						justify-content: space-between;
						gap: 50px 50px;
						">
						<?php
							$get_product = $pdo->prepare("SELECT * FROM `products`where status='INSTOCK' ORDER BY `id` DESC");
							$get_product->execute();
							while ($product = $get_product->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
						?>

							<div class="card shadow product show" id="prod-<?= $product->id ?>">

								<input type="hidden" class="id" id="prod_id-<?= $product->id ?>" value="<?= $product->id ?>">								
								<input type="hidden" class="img" id="prod_img-<?= $product->id ?>" value="<?=$baseurl.'admin/uploads/img/products/'.$product->img ?>">
								<input type="hidden" class="name" id="prod_name-<?= $product->id ?>" value="<?= $product->name ?>">								
								<input type="hidden" class="code" id="prod_code-<?= $product->id ?>" value="<?= $product->code ?>">
								<input type="hidden" class="desc" id="prod_desc-<?= $product->id ?>" value="<?= $product->description ?>">
								<input type="hidden" class="price" id="prod_price-<?= $product->id ?>" value="<?= $product->price ?>">
								<input type="hidden" class="stock" id="prod_stock-<?= $product->id ?>" value="<?= $product->stock ?>">
								<input type="hidden" class="cat" id="prod_cat-<?= $product->id ?>" value="<?= $product->category ?>">

								<div class="" style="position: relative;">
									<img class="card-img-top" src="uploads/img/products/<?= $product->img ?>" alt="Image Description">

									<div class="pt-3 pl-3" style="position: absolute; top:0; left: 0;">
									    <?php
									        if($product->stock<=10 && $product->stock!=0){
									            echo '<span class="badge badge-warning badge-pill">'.$product->stock.'</span>';
									        } else if($product->stock==0){
									            echo '<span class="badge badge-danger badge-pill">OUT OF STOCK</span>';
									        } else {
									            echo '<span class="badge badge-success badge-pill">'.$product->stock.'</span>';
									        }
									    
									    ?>
									    
									</div>

								</div>
								<div class="card-footer text-center py-4 mt-auto">
									<a class="d-inline-block text-secondary small font-weight-medium mb-1" href="#"><?= $product->category?></a>
									<h3 class="font-size-1 font-weight-normal">

										<button class="text-secondary btn btn-outline-transparent" href="#" id="pop-<?= $product->id?>" tabindex="0" role="button" data-toggle="popover" data-trigger="click"  data-container="body" title="<?= $product->name?>" data-content="<?= $product->description?>"><h5><?= $product->name?></h5></button>
									</h3>
									<div class="d-block font-size-1 mb-2">
										<span class="font-weight-medium"><i class="fas fa-peso-sign"></i><?= number_format($product->price, 2)?></span>
									</div>
									<a class="btn btn-sm btn-outline-primary btn-pill transition-3d-hover px-5 " style="border-radius: 50px" onclick="viewProduct(<?= $product->id ?>)" href="#"><span><i class="fas fa-edit"></i></span>&nbsp;Update</a>
								</div>
							</div>

						<?php
							}

						?>
							
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


 <div class="modal fade" id="addProduct">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Product Info</h3 >
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Name</label>
                    <input type="text" name="prod_name" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Code</label>
                    <input type="text" name="prod_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="" required>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Image</label>
                    
                    <div class="input-group" style="column-gap: 2px;" >
                        <div class="input-group-addon" style="width:10%">
                          <img class="add-img-tag" id="add-img" src="" style="max-width: 100%; max-height: 100%;">
                        </div>
                        <input style="width:85%" accept="image/*" type="file" name="prod_img" class="btn btn-outline-success form-control add-img" value="" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label>Category</label>
                    <select name="prod_cat" class="form-control" value="" required>
                    	<option value="Pet Food">Pet Food</option>
                    	<option value="Vitamins">Vitamins</option>
                    	<option value="Medicine">Medicine</option>
                    	<option value="Others">Others</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Price</label>
                    <input type="number" min="1" name="prod_price" class="form-control" value="" required>
                  </div>
                  <div class="col-md-6">
                    <label>Quantity</label>
                    <input type="number" min="1" name="prod_stock" class="form-control" value=""  required>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Description</label>
                    <textarea rows="5" name="prod_desc" class="form-control" value="" required></textarea>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addProduct" value="Add Product" class="btn btn-success" value="">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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


 <div class="modal fade" id="viewProduct">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom ">
          <div class="modal-content">
            <div class="modal-body">
            <div class="card" style="box-shadow: none !important; margin-bottom:0 !important">
            <div class="card-header border-0" >
              <h3 style="display: inline !important">Product Info</h3 >
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="float-right" aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="card-body">

							<form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Name</label>
                    <input type="text" name="prod_name" class="form-control name" required>
                    <input type="hidden" name="prod_id" class="form-control id" required>
                  </div>
                  <div class="col-md-6">
                    <label>Code</label>
                    <input type="text" name="prod_code" value="" class="form-control code" value="" required>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Image</label>
                    <div class="input-group" style="column-gap: 2px;" >
                        <div class="input-group-addon" style="width:10%">
                          <img class="img-tag hidden" id="img-modal" src="" style="max-width: 100%; max-height: 100%;">
                        </div>
                    		<input style="width:85%" accept="image/*" type="file" id="" name="prod_img" class="btn btn-outline-success form-control img img-modal" value="" placeholder="">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label>Category</label>
                    <select name="prod_cat" class="form-control cat" value="" required>
                    	<option value="Pet Food">Pet Food</option>
                    	<option value="Vitamins">Vitamins</option>
                    	<option value="Medicine">Medicine</option>
                    	<option value="Others">Others</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Price</label>
                    <input type="number" min="1" name="prod_price" class="form-control price" value="" required>
                  </div>
                  <div class="col-md-6">
                    <label>Quantity</label>
                    <input type="number" min="1" name="prod_stock" class="form-control stock" value=""  required>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Description</label>
                    <textarea rows="5" name="prod_desc" class="form-control desc" value="" required></textarea>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="button" name="deleteProduct" id="del-prod-btn" onclick="delProduct()" value="Delete" class="btn btn-danger" value="">
                    <input type="submit" name="editProduct" value="Save" class="btn btn-success" value="">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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

<script type="text/javascript">

    function delProduct(){

      swal({
            title: `Delete Product?`,
            text: `Selected product will be deleted.`,
            icon: "warning",
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
            closeOnClickOutside: false
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result) {
              $("#del-prod-btn").attr("type","submit");
              $("#del-prod-btn").removeAttr("onclick");
              $("#del-prod-btn").click();

            }
          });

    }

		$(".img-modal").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img-modal').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $(".add-img").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#add-img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

	  $("[data-toggle=popover]").popover();

      $('[data-toggle=popover]').on('click', function (e) {
        $('[data-toggle=popover]').not(this).popover('hide');
    });

</script>

<script type="text/javascript">

	$('#addProduct').on('shown.bs.modal',function(){
		$('.popover').popover('hide');
	});

	function viewProduct(id){
				$('.popover').popover('hide');

				$('#viewProduct .name').val($('#prod_name-'+id).val());
				$('#viewProduct .id').val($('#prod_id-'+id).val());
				$('#viewProduct .code').val($('#prod_code-'+id).val());
				$('#viewProduct .desc').val($('#prod_desc-'+id).val());
				$('#viewProduct .price').val($('#prod_price-'+id).val());
				$('#viewProduct .stock').val($('#prod_stock-'+id).val());
				$('#viewProduct .cat').val($('#prod_cat-'+id).val());
				$('#viewProduct .img-tag').attr('src',$('#prod_img-'+id).val());
				
				$('#viewProduct').modal().show();

			}

  $(function(){
    $(".filter-cb").click(function(){
      searchProduct($("#search-bar").val());
    })
  })

  function searchProduct(keyword) {
    var filter = [];
      $(".filter-cb").each(function(i,obj){
        if($(obj).is(":checked")){
          filter.push($(obj).val());
        }
      });

		$('div.product').each(function(){
			$('.popover').popover('hide');
			if(keyword.trim().length>0) {
				if( ( $(this).find('.cat').val().toLowerCase().trim().search(keyword.toLowerCase().trim()) > -1 || $(this).find('.name').val().toLowerCase().trim().search(keyword.toLowerCase().trim()) > -1 || $(this).find('.code').val().toLowerCase().trim().search(keyword.toLowerCase().trim()) > -1 || $(this).find('.desc').val().toLowerCase().trim().search(keyword.toLowerCase().trim()) > -1 || $(this).find('.stock').val().toLowerCase().trim().search(keyword.toLowerCase().trim()) > -1 ) && filter.includes($(this).find('.cat').val().trim())){
          
					$(this).removeAttr('hidden').toggleClass('show');

					
				} else{
					$(this).attr('hidden','hidden');
				}
			} else{
        if(filter.includes($(this).find('.cat').val().trim())){
          $(this).removeAttr('hidden').toggleClass('show');
        } else{
          $(this).attr('hidden','hidden');
        }
        
      }

		})
	}
	
</script>

<?php require_once './footer.php'; ?>