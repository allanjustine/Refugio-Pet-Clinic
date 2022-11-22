<?php 
require_once './header.php'; 
include('inc/code-generator.php');
include('inc/analytics.php');
?>

<script type="text/javascript">
	$('#nav-prod').find('a').toggleClass('active');
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
								 <a  href="#" data-target="" class="btn btn-sm btn-outline-success disabled" style="border-radius: 5px;" aria-haspopup="" aria-expanded="">
					          <i class="fas fa-bone mr-2"></i>Products
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
							$get_product = $pdo->prepare("SELECT * FROM `products` where status='INSTOCK' ORDER BY `id` DESC");
							$get_product->execute();
							while ($product = $get_product->fetch(PDO::FETCH_OBJ)) {
                                    // var_dump($product);
						?>

							<div class="card shadow product show" id="prod-<?= $product->id ?>">

								<input type="hidden" class="id" id="prod_id-<?= $product->id ?>" value="<?= $product->id ?>">							
								<input type="hidden" class="name" id="prod_name-<?= $product->id ?>" value="<?= $product->name ?>">								
								<input type="hidden" class="code" id="prod_code-<?= $product->id ?>" value="<?= $product->code ?>">
								<input type="hidden" class="desc" id="prod_desc-<?= $product->id ?>" value="<?= $product->description ?>">
								<input type="hidden" class="price" id="prod_price-<?= $product->id ?>" value="<?= $product->price ?>">
								<input type="hidden" class="stock" id="prod_stock-<?= $product->id ?>" value="<?= $product->stock ?>">
								<input type="hidden" class="cat" id="prod_cat-<?= $product->id ?>" value="<?= $product->category ?>">

								<div class="" style="position: relative;">
									<img class="card-img-top" src="<?=$baseurl?>admin/uploads/img/products/<?= $product->img ?>" alt="Image Description">

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
									<a class="btn btn-sm btn-outline-primary btn-pill disabled"  href="#"><?= $product->code?></a>
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


<script type="text/javascript">

	  $("[data-toggle=popover]").popover();

      $('[data-toggle=popover]').on('click', function (e) {
        $('[data-toggle=popover]').not(this).popover('hide');
    });

</script>

<script type="text/javascript">

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