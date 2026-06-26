<?php
class ControllerProductProduct extends Controller {
	private $error = array();


	public function breadList_mark($category_id) {
		$this->load->model('catalog/mark');
		$data = array();
		$categories = $this->model_catalog_mark->getMarks($category_id);
		foreach($categories as $category){
			$data[] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/mark', 'mark_id=' . $category['mark_id'])
			);
		}
		return $data;
	}
	public function breadlistcr_mark() {

		$this->load->model('catalog/mark');
		$category_id = $this->request->get['cat_id'];
		$data['breadLists'] = array();
		$categories = $this->model_catalog_mark->getMarks($category_id);
		foreach($categories as $category){
			$data['breadLists'][] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/mark', 'mark_id=' . $category['mark_id'])
			);
		}
		$this->response->setOutput($this->load->view('product/bread_popup',$data));
	}
	

	
	public function index() {
		$this->load->language('product/product');

		
		$data['href'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);

		$this->document->setHref($data['href']);
		
		/* Это ненадо - оно есть в сео_про
		if(str_replace('http://', 'https://', $_SERVER['SCRIPT_URI']) != $data['href']){
			
			die('222');
			
			header("HTTP/1.1 301 Moved Permanently"); 
			header("Location: " . $data['href']); 
			exit(); 
		}
		*/
		if(isset($this->session->data['show_as_diagram']) AND $this->session->data['show_as_diagram'] == 1){
			$data['show_as_diagram'] = true;
		}else{
			$data['show_as_diagram'] = false;
		}
			
			
		$data['breadcrumbs'] = array();
		$data['view_last'] = true;

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/mark');
		$this->load->model('catalog/information');
		
		
		if(isset($this->session->data['mark_id'])){
			$data['mark_id'] = $this->session->data['mark_id'];
			$mark_info = $this->model_catalog_mark->getMark($data['mark_id']);

			if ($mark_info) {
				$data['breadcrumbs'][] = array(
					'text' => $mark_info['name'],
					'breadList' => $this->breadList_mark(0),// technics
					'cat_id' => $data['mark_id'],// technics
					'href' => $this->url->link('product/mark', 'mark_id=' . $data['mark_id'] )
				);
			}
		}
		
		if(isset($this->session->data['model_id'])){
			$mark_id = $data['model_id'] = $this->session->data['model_id'];
			$mark_info = $this->model_catalog_mark->getMark($data['model_id']);

			if ($mark_info) {
				$data['breadcrumbs'][] = array(
					'text' => $mark_info['name'],
					'breadList' => $this->breadList_mark($data['mark_id']),// technics
					'cat_id' => $data['mark_id'],// technics
					'href' => $this->url->link('product/mark', 'mark_id=' . $data['model_id'] )
				);
			}

		}
	
		
		$data['DIR_APPLICATION'] = DIR_APPLICATION;
		
		//Отключаем категории
		unset($this->request->get['path']);
		
		$this->load->model('catalog/category');
		if (isset($this->request->get['path'])) {
			$path = '';

			$parts2 = $parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);
			
			$parts = $parts2;
			
			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$url = '';

					if (isset($this->request->get['sort'])) {
						$url .= '&sort=' . $this->request->get['sort'];
					}
	
					if (isset($this->request->get['order'])) {
						$url .= '&order=' . $this->request->get['order'];
					}
	
					if (isset($this->request->get['page'])) {
						$url .= '&page=' . $this->request->get['page'];
					}
	
					if (isset($this->request->get['limit'])) {
						$url .= '&limit=' . $this->request->get['limit'];
					}
	
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'breadList' => $this->breadList(0),// technics
						'cat_id' => 0,// technics
						'href' => $this->url->link('product/category', 'path=' . $path_id . $url)
					);
				}
			}

		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		//check product page open from cateory page
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
						
			if(empty($this->model_catalog_product->checkProductCategory($product_id, $parts))) {
				$product_info = array();
			}
		}

		//check product page open from manufacturer page
		if (isset($this->request->get['manufacturer_id']) && !empty($product_info)) {
			if($product_info['manufacturer_id'] !=  $this->request->get['manufacturer_id']) {
				$product_info = array();
			}
		}


		$data['club_href'] =  $this->url->link('product/category', 'path=' . CLUB_CATEGORY_ID);

		if ($product_info) {
			
			// Check if product has active auction
			$this->load->model('catalog/product');
			$auction_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction WHERE product_id = '" . (int)$product_id . "' AND status = '1' AND date_start <= NOW() AND date_end >= NOW() LIMIT 1");
			if ($auction_query->num_rows > 0) {
				$product_info['auction'] = $auction_query->row;
			}
			
			//Вычислим стоимость по весу товара и ИП клиента
			$this->load->model('extension/shipping/flat1');
			$shipping_cost = $this->model_extension_shipping_flat1->getQuoteOnProduct($product_info['product_id']);
			$data['text_shipping_cost'] = $shipping_cost['quote']['flat1']['title'] . ' - ' . $shipping_cost['quote']['flat1']['text'];
			
			$this->load->model('account/wishlist');
			$data['in_wishlist'] = $this->model_account_wishlist->isWishlist($product_info['product_id']);
			
			
			$data['in_cart'] = $this->cart->inCart($product_info['product_id']);
			
				
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			/*
			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);
			*/
		
			$mark = $this->model_catalog_product->getProductMark($product_info['product_id']);
			
			if(isset($mark['parent_id'])){
				$parent_mark = $this->model_catalog_mark->getMark($mark['parent_id']);
			}
		
			if(isset($parent_mark) AND isset($parent_mark['name'])){
				$product_info['meta_title'] = str_replace('[name_mark]', $parent_mark['name'], $this->language->get('meta_title'));
				$product_info['meta_description'] = str_replace('[name_mark]', $parent_mark['name'], $this->language->get('meta_description'));	
			}
			
			if(isset($mark) AND isset($mark['name'])){
				$product_info['meta_title'] = str_replace('[name_model]', $mark['name'], $product_info['meta_title']);	
				$product_info['meta_description'] = str_replace('[name_model]', $mark['name'], $product_info['meta_description']);	
			}
			
			
			$product_info['meta_title'] = str_replace('[name_product]', $product_info['name'], $product_info['meta_title']);
			$product_info['meta_title'] = str_replace('[car_brand]', $product_info['manufacturer'], $product_info['meta_title']);
			
			$product_info['meta_description'] = str_replace('[name_product]', $product_info['name'], $product_info['meta_description']);
			$product_info['meta_description'] = str_replace('[car_brand]', $product_info['manufacturer'], $product_info['meta_description']);
		

	
			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			
			if(trim($product_info['modeli']) != ''){
				$data['heading_title'] = $product_info['mark'] . ' / ' . $product_info['modeli'] . ' / ' . $product_info['name'];
			}else{
				$data['heading_title'] = $product_info['name'];
			}
			
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['sku'] = $product_info['sku'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}
			
			$data['stock_status_id'] = $product_info['stock_status_id'];
		
			
			$data['stock_statuses'] = $this->model_catalog_product->getProductStatuses();
			$data['condition_statuses'] = $this->model_catalog_product->getProductConditions();
		
			$data['condition'] = isset($data['condition_statuses'][$product_info['condition_status_id']]) ? $data['condition_statuses'][$product_info['condition_status_id']]['name'] : '';
			$data['stock_status'] = isset($data['stock_statuses'][$product_info['stock_status_id']]) ? $data['stock_statuses'][$product_info['stock_status_id']]['name'] : '';

			$data['uom'] = $product_info['uom'];
			
			$this->load->model('tool/image');
			
			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup');
			} else {
				$data['popup'] = '';
			} 

			if ($product_info['image']) {
				
				$this->document->setImage($this->model_tool_image->resize($product_info['image'], 214, 214, 'product_thumb'));
				
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'), 'product_thumb');
				$data['thumb_coordiname'] = $this->model_tool_image->resize($product_info['image'], 1000, 800);
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup'),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'), 'product_additional'),
					'thumb2' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'), 'product_additional')
				);
			}
	
			if($product_info['price'] <= 0){
				if(!$product_info['diagram']){
					$data['price'] = '-';
					$data['button_cart'] = 'inquire';
				}
			}elseif ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$this->document->setPrice($product_info['price']);
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}
			
			if ($product_info['special'] && (float)$product_info['special'] > 0) {
				$this->document->setPrice($product_info['special']);
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$tax_price = (float)$product_info['special'];
			} else {
				$data['special'] = false;
				$tax_price = (float)$product_info['price'];
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format($tax_price, $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			
			        $data['neat_countdown'] = $this->load->controller('extension/module/neat_countdown');

			
			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			if(!isset($mark_id)) $mark_id = 0;
			
			$product_list = $this->model_catalog_product->getNeiboProducts($product_id, 0, $mark_id, (int)$product_info['diagram']);
			
			$data['product_prev'] = false;
			$data['product_next'] = false;
			$data['product_shema_list'] = array();
			
			foreach($product_list as $index => $list){
				if((int)$list['product_id'] == (int)$product_id){
					
					if(isset($product_list[$index - 1])){
						$data['product_prev'] = $this->url->link('product/product', 'product_id=' . $product_list[$index - 1]['product_id']. $url);
					}

					if(isset($product_list[$index + 1])){
						$data['product_next'] =  $this->url->link('product/product', 'product_id=' . $product_list[$index + 1]['product_id']. $url);
					}
					
				}
				
				if ($list['image']) {
					$image = $this->model_tool_image->resize($list['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'), 'product_related');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				$data['product_shema_list'][] = array(
					'product_id'  => $list['product_id'],
					'thumb'       => $image,
					'name'        => $list['name'],
					'href'        => $this->url->link('product/product', 'product_id=' . $list['product_id']),
					'active'      => ($list['product_id'] == $product_id) ? true : false,
				);
				
			}
			
			
			
			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'), 'product_related');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if($result['price'] <= 0){
					$price = 'inquire';
				}elseif ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

		 
				if (!is_null($result['special']) && (float)$result['special'] > 0) {
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
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'sku'        => $result['sku'],
					'stock_status_id'        => $result['stock_status_id'],				
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			
			$data['coordinate_products'] = array();

			$results = $this->model_catalog_product->getProductCoordinate($this->request->get['product_id']);


			
			 
			$manufacturers = array(); 
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 180,180, 'product_related');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 40, 40);
				}

				if($result['price'] <= 0){
					$price = '-';
				}elseif ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

		 
				if ($result['special'] && !is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}				
	
				$tmp = explode('|',$result['coordiname']);
				
				$tmp = explode(':', $tmp[0]);
				
				$x = $tmp[0];
				$y = isset($tmp[1]) ? $tmp[1] : 0;
				
				$manufacturers[$result['manufacturer_id']] = array();
				
				$data['coordinate_products'][] = array(
					'product_id'  => $result['product_id'],
					'model'  => $result['model'],
					'sku'  => $result['sku'],
					'manufacturer_id'  => (int)$result['manufacturer_id'],
					'thumb'       => $image,
					'x'       => $x,
					'y'       => $y,
					'name'        => str_replace("'", "\'", str_replace("\n", '<br>',$result['name'])),
					'sku'        => str_replace("\n", '<br>', $result['sku']),
					'stock_status_id'   => $result['stock_status_id'],
					'condition_status_id'   => $result['condition_status_id'],
					'condition'   => (isset($data['condition_statuses'][$result['condition_status_id']]['name']) ? $data['condition_statuses'][$result['condition_status_id']]['name'] : ''),
					'num'        => $result['num'],
					'status'        => $result['status'],
					'price'       => $price,
					'special'     => $special,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
				
				$this->document->setPageType('diagram');
			}

			
			$data['diagram_list'] = array();
			
			if(!$product_info['diagram']){
				
				//Это обычный товар не схема
				$this->document->setPageType('product');
			
				$diagram_list = $this->model_catalog_product->getProductDiagrams($product_id);
				
				foreach($diagram_list as $d_list){
			
					if(!$d_list) continue;
			
					$mark = $this->model_catalog_product->getProductMark($d_list['product_id']);

					
					if(isset($mark['parent_id'])){
						$parent_mark = $this->model_catalog_mark->getMark($mark['parent_id']);
					}else{
						$parent_mark = array();
						$parent_mark['name'] = '';
						$parent_mark['image'] = '';
					}
					
					$categ = $this->model_catalog_product->getCategories($d_list['product_id']);
					$categ = array_pop($categ);
					$categ = $this->model_catalog_category->getCategory($categ['category_id']);
				
				
					if ($d_list['image']) {
						$image = $this->model_tool_image->resize($d_list['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}

					if ($mark AND $mark['image']) {
						$mark_image = $this->model_tool_image->resize($mark['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$mark_image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}

					if ($parent_mark AND $parent_mark['image']) {
						$parent_mark_image = $this->model_tool_image->resize($parent_mark['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$parent_mark_image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}

					if ($categ AND $categ['image']) {
						$categ_image = $this->model_tool_image->resize($categ['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					} else {
						$categ_image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
					}

					$data['diagram_list'][] = array(
						'product_id'  => $d_list['product_id'],
						'thumb'       => $image,
						'parent_mark_thumb'       => $parent_mark_image,
						'mark_thumb'       => $mark_image,
						'categ_thumb'       => $categ_image,
						'name'        => $d_list['name'],
						'parent_mark_name'        => $parent_mark['name'],
						'mark_name'        => isset($mark['name']) ? $mark['name'] : false,
						'categ_name'        => isset($categ['name']) ? $categ['name'] : false,
						'href'        => $this->url->link('product/product', 'product_id=' . $d_list['product_id']),
						'parent_mark_href'        => isset($parent_mark['mark_id']) ? $this->url->link('product/mark', 'mark_id=' . $parent_mark['mark_id']) : false,
						'mark_href'        => isset($mark['mark_id']) ? $this->url->link('product/mark', 'mark_id=' . $mark['mark_id']) : false,
						'categ_href'        => isset($categ['category_id']) ? $this->url->link('product/category', 'path=' . $categ['category_id']) : false,
					);
				}
			}
			
			foreach($manufacturers as $manuf_id => $row){
				$manufacturers[$manuf_id] = $this->model_catalog_manufacturer->getManufacturer($manuf_id);
			}
			$manufacturers[0]['name'] = '- - -';
			$manufacturers[0]['manufacturer_id'] = 0;
			
			$data['manufacturer_list'] = $manufacturers;
			
			
			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);
	
			
			
			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			
			
			$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
			
			if(count($data['coordinate_products']) > 0){
				$this->response->setOutput($this->load->view('product/product_shema', $data));	
			}else{
				$this->response->setOutput($this->load->view('product/product', $data));	
			}
			
			
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if (isset($this->request->get['product_id']) && $this->request->get['product_id']) {
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
					$json['error'] = $this->language->get('error_name');
				}

				if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
					$json['error'] = $this->language->get('error_text');
				}
			
				if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
					$json['error'] = $this->language->get('error_rating');
				}

				// Captcha
				if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
					$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

					if ($captcha) {
						$json['error'] = $captcha;
					}
				}

				if (!isset($json['error'])) {
					$this->load->model('catalog/review');

					$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

					$json['success'] = $this->language->get('text_success');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_product');
		} 

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription(){
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
