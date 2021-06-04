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
    <main class="main-content retail-merchant">
      <div class="container">
        <div class="col-lg-10 col-12 mx-auto">
        <div class="row">
          <div class="col-12">
            <div class="card">
                  <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <p>Settings</p>
                        </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row p-3">
						<?php
							$enabled = false;
							if($clientDetails['is_enable'] == 1){
								$enabled = true;
							}
						?>
                        <div class="col-md-4 col-sm-6 col-12 my-3">
                            <p>Name</p>
                            <p><strong><?= $clientDetails['email_id'] ?></strong></p>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12 my-3">
                            <p>Configuration ID</p>
                            <p><strong><?= $clientDetails['merchant_id'] ?></strong></p>
                        </div>
                        <div class="col-md-3 col-sm-4 col-12 my-3">
                            <p>Current API Key</p>
                            <p><strong><?= $clientDetails['cardstream_signature'] ?></strong></p>
                        </div>
                        <div class="col-sm-2 col-md-1 col-12 my-3">
                            <p>Status</p>
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="actionChange" <?= ($enabled)?'checked':'' ?> value="<?= ($enabled)?'1':'0' ?>" >
                            </div>
                        </div>
                    </div>
                  </div>
            </div>
          </div>        
        </div>
        <div class="row mb-2">
            <div class="col-md-6 col-sm-6 col-5">
                <h5>Order Details</h5>
            </div>
        </div>
        <div class="row top-bar order-srch">
          <div class="col-md-12 col-sm-8 col-8 top-search">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input rounded-end" id="myInput" placeholder="Search">
          </div>
        </div>
        <div class="table-responsive table-color">
            <table id="myTable" class="table">
                  <tr class="header">
                    <th>Transaction ID</th>
                    <th>BigCommerce<br/>Order Id</th>
                    <th>Payment<br/>type</th>
                    <th>Payment<br/>Status</th>
                    <th>Currency</th>
                    <th>Total</th>
                    <th>Amount Paid</th>
                    <th>Created Date</th>
                  </tr>
				  <tbody id="rowData">
				<?php
					if(count($orderDetails) > 0){
						foreach($orderDetails as $k=>$values) {
				?>
                  <tr>
                    <td>
						<?php
							if(isset($values['api_response'])){
								$api_response = str_replace("\\","",$values['api_response']);
								$api_response = json_decode($api_response,true);
								if(isset($api_response['transactionID'])){
									echo $api_response['transactionID'];
								}else{
									echo "";
								}
							}else{
								echo "";
							}
						?>
					</td>
                    <td><?= $values['order_id'] ?></td>
                    <td><?= $values['type'] ?></td>
					<td>
						<?php
							$sstatus = '';
							if($values['status'] == "CONFIRMED"){
								$sstatus = '<span class="badge bg-success table-status-clr">'.ucfirst($values['status']).'</span>';
							}else{
								$sstatus = '<span class="badge btn-pink table-status-clr">'.ucfirst($values['status']).'</span>';
							}
						?>
						<?= $sstatus ?>
					</td>
                    <td><?= $values['currency'] ?></td>
                    <td><?= $values['total_amount'] ?></td>
                    <td><?= $values['amount_paid'] ?></td>
					<td>
						<?php
							if(isset($values['api_response'])){
								$api_response = str_replace("\\","",$values['api_response']);
								$api_response = json_decode($api_response,true);
								if(isset($api_response['timestamp'])){
									echo $api_response['timestamp'];
								}else{
									echo date("Y-m-d h:i A",strtotime($values['created_date']));
								}
							}else{
								echo date("Y-m-d h:i A",strtotime($values['created_date']));
							}
						?>
					</td>
                  </tr>
				<?php } } ?>
				</tbody>
            </table>
        </div>
      </div>
    </div>
	<!-- Modal -->
		<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
			  <div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="exampleModalLongTitle"><span><img src="<?= getenv('app.ASSETSPATH') ?>img/trash-purple.svg" style="margin-top: -5px;"></span> <span class="purple">Delete Store Token</span>  </h5>
				  <button type="button" class="closeStore" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body" id="modalContent">
				  Are you sure you want to Clear <strong>BigCommerce Store Token Details?</strong>.
				  <p>This will clear all your Data in BigCommerce</p>
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-order" id="cancelConfirmStore" data-dismiss="modal">Cancel</button>
				  <button type="button" class="btn btn-order" id="deleteConfirmStore">Delete</button>
				</div>
			  </div>
			</div>
		  </div>
    </main>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/jquery-min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.bundle.min.js"></script>
	<script src="<?= getenv('app.ASSETSPATH') ?>js/247rmsloader.js"></script>
	<script src="<?= getenv('app.ASSETSPATH') ?>js/toaster/jquery.toaster.js"></script>
<script type="text/javascript">
		var text = "Please wait...";
		var current_effect = "bounce";
		var app_base_url = "<?= getenv('app.baseURL') ?>";
		$(document).ready(function() {
			$('body').on('keyup','#myInput',function(event){
				var keycode = (event.keyCode ? event.keyCode : event.which);
				if(keycode == '13'){
					$("body").waitMe({
						effect: current_effect,
						text: text,
						bg: "rgba(255,255,255,0.7)",
						color: "#000",
						maxSize: "",
						waitTime: -1,
						source: "images/img.svg",
						textPos: "vertical",
						fontSize: "",
						onClose: function(el) {}
					});
					var searchText = $(this).val();
					$.ajax({
						type: 'POST',
						url: app_base_url + "home/dashboardSearch",
						//dataType: 'json',
						data:{searchText:searchText},
						success: function (res) {
							$("body").waitMe("hide");
							$('body #rowData').html(res);
							
						}
					});
				}
			});
			$('body').on('change','#actionChange',function(){
				$("body").waitMe({
					effect: current_effect,
					text: text,
					bg: "rgba(255,255,255,0.7)",
					color: "#000",
					maxSize: "",
					waitTime: -1,
					source: "images/img.svg",
					textPos: "vertical",
					fontSize: "",
					onClose: function(el) {}
				});
				var val = $(this).val();
				if(val == "0"){
					var url = app_base_url+'settings/bcEnablePayment';
					window.location.href = url;
				}else{
					$('body #deleteModalCenter').modal('show');
					$("body").waitMe("hide");
				}
			});
			$('body').on('click','#deleteConfirmStore',function(e){
				$("body").waitMe({
					effect: current_effect,
					text: text,
					bg: "rgba(255,255,255,0.7)",
					color: "#000",
					maxSize: "",
					waitTime: -1,
					source: "images/img.svg",
					textPos: "vertical",
					fontSize: "",
					onClose: function(el) {}
				});
				var url = app_base_url+'settings/bcDisablePayment';
				window.location.href = url;
			});
			$('body').on('click','#cancelConfirmStore,.close',function(e){
				$('body #deleteModalCenter').modal('hide');
				$('#actionChange').trigger('click');
			});
		});
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split("&"),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split("=");

				if (sParameterName[0] === sParam) {
					return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
			return false;
		};
		$(document).ready(function(){
			var enabled = getUrlParameter('enabled');
			if(enabled){
				$.toaster({ priority : "success", title : "Success", message : "RMS Payments enabled for your Store" });
			}
			var disabled = getUrlParameter('disabled');
			if(disabled){
				$.toaster({ priority : "success", title : "Success", message : "RMS Payments disabled for your Store" });
			}
			var updated = getUrlParameter('updated');
			if(updated){
				$.toaster({ priority : "success", title : "Success", message : "Payment Option Updated" });
			}
		});
	</script>
  </body>
</html>
