<!--
 * This file is part of the 247Commerce BigCommerce RETAIL MERCHANT App.
 *
 * ©247 Commerce Limited <info@247commerce.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 -->
<?php include('header.php');
?>
<link rel="stylesheet" type="text/css" href="<?= getenv('app.ASSETSPATH') ?>css/datatable/jquery.dataTables.min.css">
    <main class="main-content">
      <div class="container">
        <div class="col-md-10 col-12 mx-auto">
        <div class="row mb-2">
            <div class="col-md-6 col-sm-6 col-5">
                <h5>Order Details</h5>
            </div>
            <div class="col-md-6 text-end col-sm-6 col-7">
                <a href="<?= getenv('app.baseURL') ?>home/dashboard"><h5><i class="fas fa-arrow-left me-2"></i>Back to dashboard</h5></a>
            </div>
        </div>
        <div class="row top-bar order-srch">
          <div class="col-md-12 col-sm-8 col-8 top-search">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input rounded-end" id="exampleInputEmail1" placeholder="Search OrderID">
          </div>
        </div>
        <div class="table-responsive table-color">
            <table class="table" id="orderdetails_dashboard">
				<thead class="cf">
                  <tr class="header" id="table_columns">
                    <th><div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                        </div>
                    </th>
                    <th>Payment Number</th>
                    <th>Payment type</th>
                    <th>Payment Status</th>
                    <th>Settlement Status</th>
                    <th>Currency</th>
                    <th>Total</th>
                    <th>Amount Paid</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>
				</thead>
				<tbody id="table_data_rows">
									  
				</tbody>
                  
            </table>
        </div>
      </div>
    </div>
    </main>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/jquery-min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" charset="utf8" src="<?= getenv('app.ASSETSPATH') ?>js/datatable/jquery.dataTables.min.js"></script>
		<script type="text/javascript" charset="utf8" src="<?= getenv('app.ASSETSPATH') ?>js/datatable/datatable-responsive.js"></script>
        <script src="<?= getenv('app.ASSETSPATH') ?>js/order-details.js?v=1.00"></script>
    <script>
		var app_base_url = "<?= getenv('app.baseURL') ?>";
		$(document).ready(function(){
			X247OrderDetails.main_data('home/orderdetailsprocessing','orderdetails_dashboard');
		});
	</script>
  </body>
</html>
