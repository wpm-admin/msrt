<?php
class ControllerCheckoutAuction extends Controller {
	private $error = array();


	public function check() {
		$this->load->model('checkout/auction');
		$this->load->model('catalog/product');
		$this->load->model('checkout/order');
		
		$from = $this->config->get('config_email');
		$store_name = 'Auction! '. $this->config->get('config_email');
		
		
		foreach($this->model_checkout_auction->checkEndAuction() AS $row){
			
			$historys = $this->model_catalog_product->getProductAuctionHistory($row['auction_id'], 'DESC');
			
			$this->model_checkout_auction->setAuctionDone($row['auction_id']);
			
			$count = 1;
			$emails = array();
			
			$data = array();
			
			$data['product_info'] = $this->model_catalog_product->getProduct($row['product_id']);
			$data['product_info']['href'] = $this->url->link('product/product', 'product_id=' . $row['product_id']);
			
			
			foreach($historys as $history){

				if($count++ == 1 AND (float)$row['reserv_price'] <= (float)$history['price_add']){ //Это последняя ставка - значит она сыграла
					
					$address = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$history['customer_id'] . "' LIMIT 1")->row;
					
					$country['name'] = '';
					if(isset($address['country_id'])){
						$country = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$address['country_id'] . "' LIMIT 1")->row;
					}
					
					$zone['name'] = '';
					if(isset($address['zone_id'])){
						$zone = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$address['zone_id'] . "' LIMIT 1")->row;
					}
					
					$data['invoice_prefix'] = 'auction';
					$data['store_id'] = 0;
					$data['store_name'] = $store_name;
					$data['store_url'] = $this->config->get('simple_domain');
					$data['customer_id'] = $history['customer_id'];
					$data['customer_group_id'] = $history['customer_group_id'];
					$data['shipping_firstname'] = $data['payment_firstname'] = $data['firstname'] = $history['firstname'];
					$data['shipping_lastname'] = $data['payment_lastname'] = $data['lastname'] = $history['lastname'];
					$data['email'] = $history['email'];
					$data['telephone'] = $history['telephone'];
					
					$data['shipping_address_1'] = $data['payment_address_1'] = $data['address_1'] = isset($address['address_1']) ? $address['address_1'] : '';
					$data['shipping_address_2'] = $data['payment_address_2'] = $data['address_2'] = isset($address['address_2']) ? $address['address_2'] : '';
					$data['shipping_city'] = $data['payment_city'] = $data['city'] = isset($address['city']) ? $address['city'] : '';
					$data['shipping_postcode'] = $data['payment_postcode'] = $data['postcode'] = isset($address['postcode']) ? $address['postcode'] : '';
					$data['shipping_country_id'] = $data['payment_country_id'] = $data['country_id'] = isset($address['country_id']) ? $address['country_id'] : '';
					$data['shipping_country'] = $data['payment_country'] = $data['country'] = $country['name'];
					$data['shipping_zone'] = $data['payment_zone'] = $data['zone'] = $zone['name'];
					$data['shipping_zone_id'] = $data['payment_zone_id'] = $data['zone_id'] = isset($address['zone_id']) ? $address['zone_id'] : '';
					$data['shipping_address_format'] = $data['payment_address_format'] = $data['address_format'] = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";
				
					
					$data['payment_custom_field'] = '[]';
					$data['payment_method'] = 'Cash On Delivery';
					$data['payment_code'] = 'cod';
					$data['shipping_method'] = 'Flat Shipping Rate';
					$data['shipping_code'] = 'flat.flat';
					$data['comment'] = 'Auction Winner';
					$data['total'] = $history['price_add'];
					$data['order_status_id'] = 0;
					$data['commission'] = $data['marketing_id'] = $data['affiliate_id'] = 0;
					$data['tracking'] = '';
					$data['language_id'] = 2;
					$data['currency_id'] = 2;
					$data['currency_value'] = 1;
					$data['currency_code'] = 'USD';
					$data['ip'] = '';
					$data['forwarded_ip'] = '';
					$data['user_agent'] = 'Auction crone';
					$data['accept_language'] = 'en-us';
					
					
					$data['products'] = array();
					$data['products'][1] = $data['product_info'];
					$data['products'][1]['quantity'] = 1;
					$data['products'][1]['price'] = $history['price_add'];
					$data['products'][1]['total'] = $history['price_add'];
					$data['products'][1]['tax'] = 0;
					$data['products'][1]['reward'] = 0;
					
					$data['totals'] = array();
					$data['totals'][] = array(
											  'code'		=> 'sub_total',
											  'title'		=> 'Sub-Total',
											  'value'		=> $history['price_add'],
											  'sort_order'	=> '1'
											  );
					
					$data['totals'][] = array(
											  'code'		=> 'shipping',
											  'title'		=> 'Flat Shipping Rate',
											  'value'		=> 0,
											  'sort_order'	=> '3'
											  );
					
					$data['totals'][] = array(
											  'code'		=> 'total',
											  'title'		=> 'Total',
											  'value'		=> $history['price_add'],
											  'sort_order'	=> '9'
											  );
					
					$json['order_id'] = $this->model_checkout_order->addOrder($data);
					$this->model_checkout_order->addOrderHistory($json['order_id'], 25, '', 0, 0);
					
					$subj = 'WIN';
					$html = false;//$this->load->view('mail/auction_false', $data);
					echo '<br>Win';
					
				}else{
					echo '<br>false';
					$subj = 'Your bet has not played';
					$html = $this->load->view('mail/auction_false', $data);
				
				}
				
				//Если емайлы еще не отправляли
				if(!in_array($history['email'], $emails) AND $html){
					
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			
					$mail->setTo($history['email']);
					$mail->setFrom($from);
					$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode($subj, ENT_QUOTES, 'UTF-8'));
					$mail->setHtml($html);
					$mail->send();
					
				}
				
				$emails[] = $history['email'];
				
			}
		}
	}
}
