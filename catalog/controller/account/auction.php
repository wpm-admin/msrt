<?php
class ControllerAccountAuction extends Controller {
	public function index() {
		
		if (isset($this->request->get['remove'])) {
			unset($_COOKIE['productsWish'][(int)$this->request->get['remove']]);
		}
		
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/auction', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/auction');

		$this->load->model('account/auction');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			// Remove Auction
			$this->model_account_auction->deleteAuction($this->request->get['remove']);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/auction'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/auction')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();

		$results = $this->model_account_auction->getCustomerAuctionProduct();

		
		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);

			if ($product_info) {
				
				$history = $this->model_account_auction->getAuctionHistorys($result['auction_id']);
				
				if(!$history){
					$product_info['price'] = $result['price_start'];
				}else{
					$h_row = array_shift($history);
					$product_info['price'] = $h_row['price_add'];
				}
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'), 'product_in_wish_list');
				} else {
					$image = false;
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}
			
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'special_date_end'      => $result['date_end'],
					'stock'      => $stock,
					'price'      => $price,
 					'sku'        => $product_info['sku'],				
 					'stock_status_id'        => $product_info['stock_status_id'],				
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/auction', 'remove=' . $product_info['product_id'])
				);
			} else {
				$this->model_account_auction->deleteAuction($result['product_id']);
			}
		}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/auction', $data));
	}

	public function add() {
		$this->load->language('account/auction');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if ($this->customer->isLogged()) {
				// Edit customers cart
				$this->load->model('account/auction');

				$this->model_account_auction->addAuction($this->request->post['product_id']);

				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/auction'));

				$json['total'] = sprintf($this->language->get('text_auction'), $this->model_account_auction->getTotalAuction());
			} else {
				if (!isset($this->session->data['auction'])) {
					$this->session->data['auction'] = array();
				}

				$this->session->data['auction'][] = $this->request->post['product_id'];

				$this->session->data['auction'] = array_unique($this->session->data['auction']);

				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/auction'));

				$json['total'] = sprintf($this->language->get('text_auction'), (isset($this->session->data['auction']) ? count($this->session->data['auction']) : 0));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function mail(){
		
		$this->load->language('account/auction');

		$this->load->language('extension/theme/technics'); // technics
            
		$this->load->model('account/auction');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		
		
		$results = $this->model_account_auction->getAllAuction();

		$sort_result = array();
		
		foreach($results as $result){
			
			if(!isset($sort_result[$result['customer_id']])){
				$sort_result[$result['customer_id']] = $result;
			}
			
			$sort_result[$result['customer_id']]['products'][] = $result;
			
		}
		
		unset($results);
		
		foreach($sort_result as $customer){
		
			$sbj = $this->language->get('sbj_auction_'.$customer['language_id']);
			$msg = '<div style="font-size:18px;">'.sprintf($this->language->get('mail_auction_'.$customer['language_id']), $customer['firstname'].' '.$customer['lastname']).'</div><br>';
		
			foreach($customer['products'] as $product){
				
				//Получим товар если его еще не получили
				if(!isset($product_info[$product['product_id']])){
					$result = $this->model_catalog_product->getProduct($product['product_id']);
				
					
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'), 'product_list');
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
	
					if (!is_null($result['special']) && (float)$result['special'] >= 0) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}
					
					$product_info[$result['product_id']] = $result;
					$product_info[$result['product_id']]['image'] = $image;
					$product_info[$result['product_id']]['price'] = $price;
					$product_info[$result['product_id']]['special'] = $special;
					$product_info[$result['product_id']]['stock_status'] = $result['stock_status'];
					$product_info[$result['product_id']]['href'] = $this->url->link('product/product','product_id=' . $result['product_id'] );
					
				}
				
				//Добавим в сообщение
				$msg .= '<div style="width: 31%;margin: 10px 10px 30px;float: left;">';
				$msg .= '<br><img style="width:200px;" src="'.$product_info[$product['product_id']]['image'].'"><br>';
				$msg .= '<h2><a href="'.$product_info[$product['product_id']]['href'].'">'.$product_info[$product['product_id']]['name'].'</a></h4><br><br>';
				
				if(!$special){
					$msg .= '<span style="font-size: 16px;">Price: <span style="font-size: 20px;font-weight: bold;">'.$product_info[$product['product_id']]['price'].'</span></span><br>';
				}else{
					$msg .= '<span style="font-size: 16px;">NEW Price: <span style="font-size: 20px;font-weight: bold;">'.$product_info[$product['product_id']]['special'].'</span> <span style="font-size: 16px;text-decoration:line-through;">'.$product_info[$product['product_id']]['price'].'<span></span><br>';
				}
				$msg .= '<div style="font-size: 16px;">Stock status: <span  style="font-size: 20px;font-weight: bold;">'.$product_info[$product['product_id']]['stock_status'].'</span></div>';
				$msg .= '</div>';
			}
			
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));			
			$mail->setSubject(html_entity_decode($sbj, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($msg);
			$mail->send();

			
			
		}
		
		
		echo 'done';
	}
}
