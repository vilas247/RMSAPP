<?php
/**
 * This file is part of the 247Commerce BigCommerce RETAIL MERCHANT App.
 *
 * ©247 Commerce Limited <info@247commerce.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controllers;

/**
 * Class WorldPay
 *
 * Represents a RETAIL MERCHANT Payment Authentication and redirection
 */
class Rmspay extends BaseController
{
	/**
	 * Index - default page
	 *
	 */
	public function index()
	{
		echo "Under Construnction";exit;
	}
	
	/**
	 * worldpay Paymet data and redirection
	 *
	 */
	public function authentication()
	{
		
		$res = array();
		$res['status'] = false;
		$res['data'] = '';
		
		helper('settingsviews');
		if(!empty($this->request->getPost('authKey')) && !empty($this->request->getPost('cartId'))){
			
			log_message('info', 'Worldpayment fields-authKey:'.$this->request->getPost('authKey'));
			log_message('info', 'Worldpayment fields-cartId:'.$this->request->getPost('cartId'));
		
			$tokenData = json_decode(base64_decode($this->request->getPost('authKey')),true);
			$email_id = $tokenData['email_id'];
			$validation_id = $tokenData['key'];
			
			if (filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
				$db = \Config\Database::connect();
				$builder = $db->table('rms_token_validation');        
				$builder->select('*');       
				$builder->where('email_id', $email_id);
				$builder->where('validation_id', $validation_id);
				$query = $builder->get();
				$result = $query->getResultArray();
				if (count($result) > 0) {
					$clientDetails = $result[0];
					$cartAPIRes = $this->getCartData($email_id,$this->request->getPost('cartId'),$validation_id);
					if(!is_array($cartAPIRes) || (is_array($cartAPIRes) && count($cartAPIRes) == 0)) {
						exit;
					}
					$cartData = $cartAPIRes;	
					$invoiceId = "RMS-".time();
					$currency = $cartData['cart']['currency']['code'];
					$cartbillingAddress = $cartData['billing_address'];
					$checkShipping = false;
					if(count($cartData['cart']['line_items']['physical_items']) > 0 || count($cartData['cart']['line_items']['custom_items']) > 0){
						$checkShipping = true;
					}else{
						if(count($cartData['cart']['line_items']['digital_items']) > 0){
							$checkShipping = false;
						}
					}
					if($checkShipping){
						$cart_shipping_address = $cartData['consignments'][0]['shipping_address'];
					}else{
						$cart_shipping_address = $cartData['billing_address'];
					}
					$totalAmount = $cartData['grand_total'];
					
					$transaction_type = "SALE";
					
					$tokenData = array("email_id"=>$email_id,"key"=>$validation_id,"invoice_id"=>$invoiceId);
					
					$db = \Config\Database::connect();
					$data = [
						'email_id' => $email_id,
						'type' => $transaction_type,
						'order_id'    => $invoiceId,
						'cart_id'    => $cartData['id'],
						'total_amount' => $cartData['grand_total'],
						'amount_paid' => "0.00",
						'currency' => $currency,
						'status' => "PENDING",
						'params' => base64_encode(json_encode($cartData)),
						'token_validation_id' => $validation_id,
					];
					$builderinsert = $db->table('order_payment_details'); 
					$builderinsert->insert($data);
					
					$key = $clientDetails['cardstream_signature'];
					
					$unique_id = uniqid();
					$req = array(
						'merchantID' => $clientDetails['merchant_id'],
						'action' => "SALE",
						'type' => "1",
						'countryCode' => $cartbillingAddress['country_code'],
						'currencyCode' => $cartData['cart']['currency']['code'],
						'amount' => sprintf("%.2f",$cartData['grand_total']),
						'orderRef' => $invoiceId,
						'transactionUnique' => $unique_id,
						'redirectURL' => getenv('app.baseURL').'rmspay/success'////,
						////'customerName' => $cartbillingAddress['first_name'],
						////'customerEmail' => $cartbillingAddress['email'],
						////'customerPhone' => $cartbillingAddress['phone'],
						////'customerAddress' => $cartbillingAddress['address1'],
						////'customerPostCode' => $cartbillingAddress['postal_code'],
						////'authenticity_token'=>"424654961f7349222a72a5c91f66a3496217b6b0a6b40225ce1a5e941d094d0c"
					);
					
					$req['signature'] = \SettingsViews::createSignature($req, $key).'|merchantID,action,type,countryCode,currencyCode,amount,orderRef,transactionUnique,redirectURL';
					$data = array(
								'id'=>'247cardstream_form',
								'url'=>getenv('bigcommerceapp.RMS_URL'),
								'modal'=>true,
								'data'=>$req,
							);
					$res['status'] = true;
					//$url = BASE_URL."cardstreamPay.php?invoiceId=".base64_encode(json_encode($invoiceId));
					$res['data'] = array();
					$res['data'] = $data;
					$res['form_id'] = '#247cardstream_form';
				}
			}
		}
		echo json_encode($res,true);exit;
	}
	
	/**
	 * worldpay Payment data and redirection
	 *
	 */
	public function success()
	{
		//print_r(json_encode($_REQUEST));exit;
		helper('settingsviews');
		helper('bigcommerceorder');
		$db = \Config\Database::connect();
		log_message('info', 'PostLink Update Order RMS Payment');
		$_REQUEST['responseCode'] = 0;
		if(isset($_REQUEST['responseCode'])){
			if($_REQUEST['responseCode'] == 0 || $_REQUEST['responseCode'] == 1 || $_REQUEST['responseCode'] == 2){
				$invoice_id = $_REQUEST['orderRef'];
				if(!empty($invoice_id)) {
					$data = [
						'status' => "CONFIRMED",
						'api_response' => addslashes(json_encode($_REQUEST)),
						'amount_paid' => @$_REQUEST['amount'],
					];
					$builderupdate = $db->table('order_payment_details'); 
					$builderupdate->where('order_id', $invoice_id); 
					$builderupdate->update($data);
					
					$builder = $db->table('order_payment_details');        
					$builder->select('*');       
					$builder->where('order_id', $invoice_id);
					$query = $builder->get();
					$result_order_payment = $query->getResultArray();

					if (isset($result_order_payment[0])) {
						$result_order_payment = $result_order_payment[0];
						
						$string = base64_decode($result_order_payment['params']);
						$string = preg_replace("/[\r\n]+/", " ", $string);
						$json = utf8_encode($string);
						$cartData = json_decode($json,true);
						$items_total = 0;
						//print_r(json_encode($cartData));exit;
						$order_products = array();
						foreach($cartData['cart']['line_items'] as $liv){
							$cart_products = $liv;
							foreach($cart_products as $k=>$v){
								if($v['variant_id'] > 0){
									$details = array();
									$productOptions = \BigCommerceOrder::productOptions($result_order_payment['email_id'],$v['product_id'],$v['variant_id'],$result_order_payment['token_validation_id']);
									
									log_message('info', "Product variant options: ".json_encode($productOptions));
									
									$temp_option_values = $productOptions['option_values'];
									$option_values = array();
									if(!empty($temp_option_values) && isset($temp_option_values[0])){
										foreach($temp_option_values as $tk=>$tv){
											$option_values[] = array(
															"id" => $tv['option_id'],
															"value" => strval($tv['id'])
														);
										}
									}
									$items_total += $v['quantity'];
									$details = array(
													"product_id" => $v['product_id'],
													"quantity" => $v['quantity'],
													"product_options" => $option_values,
													"price_inc_tax" => $v['sale_price'],
													"price_ex_tax" => $v['sale_price'],
													"upc" => @$productOptions['upc'],
													"variant_id" => $v['variant_id']
												);
									$order_products[] = $details;
								}
							}
						}
						
						$checkShipping = false;
						if(count($cartData['cart']['line_items']['physical_items']) > 0 || count($cartData['cart']['line_items']['custom_items']) > 0){
							$checkShipping = true;
						}else{
							if(count($cartData['cart']['line_items']['digital_items']) > 0){
								$checkShipping = false;
							}
						}
						$cart_billing_address = $cartData['billing_address'];
						$billing_address = array(
												"first_name" => $cart_billing_address['first_name'],
												"last_name" => $cart_billing_address['last_name'],
												"phone" => $cart_billing_address['phone'],
												"email" => $cart_billing_address['email'],
												"street_1" => $cart_billing_address['address1'],
												"street_2" => $cart_billing_address['address2'],
												"city" => $cart_billing_address['city'],
												"state" => $cart_billing_address['state_or_province'],
												"zip" => $cart_billing_address['postal_code'],
												"country" => $cart_billing_address['country'],
												"company" => $cart_billing_address['company']
											);
						if($checkShipping){
							$cart_shipping_address = $cartData['consignments'][0]['shipping_address'];
							$cart_shipping_options = $cartData['consignments'][0]['selected_shipping_option'];
							$shipping_address = array(
													"first_name" => $cart_shipping_address['first_name'],
													"last_name" => $cart_shipping_address['last_name'],
													"company" => $cart_shipping_address['company'],
													"street_1" => $cart_shipping_address['address1'],
													"street_2" => $cart_shipping_address['address2'],
													"city" => $cart_shipping_address['city'],
													"state" => $cart_shipping_address['state_or_province'],
													"zip" => $cart_shipping_address['postal_code'],
													"country" => $cart_shipping_address['country'],
													"country_iso2" => $cart_shipping_address['country_code'],
													"phone" => $cart_shipping_address['phone'],
													"email" => $cart_billing_address['email'],
													"shipping_method" => $cart_shipping_options['type']
												);
						}
						$createOrder = array();
						$createOrder['customer_id'] = $cartData['cart']['customer_id'];
						$createOrder['products'] = $order_products;
						if($checkShipping){
							$createOrder['shipping_addresses'][] = $shipping_address;
						}
						$createOrder['billing_address'] = $billing_address;
						if(isset($cartData['coupons'][0]['discounted_amount'])){
							$createOrder['discount_amount'] = $cartData['coupons'][0]['discounted_amount'];
						}
						$createOrder['customer_message'] = $cartData['customer_message'];
						$createOrder['customer_locale'] = "en";
						$createOrder['total_ex_tax'] = $cartData['grand_total'];
						$createOrder['total_inc_tax'] = $cartData['grand_total'];
						////$createOrder['geoip_country'] = "India";
						////$createOrder['geoip_country_iso2'] = "IN";
						//$createOrder['status_id'] = 11;
						$createOrder['ip_address'] = \BigCommerceOrder::get_client_ip();
						if($checkShipping){
							$createOrder['order_is_digital'] = true;
						}
						
						$createOrder['tax_provider_id'] = "BasicTaxProvider";
						$createOrder['payment_method'] = "Manual";
						$createOrder['external_source'] = "247 WORLDPAYMENT";
						$createOrder['default_currency_code'] = $cartData['cart']['currency']['code'];
						
						log_message('info', "Before create order API call");
						$bigComemrceOrderId = \BigCommerceOrder::createOrder($result_order_payment['email_id'],$createOrder,$invoice_id,$result_order_payment['token_validation_id']);
						
						
						log_message('info', "Create order API response: ".$bigComemrceOrderId);
						if($bigComemrceOrderId != "") {
							log_message('info', "Before update order API call");
							//update order status for trigger status update mail from bigcommerce
							$statusResponse = \BigCommerceOrder::updateOrderStatus($bigComemrceOrderId,$result_order_payment['email_id'],$result_order_payment['token_validation_id']);
							log_message('info', "Update order status API response: ".$statusResponse);
						}
						log_message('info', "Before delete cart API call");
						$delCartResponse = \BigCommerceOrder::deleteCart($result_order_payment['email_id'],$result_order_payment['cart_id'],$result_order_payment['token_validation_id']);
						log_message('info', "delete cart API response: ".$delCartResponse);
						$this->redirectBigcommerce($result_order_payment['email_id'],$invoice_id,$result_order_payment['token_validation_id']);
					}
				}
			}else{
				$invoice_id = $_REQUEST['orderRef'];
				$data = [
					'status' => "FAILED",
					'api_response' => addslashes(json_encode($_REQUEST))
				];
				$builderupdate = $db->table('order_payment_details'); 
				$builderupdate->where('order_id', $invoice_id); 
				$builderupdate->update($data);
				
				$db = \Config\Database::connect();
				$builder = $db->table('order_payment_details');        
				$builder->select('*');
				$builder->where('order_id', $invoice_id);
				$query = $builder->get();
				$result = $query->getResultArray();
				if (count($result) > 0) {
					$orderDetails = $result[0];
					$this->redirectBigcommerce($orderDetails['email_id'],$invoice_id,$orderDetails['token_validation_id']);
				}
			}
		}
	}
	
	/**
	 * get Cart Data from BigCommerce API
	 * @param text| $email_id
	 * @param text| $cartId
	 * @param text| $validation_id
	 * @return cart Data from BigCommerce api
	 */
	public function getCartData($email_id,$cartId,$validation_id){
		$data = array();
		if(!empty($cartId) && !empty($email_id)){
			$db = \Config\Database::connect();
			$builder = $db->table('rms_token_validation');        
			$builder->select('*');       
			$builder->where('email_id', $email_id);
			$builder->where('validation_id', $validation_id);
			$query = $builder->get();
			$result = $query->getResultArray();
			if (count($result) > 0) {
				$result = $result[0];
				$request = '';
				$url = getenv('bigcommerceapp.STORE_URL').$result['store_hash'].'/v3/checkouts/'.$cartId;
				
				$client = \Config\Services::curlrequest();
				$response = $client->request('get', $url, [
						'headers' => [
								'X-Auth-Token' => $result['acess_token'],
								'store_hash' => $result['store_hash'],
								'Accept' => 'application/json',
								'Content-Type' => 'application/json'
						]
				]);
				
				if (strpos($response->getHeader('content-type'), 'application/json') != false){
					$res = $response->getBody();
				
					$data = [
						'email_id' => $email_id,
						'type' => 'BigCommerce',
						'action'    => 'Cart Data',
						'api_url'    => addslashes($url),
						'api_request' => addslashes($request),
						'api_response' => addslashes($res),
						'token_validation_id' => $validation_id,
					];
					$builderinsert = $db->table('api_log'); 
					$builderinsert->insert($data);
					if(!empty($res)){
						$res = json_decode($res,true);
						if(isset($res['data'])){
							$data = $res['data'];
						}
					}
				}
			}
		}
		
		return $data;
	}
	/**
	 * redirect to BigCommerce
	 *
	 */
	public function redirectBigcommerce($email_id,$invoice_id,$validation_id){
		$db = \Config\Database::connect();
		$builder = $db->table('rms_token_validation');        
		$builder->select('*');       
		$builder->where('email_id', $email_id);
		$builder->where('validation_id', $validation_id);
		$query = $builder->get();
		$result = $query->getResultArray();
		if (count($result) > 0) {
			$result = $result[0];
			$url = getenv('bigcommerceapp.STORE_URL').$result['store_hash'].'/v2/store';
			$client = \Config\Services::curlrequest();
			$response = $client->request('get', $url, [
					'headers' => [
							'X-Auth-Token' => $result['acess_token'],
							'store_hash' => $result['store_hash'],
							'Accept' => 'application/json',
							'Content-Type' => 'application/json'
					]
			]);
			if (strpos($response->getHeader('content-type'), 'application/json') != false){
				$res = $response->getBody();
				
				log_message('info', "RedirectBigcommerce - Store API Response : ".$res);
				
				if(!empty($res)){
					$res = json_decode($res,true);
					if(isset($res['secure_url'])){
						$builder = $db->table('order_details');        
						$builder->select('*');       
						$builder->where('email_id', $email_id);
						$builder->where('invoice_id', $invoice_id);
						$builder->where('token_validation_id', $validation_id);
						$query = $builder->get();
						$invoice_result = $query->getResultArray();
						if(isset($invoice_result[0])) {
							$invoice_result = $invoice_result[0];
							$order_id = $invoice_result['order_id'];
							$invoice_id = $invoice_result['invoice_id'];
							$bg_customer_id = $invoice_result['bg_customer_id'];
							
								log_message('info', "Redirecting to carsaver-order-confirmation.");
								
								$invoice_id = base64_encode(json_encode($invoice_id,true));
								$url = $res['secure_url'].'/rms-order-confirmation?authKey='.$invoice_id;
								echo '<script>window.parent.location.href="'.$url.'";</script>';
						}else{
							$url = $res['secure_url']."/checkout?rmsinv=".base64_encode(json_encode($invoice_id));
							echo '<script>window.parent.location.href="'.$url.'";</script>';
						}
					}
				}
			}
		}
	}
	
	/*
	* Check Payment Status whether it is failed or not
	*/
	public function getPaymentStatus(){
		
		$final_data = array();
		$final_data['status'] = false;
		$final_data['data'] = array();
		$final_data['msg'] = '';
		if(!empty($this->request->getPost('authKey'))){
			$invoiceId = json_decode(base64_decode($this->request->getPost('authKey')),true);
			if($invoiceId != ""){
				$db = \Config\Database::connect();
				$builder = $db->table('order_payment_details');        
				$builder->select('*');       
				$builder->where('order_id', $invoiceId);
				$query = $builder->get();
				$result_order_payment = $query->getResultArray();
				if (isset($result_order_payment[0])) {
					$result_order_payment = $result_order_payment[0];
					if($result_order_payment['status'] != "CONFIRMED"){
						$final_data['status'] = true;
						$final_data['msg'] = "There was an issue with your payment";
					}
				}
			}
		}
		echo json_encode($final_data,true);exit;
	}
}
