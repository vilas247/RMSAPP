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
    <main class="main-content">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-10 offset-md-1">
            <form action="<?= getenv('app.baseURL') ?>settings/updateCustomButton" method="POST" >
			<div class="card">
                  <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-6">
                            <p>Custom Payment Button</p>
                        </div>
                        <div class="col-md-6 text-end col-sm-6 col-6">
                            <a href="<?= getenv('app.baseURL') ?>home/dashboard"><h5><i class="fas fa-arrow-left me-2"></i>Back to dashboard</h5></a>
                        </div>
                    </div>
                  </div>
					<?php
						$container_id = '.checkout-step--payment .checkout-view-header';
						$html_code = '<button id="form-submit" type="submit" class="button button--action button--large button--slab optimizedCheckout-buttonPrimary" style="background-color: #424242;border-color: #424242;color: #fff;" disabled>Continue</button>';
						$css_prop = '#form-submit{display:block; background-color: #00FF00 !important; color: #000000 !important; border-color: #FF0000 !important;}';
						$result_c = $buttonDetails;
						if(count($result_c) > 0){
							$result_c = $result_c[0];
						}else{
							$result_c['container_id'] = $container_id;
							$result_c['css_prop'] = $css_prop;
							$result_c['html_code'] = $html_code;
						}
						//print_r($result_c);exit;
						$enable = '';
						if(isset($result_c['is_enabled']) && $result_c['is_enabled'] == "1"){
							$enable = "checked";
						}
					?>
                  <div class="card-body">
                    <div class="row p-3">
                        <div class="col-md-6 col-sm-6 col-12 mb-3">
                            <p><strong>Container Id</strong></p>
                            <div class="my-3 col-md-12">
                              <textarea class="form-control" name="container_id" id="container_id" required rows="6"><?= @$result_c['container_id'] ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <p><strong>Css Properties</strong></p>
                                <div class="form-check form-switch">
                                  <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="is_enabled" <?= $enable ?> />
                                </div>
                            </div>
                            <div class="my-3 col-md-12">
                              <textarea class="form-control" name="css_prop" id="css_prop" rows="6"><?= @$result_c['css_prop'] ?></textarea>
                            </div>
                        </div>
                    </div>
					<div class="row p-3">
                        <div class="col-md-12 col-sm-12 col-12 mb-12">
                            <p><strong>Html Code</strong></p>
                            <div class="my-3 col-md-12">
                              <textarea class="form-control" rows="6" required name="html_code" id="html_code" placeholder='<button id="form-submit" type="submit" class="button button--action button--large button--slab optimizedCheckout-buttonPrimary" style="background-color: #424242;border-color: #424242;color: #fff;" disabled>Continue</button>' id="exampleFormControlTextarea1" rows="5"><?= @$result_c['html_code'] ?></textarea>
                            </div>
                        </div>
                    </div>
                  </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-end">
					<button type="button" id="resetCustom" class="btn update">Reset</button>&nbsp;&nbsp;&nbsp;
                    <button type="submit" class="btn update">Update</button>
                </div>
            </div>
			</form>
          </div>        
        </div>
      </div>
    </main>
  </body>
</html>
<script src="<?= getenv('app.ASSETSPATH') ?>js/jquery-min.js"></script>
<script>
	var id = '<?= $container_id ?>';
	var css = '<?= base64_encode($css_prop) ?>';
	var html_code = '<?= $html_code ?>';
	$('body').on('click','#resetCustom',function(){
		$('body #container_id').val(id);
		$('body #css_prop').val(window.atob(css));
		$('body #html_code').val(html_code);
	});
</script>