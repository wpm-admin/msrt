<?php
class ControllerExtensionModuleAuction extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/auction');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$results = $this->model_catalog_product->getAuctionProducts($setting['limit']);

		$data['products'] = array();

		if(isset($this->request->get['mark_id']) AND $this->request->get['mark_id']){
			$setting['width'] = 1200;
			$setting['height'] = 800;
		//echo "<pre>";print_r(var_dump($this->request->get['mark_id']));echo "</pre>";
		//die();
		}
		
		$data['home_page'] = false;
		if(empty($this->request->get)){
			$data['home_page'] = true;
		}
		
		$data['home_url'] = $_SERVER['REQUEST_URI'];
		
		if($data['home_url'] == '/'){
			unset($this->session->data['mark_id']);
		}
		
		$data['href'] = $this->url->link('catalog/category', 'path=234');
		
		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					//$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					$image = $this->model_tool_image->resize($result['image'], 373, 226);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				/*
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
				*/
				$result['auction']['price_start_cur'] = $this->currency->format($this->tax->calculate($result['auction']['price_start'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$result['auction']['price_end_cur'] = $this->currency->format($this->tax->calculate($result['auction']['price_end'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$result['auction']['step_cur'] = $this->currency->format($this->tax->calculate($result['auction']['step'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				
				
				$auction_history = $this->model_catalog_product->getProductAuctionHistory($result['auction']['auction_id']);
				$result['auction']['last_bet'] = $result['auction']['price_start'];
				$result['auction']['z_last_bet'] = $result['auction']['price_start'];
				foreach($auction_history as $index => $row){
					$result['auction']['last_bet'] = $row['price_add'] + $result['auction']['price_step'];
					$result['auction']['z_last_bet'] = $row['price_add'];
				}
				$result['auction']['last_bet_cur'] = $this->currency->format($this->tax->calculate($result['auction']['last_bet'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$price = $this->currency->format($this->tax->calculate($result['auction']['z_last_bet'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
	
				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'auction'        => $result['auction'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
				
			}

			return $this->load->view('extension/module/auction', $data);
		}
	}
}
