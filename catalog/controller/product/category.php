<?php
class ControllerProductCategory extends Controller {
	
	// Прокси-метод для OCmod-модификаций на сервере, которые в исходнике вызывают $this->breadList().
	// Если OCmod отвалится — будет работать через breadList_mark. Не удалять.
	public function breadList($category_id) {
		return $this->breadList_mark($category_id);
	}
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
	
	public function breadList_consigment($category_id, $active_id, $mark_id = false) {
		$this->load->model('catalog/category');
		$data = array();
		$categories = $this->model_catalog_category->getCategories($category_id);
		
		$data[] = array(
				'name'		=> '-all-',
				'active' => ((int)$active_id == 0) ? true : false,
				'href'       => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID)
			);
		
		foreach($categories as $category){
			
			$data[] = array(
				'name'		=> $category['name'],
				'active' => ($category['category_id'] == $active_id) ? true : false,
				'href'       => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID.'_'.$category['category_id'] . '&mark_id=' . (int)$mark_id)
			);
		}
		return $data;
	}
	
	public function breadList_mark_consigment($category_id, $active_id, $adds = '') {
		$this->load->model('catalog/mark');
		$data = array();
		$categories = $this->model_catalog_mark->getMarks($category_id);
		
		$data[] = array(
				'name'		=> '-all-',
				'active' => ((int)$active_id == 0) ? true : false,
				'href'       => $this->url->link('product/category', $adds)
			);		
		
		foreach($categories as $category){
			$data[] = array(
				'name'		=> $category['name'],
				'active' => ($category['mark_id'] == $active_id) ? true : false,
				'href'       => $this->url->link('product/category', $adds . 'mark_id=' . $category['mark_id'])
			);
		}
	
		return $data;
	}
	
	public function index() {
		if(isset($this->session->data['show_as_diagram']) AND $this->session->data['show_as_diagram'] == 1){
			$data['show_as_diagram'] = true;
		}else{ //if($query->row['image2']){
			$data['show_as_diagram'] = false;
		}		
		
		$data['club_href'] =  $this->url->link('product/category', 'path=' . CLUB_CATEGORY_ID);
		
		
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');
		$this->load->model('catalog/mark');

		$this->load->model('tool/image');

		$data['mark_id'] = $data['model_id'] = false;
		
		
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}


		
		if(isset($this->session->data['mark_id'])){
			$filter_mark_id = $this->session->data['mark_id'];
		}else{
			$filter_mark_id = false;
		}

	
		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['mark_href'] = '';
		$data['model_href'] = '';
		
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
			
			$data['mark_href'] = $this->url->link('product/mark', 'mark_id=' . $data['mark_id'] );
		}
		

		
		if(isset($this->session->data['model_id'])){
			$data['model_id'] = $this->session->data['model_id'];
			$mark_info = $this->model_catalog_mark->getMark($data['model_id']);

			if ($mark_info) {
				$data['breadcrumbs'][] = array(
					'text' => $mark_info['name'],
					'breadList' => $this->breadList_mark($data['mark_id']),// technics
					'cat_id' => $data['mark_id'],// technics
					'href' => $this->url->link('product/mark', 'mark_id=' . $data['model_id'] )
				);
			}
			
			$data['model_href'] = $this->url->link('product/mark', 'mark_id=' . $data['mark_id'] );

		}

	
		
		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $category_id . $url)
				);
			}
			
			
			
			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'breadList' => $this->breadList($path_id),// technics
						'cat_id' => $path_id,// technics
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		$data['parent_id'] = $parent_id = $category_info['parent_id'];
	
		$data['view_last'] = true;
		
			
		if ($category_info) {
			
						
			if((int)$category_info['is_diagram']){
				$filter_is_diagram = true;
			}else{
				$filter_is_diagram = false;
			}
		 

			//Добавляем в название категории Модель и Марку
			$preff = '';
			if(isset($mark_info)){
			   if((int)$mark_info['parent_id'] > 0){
				   $parent_mark_info = $this->model_catalog_mark->getMark($mark_info['parent_id']);
				   $preff = $parent_mark_info['name'].' ' . $mark_info['name'] . ', ';
			   }else{
				   $preff = $mark_info['name'] . ', ';
			   }
			}
		 
			$this->document->setTitle($preff.$category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['heading_title'] = $preff.$category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			/*
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);
			*/

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'), 'category_image');
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			
			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				
				if((int)$result['category_id'] == 234) continue; //Аукцион
				
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true,
					'filter_model'	       => true,
				);
				if ($filter_is_diagram) {
					$filter_data['filter_diagram'] = true;
				}

				if($this->model_catalog_product->getTotalProducts($filter_data)){
					$data['categories'][] = array(
						'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
					);
				}
			}

			$data['products'] = array();

			$filter_category_id = $category_id;
			if($category_id == 229){
				$category_id = 0;
				$limit = 200;
			}
			if((int)$category_id == 232){
				$limit = 1000;
			}			

			
			$filter_data = array(
				'filter_category_id'  => $category_id,
				'filter_filter'       => $filter,
				'filter_sub_category' => true,
				'filter_model'	       => true,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);
			if ($filter_is_diagram) {
				$filter_data['filter_diagram'] = true;
			}
			$category_id = $filter_category_id;
			
			
			
			global $club_categ;
			$category_store = $club_categ;
			if(in_array((int)$category_id, $category_store) OR in_array((int)$parent_id, $category_store)){
				unset($filter_data['filter_diagram']);
			}
			
			$this_consigment = false;
			
			if($category_id == 234){
				$results = $this->model_catalog_product->getAuctionProducts(1000);
				$product_total = count($results);
				
				$this->load->model('catalog/mark');
				
				$seo = new ControllerStartupSeoUrl($this->registry);
				
				$data['all_brands_href'] = $seo->cleanLink('product/category', 'path=234');
				$data['text_all_brands'] = 'All Brands';
				$data['selected_mark_id'] = isset($this->request->get['mark_id']) ? (int)$this->request->get['mark_id'] : 0;
				$data['selected_model_id'] = isset($this->request->get['model_id']) ? (int)$this->request->get['model_id'] : 0;
				
				$data['marks'] = array();
				$marks_raw = $this->model_catalog_mark->getMarks(0);
				foreach ($marks_raw as $mark) {
					$data['marks'][] = array(
						'mark_id' => $mark['mark_id'],
						'name' => $mark['name'],
						'href' => $seo->cleanLink('product/category', 'path=234&mark_id=' . $mark['mark_id'])
					);
				}

				//Clean heading — brand из URL, без сессии/модели
				$heading_preff = '';
				if(isset($this->request->get['mark_id'])){
					$mark = $this->model_catalog_mark->getMark((int)$this->request->get['mark_id']);
					if($mark && (int)$mark['parent_id'] == 0){
						$heading_preff = $mark['name'] . ' — ';
					}
				}
				$data['heading_title'] = $heading_preff . $category_info['name'];

			}elseif($category_id == CONSIGNMENT_CATEGORY_ID OR $parent_id == CONSIGNMENT_CATEGORY_ID){
				
				//Если выбрано несколько категорий то обьединяем запросы по ним
				if(isset($this->request->get['filter_categories']) AND count($this->request->get['filter_categories']) > 1){
					
					$data['filter_categories'] = $this->request->get['filter_categories'];
						
					$product_total = $this->model_catalog_product->getTotalConsigments($this->request->get['filter_categories'], true);
					$results = $this->model_catalog_product->getConsigments($this->request->get['filter_categories'], $page, true);
				
				}else{
					$product_total = $this->model_catalog_product->getTotalConsigments($category_id);
					$results = $this->model_catalog_product->getConsigments($category_id, $page);
				}
				
				$data['category_id'] = $category_id;
				
				$this_consigment = true;
				
				$data['consignment_href'] = $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID );
				
			}else{
				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
				$results = $this->model_catalog_product->getProducts($filter_data);
			}
		
			$this->load->model('account/wishlist');
		
			//Если в категории всего один продукт - переходим сразу в него (Если только это не обьявление)
			if(count($results) === 1 && !$this_consigment && !isset($this->request->get['mark_id']) && $category_id != 234){
				$link = $this->url->link('product/product', 'product_id=' . $results[0]['product_id'] . $url);
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: " . $link);
				exit();
			}
		

		
			foreach ($results as $result) {
	
				$result['in_wishlist'] = $this->model_account_wishlist->isWishlist($result['product_id']);
	
				//Это для случаев когда картинки хранятся вместе. Например в обьявлениях
				//Такие катринки уже имею полный путь
				if (isset($result['images_normal']) AND !isset($result['image'])) {
					$tmp = explode(';', $result['images_normal']);
					$result['image'] = array_shift($tmp);
				}
			
				if ($result['image'] AND $this_consigment) {
					$image = $this->model_tool_image->resize($result['image'], 370, 184);
				}elseif ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'), 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
			
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
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

				if(isset($result['auction'])){
					
					$result['auction']['detail'] = array();
					
					$auction_history = $this->model_catalog_product->getProductAuctionHistory($result['auction']['auction_id']);
					$result['auction']['price_start_cur'] = $this->currency->format($this->tax->calculate($result['auction']['price_start'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$result['auction']['price_end_cur'] = $this->currency->format($this->tax->calculate($result['auction']['price_end'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$result['auction']['price_step_cur'] = $this->currency->format($this->tax->calculate($result['auction']['price_step'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					
					
					$result['auction']['last_bet'] = $result['auction']['price_start'];
					$result['auction']['z_last_bet'] = $result['auction']['price_start'];
					foreach($auction_history as $index => $row){
						$result['auction']['last_bet'] = $row['price_add'] + $result['auction']['price_step'];
						$result['auction']['z_last_bet'] = $row['price_add'];
					}
					$result['auction']['last_bet_cur'] = $this->currency->format($this->tax->calculate($result['auction']['last_bet'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$result['auction']['z_last_bet_cur'] = $this->currency->format($this->tax->calculate($result['auction']['z_last_bet'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					
					$result['auction']['timer_y'] = date('Y', strtotime($result['auction']['date_end']));
					$result['auction']['timer_m'] = date('m', strtotime($result['auction']['date_end']));
					$result['auction']['timer_d'] = date('d', strtotime($result['auction']['date_end']));
					$result['auction']['timer_h'] = date('H', strtotime($result['auction']['date_end']));
					$result['auction']['timer_i'] = date('i', strtotime($result['auction']['date_end']));
					$result['auction']['timer_s'] = date('s', strtotime($result['auction']['date_end']));
					
					$end = strtotime($result['auction']['date_end']);
					$now = strtotime(date('Y-m-d H:i:s'));
					$result['auction']['days_to_end'] = ceil(($end-$now)/(60*60*24));
				}
				
				
				
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'category_id'        => (isset($result['category_id'])) ? (int)$result['category_id'] : false,
					'mark_id'        => (isset($result['mark_id'])) ? (int)$result['mark_id'] : false,

					'stock_status_id' => $result['stock_status_id'],
					'stock_status'    => $result['stock_status'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'sku'        => $result['sku'],				
					'in_wishlist'        => $result['in_wishlist'],				
					'year_manuf'        => $result['year_manuf'],				
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'auction'     => isset($result['auction']) ? $result['auction'] : array(),
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url),
					'consignment_href'        => (isset($result['consignment_id'])) ? $this->url->link('product/consignment', 'consignment_id=' . $result['consignment_id'] . $url) : false,
				);

			}
			
			$url = '';

			
			$data['product_shema_list'] = array();
			
			if($category_id != 223){
				$product_list = $this->model_catalog_product->getNeiboProducts(0, $category_id, $data['model_id'], true);
				
				foreach($product_list as $index => $list){
			
					$data['product_shema_list'][] = array(
						'product_id'  => $list['product_id'],
						'thumb'       => false, //$image,
						'name'        => $list['name'],
						'href'        => $this->url->link('product/product', 'product_id=' . $list['product_id']),
						'active'      => false, //($list['product_id'] == $product_id) ? true : false,
					);
				}
			}
			
			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();
			
			$statuses = $this->model_catalog_product->getProductStatuses();
			
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $statuses[5]['name'], //$this->language->get('stock_status_id=5'),
				'value' => 'p.stock_status_id=5',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.stock_status_id=5&order=ASC' . $url)
			);
			$data['sorts'][] = array(
				'text'  => $statuses[6]['name'], //$this->language->get('stock_status_id=6'),
				'value' => 'p.stock_status_id=6',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.stock_status_id=6&order=ASC' . $url)
			);
			$data['sorts'][] = array(
				'text'  => $statuses[7]['name'], //$this->language->get('stock_status_id=7'),
				'value' => 'p.stock_status_id=7',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.stock_status_id=7&order=ASC' . $url)
			);
	
			$data['sorts'][] = array(
				'text'  => $statuses[9]['name'], //$this->language->get('stock_status_id=9'),
				'value' => 'p.stock_status_id=9',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.stock_status_id=9&order=ASC' . $url)
			);
			/*
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);
			*/
			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 40, 60));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			
			
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			if($category_id == 234){
				$data['productslist'] = $this->load->view('product/product_auction_list', $data);				
			}elseif($category_id == CONSIGNMENT_CATEGORY_ID OR $parent_id == CONSIGNMENT_CATEGORY_ID){
				
				$data['categories'] = array();

				$categories = $this->model_catalog_category->getCategories(CONSIGNMENT_CATEGORY_ID);

				foreach ($categories as $category) {
					
					$tmp_href = $this->url->link('product/category', 'path=' . $category['category_id'] . '&mark_id=0');
            
					$data['categories'][$category['category_id']] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'],
						'thumb'    =>  $this->model_tool_image->resize($category['image'], 270,272),
						'short_href'        => str_replace($data['mark_href'], '',str_replace($data['model_href'], '',$tmp_href)),
						'href'        => $tmp_href,
						
					);
				}				
				
				$this->load->model('catalog/mark');
				
				$data['marks'] = array();
				$data['models'] = array();
				
				$categories = $this->model_catalog_mark->getMarks(0);
				foreach($categories as $category){
					$data['marks'][$category['mark_id']] = array(
						'mark_id'		=> $category['mark_id'],
						'name'		=> $category['name'],
						'href'       => $this->url->link('product/mark', 'mark_id=' . $category['mark_id'])
					);
					
					//Если у нас есть выбранная марка
					if(isset($this->session->data['mark_id']) AND $this->session->data['mark_id'] > 0){
						$category['mark_id'] = $this->session->data['mark_id'];
					}
					
					$childrens = $this->model_catalog_mark->getMarks($category['mark_id']);
					foreach($childrens as $children){
						$data['models'][$children['mark_id']] = array(
							'mark_id'		=> $children['mark_id'],
							'parent_id'		=> $category['mark_id'],
							'model_id'		=> $children['mark_id'],
							'name'		=> $children['name'],
							'href'       => $this->url->link('product/mark', 'mark_id=' . $children['mark_id'])
						);
						
					}
					
				}
				
				
				$data['productsview'] = $this->load->view('product/category_consignment_list', $data);				
			}else{
				$data['productsview'] = $this->load->view('product/category_grid', $data);	
			}
		
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
			
			global $club_categ;
			$category_store = $club_categ;
			
			if(in_array((int)$category_id, $category_store) OR in_array((int)$parent_id, $category_store)){
				
				$data['categories'] = array();
				
				if((int)$parent_id == 0){
					$categories = $this->model_catalog_category->getCategories($category_id);
	
					foreach ($categories as $category) {
						
						$tmp_href = $this->url->link('product/category', 'path=' . $category['category_id']);
						
						$data['categories'][] = array(
							'category_id' => $category['category_id'],
							'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
							'thumb'    =>  $this->model_tool_image->resize($category['image'], 270,272),
							'short_href'        => str_replace($data['mark_href'], '',str_replace($data['model_href'], '',$tmp_href)),
							'href'        => $tmp_href,
							
						);
					}
				}
			
				$this->response->setOutput($this->load->view('product/category_store', $data));
			}elseif($category_id == CONSIGNMENT_CATEGORY_ID OR $parent_id == CONSIGNMENT_CATEGORY_ID){
				
				$data['categories'] = array();

				$categories = $this->model_catalog_category->getCategories(CONSIGNMENT_CATEGORY_ID);

				foreach ($categories as $category) {
					
					$tmp_href = $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID . '_' . $category['category_id'] . '&mark_id=0');
					
					$data['categories'][$category['category_id']] = array(
						'category_id' => $category['category_id'],
						'name'        => $category['name'],
						'thumb'    =>  $this->model_tool_image->resize($category['image'], 270,272),
						'short_href'        => str_replace($data['mark_href'], '',str_replace($data['model_href'], '',$tmp_href)),
						'href'        => $tmp_href,
						
					);
				}
				
				$data['breadcrumbs'] = array();
				//Пересобираем крошки
				$category_info = $this->model_catalog_category->getCategory(CONSIGNMENT_CATEGORY_ID, $category_id);
		
				$data['breadcrumbs'][1] = array(
					'text' => $category_info['name'],
					'breadList' => $this->breadList_consigment(CONSIGNMENT_CATEGORY_ID, $category_id),// technics
					'cat_id' => 0,// technics
					'href' => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID)
				);
			
				if($product_info['mark_id']){
					$this->session->data['model_id'] = (int)$product_info['mark_id'];
					$model_info = $this->model_catalog_mark->getMark((int)$product_info['mark_id']);
					
					if($model_info){
						$this->session->data['mark_id'] = $model_info['parent_id'];
					}
				}
			
	
			
				if(isset($this->session->data['mark_id'])){
					$data['mark_id'] = $this->session->data['mark_id'];
					$data['mark_info'] = $mark_info = $this->model_catalog_mark->getMark($data['mark_id']);
		
					if ($mark_info) {
						$data['breadcrumbs'][2] = array(
							'text' => $mark_info['name'],
							'breadList' => $this->breadList_mark_consigment(0, $this->session->data['mark_id'], 'path=' . CONSIGNMENT_CATEGORY_ID . (($category_id > 0 AND $category_id != CONSIGNMENT_CATEGORY_ID) ? '_' .$category_id : '') . '&', $data['mark_id']),// technics
							'cat_id' => $data['mark_id'],// technics
							'href' => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID . (($category_id > 0 AND $category_id != CONSIGNMENT_CATEGORY_ID) ? '_' .$category_id : '') . '&mark_id=' . $mark_info['mark_id'] )
						);
					}
				}
			
	
				
				if(isset($this->session->data['model_id'])){
					$mark_id = $data['model_id'] = $this->session->data['model_id'];
					$data['model_info'] = $mark_info = $this->model_catalog_mark->getMark($data['model_id']);
		
					if ($mark_info) {
						$data['breadcrumbs'][3] = array(
							'text' => $mark_info['name'],
							'breadList' => $this->breadList_mark_consigment($data['mark_id'], $this->session->data['model_id'], 'path=' . CONSIGNMENT_CATEGORY_ID . (($category_id > 0 AND $category_id != CONSIGNMENT_CATEGORY_ID) ? '_' .$category_id : '') . '&', $data['model_id']),// technics
							'cat_id' => $data['model_id'],// technics
							'href' => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID . (($category_id > 0 AND $category_id != CONSIGNMENT_CATEGORY_ID) ? '_' .$category_id : '') . '&mark_id=' . $mark_info['model_id'] )
						);
					}
		
				}
				
				$this->response->setOutput($this->load->view('product/category_consignment', $data));	
			
			}elseif($category_id == 234){
				$this->response->setOutput($this->load->view('product/category_auction', $data));	
			
			}else{
			
				$this->response->setOutput($this->load->view('product/category', $data));	
			}

			
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
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
				'href' => $this->url->link('product/category', $url)
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
}
