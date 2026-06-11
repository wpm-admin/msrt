<?php
class ControllerProductConsignment extends Controller {
	private $error = array();


	public function breadList_mark($category_id, $active_id, $adds = '') {
		$this->load->model('catalog/mark');
		$data = array();
		$categories = $this->model_catalog_mark->getMarks($category_id);
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
		$this->load->language('product/product');

		$data['href'] = $this->url->link('product/consignment', 'consignment_id=' . $this->request->get['consignment_id']);

		
		$data['breadcrumbs'] = array();
		$data['view_last'] = true;

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/mark');
		$this->load->model('catalog/information');
		
		

	
		
		$data['DIR_APPLICATION'] = DIR_APPLICATION;
		

		// technics
		$this->load->language('extension/theme/technics');
		$data['text_technics_points'] = $this->language->get('text_technics_points');
		$data['schema'] = $this->config->get('theme_technics_schema');
		$data['soc_share_code'] = html_entity_decode($this->config->get('theme_technics_soc_share_code'), ENT_QUOTES, 'UTF-8');
		$data['soc_share_prod'] = $this->config->get('theme_technics_soc_share_prod');
		$data['optMode'] = $this->config->get('theme_technics_product_opt_select');
		$data['opt_price'] = $this->config->get('theme_technics_product_opt_price');
		$data['opt_type'] = $this->config->get('theme_technics_product_opt_type');
		$data['category_time'] = $this->config->get('theme_technics_category_time');
		$data['time_text_1'] = $this->language->get('text_time_text_1');
		$data['time_text_2'] = $this->language->get('text_time_text_2');
		$data['text_review'] = $this->language->get('text_review');
		$data['text_review_num_1'] = $this->language->get('text_review_num_1');
		$data['text_review_num_2'] = $this->language->get('text_review_num_2');
		$data['text_review_num_3'] = $this->language->get('text_review_num_3');
		$data['text_show_more'] = $this->language->get('text_show_more');
		$data['text_review_plus'] = $this->language->get('text_review_plus');
		$data['text_review_minus'] = $this->language->get('text_review_minus');
		$this->load->model('extension/module/technics');
		$data['lazyload'] = $this->config->get('theme_technics_lazyload');
		$isDateTime = false;
		// technics end
            
		$this->load->model('catalog/category');
	
		$this->load->model('catalog/manufacturer');


		if (isset($this->request->get['consignment_id'])) {
			$consignment_id = (int)$this->request->get['consignment_id'];
		} else {
			$consignment_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getConsignment($consignment_id);

	
		$data['club_href'] =  $this->url->link('product/category', 'path=' . CLUB_CATEGORY_ID);

		if ($product_info) {
		

		
			$category_info = $this->model_catalog_category->getCategory(CONSIGNMENT_CATEGORY_ID, $product_info['category_id']);
		
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'breadList' => $this->breadList(CONSIGNMENT_CATEGORY_ID, $product_info['category_id']),// technics
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
					$data['breadcrumbs'][] = array(
						'text' => $mark_info['name'],
						'breadList' => $this->breadList_mark(0, $this->session->data['mark_id'], 'path=' . CONSIGNMENT_CATEGORY_ID . '_' .(int)$product_info['category_id'] . '&', $data['mark_id']),// technics
						'cat_id' => $data['mark_id'],// technics
						'href' => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID . '_' .(int)$product_info['category_id'] . '&mark_id=' . $mark_info['mark_id'] )
					);
				}
			}
			
			if(isset($this->session->data['model_id'])){
				$mark_id = $data['model_id'] = $this->session->data['model_id'];
				$data['model_info'] = $mark_info = $this->model_catalog_mark->getMark($data['model_id']);
	
				if ($mark_info) {
					$data['breadcrumbs'][] = array(
						'text' => $mark_info['name'],
						'breadList' => $this->breadList_mark($data['mark_id'], $this->session->data['model_id'], 'path=' . CONSIGNMENT_CATEGORY_ID . '_' .(int)$product_info['category_id'] . '&', $data['model_id']),// technics
						'cat_id' => $data['model_id'],// technics
						'href' => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID . '_' .(int)$product_info['category_id'] . '&mark_id=' . $mark_info['model_id'] )
					);
				}
	
			}
			
			$this->document->setTitle($product_info['name']);
			$this->document->setDescription($product_info['name']);
			$this->document->setKeywords($product_info['name']);
			$this->document->addLink($this->url->link('product/consignment', 'consignment_id=' . $this->request->get['consignment_id']), 'canonical');
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			
			$this->load->model('account/wishlist');
			$data['in_wishlist'] = $this->model_account_wishlist->isWishlist($product_info['consignment_id']);
			
			
			$data['in_cart'] = $this->cart->inCart($product_info['consignment_id']);
			
			
			$data['description'] = $product_info['description'];
			$data['email'] = $product_info['email'];
			$data['telephone'] = $product_info['telephone'];
			$data['year'] = $product_info['year'];
			
				
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
				'href' => $this->url->link('product/consignment', $url . '&consignment_id=' . $this->request->get['consignment_id'])
			);
			*/
			

			
			// technics
			$data['href'] = $this->url->link('product/consignment', $url . '&consignment_id=' . $this->request->get['consignment_id']);
			// technics end
       	
			if(trim($product_info['modeli']) != ''){
				$data['heading_title'] = $product_info['mark'] . ' / ' . $product_info['modeli'] . ' / ' . $product_info['name'];
			}else{
				$data['heading_title'] = $product_info['name'];
			}

			
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['consignment_id'] = (int)$this->request->get['consignment_id'];
			$data['schema_description'] = str_replace(array("\r\n", "\r", "\n", "\""),' ', strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')));
			// technics
            
			$data['sku'] = $product_info['sku'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			$this->load->model('tool/image');

			$images = explode(';', $product_info['images_normal']);
			
			$product_info['image'] = false;
			if(count($images) > 0){
				$product_info['image'] = array_shift($images);
			}
			
			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup');
			} else {
				$data['popup'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
            } 

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'],  787, 458, 'product_thumb');

					if ($this->config->get('theme_technics_og')) { //Maseratinet added this
						$this->document->setOgImage($data['thumb']);
					} //Maseratinet added this
            
				$data['thumb_coordiname'] = $this->model_tool_image->resize($product_info['image'], 1000, 800);
			} else {
				
			$data['thumb'] = $this->model_tool_image->resize('placeholder.png', 787, 458);
            
			}


			if ($product_info['image']) {
				$data['additional'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
			} else {
				$data['additional'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
			}		

			$data['images'] = array();

			foreach ($images as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resize($result, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'), 'product_popup'),
					//'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
					'additional' => $this->model_tool_image->resize($result, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')),
					'thumb' => $this->model_tool_image->resize($result, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'), 'product_additional'),
					'thumb2' => $this->model_tool_image->resize($result, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'), 'product_additional')
				);
			}
	
			$data['price'] = $this->currency->format($this->tax->calculate($product_info['my_price'], $product_info['tax_class_id'], $this->config->get('config_tax')), 'USD');

	
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

			
			if ($this->config->get($this->config->get('theme_technics_config_captcha_fo') . '_status')) {
				$data['captcha_fo'] = $this->load->controller('extension/captcha/' . $this->config->get('theme_technics_config_captcha_fo'));
			} else {
				$data['captcha_fo'] = '';
			}

			$labelsInfo = array();
			if($this->config->get('theme_technics_label')){
				$labelsInfo = $this->config->get('theme_technics_label');
			}
			$data['labelsinfo'] = $labelsInfo ;
			$data['language_id'] = $this->config->get('config_language_id');
			$newest = array();
			$sales = false;
			if(isset($labelsInfo['new']['period']) && $labelsInfo['new']['status']){
				$newest = $this->model_catalog_product->getNewestProducts($labelsInfo['new']['period']);				
			}
			if(isset($labelsInfo['sale']['status']) && $labelsInfo['sale']['status']){
				$sales = true;				
			}
			
			$this->load->model('customer/customer');
			$data['customer_info'] = $this->model_customer_customer->getCustomer($product_info['customer_id']);
			
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			
			
			$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
			
			$this->response->setOutput($this->load->view('product/consignment', $data));	
			
			
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
				'href' => $this->url->link('product/consignment', $url . '&consignment_id=' . $consignment_id)
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
			
// technics
		if (is_file(DIR_IMAGE . $this->config->get('theme_technics_logo_404'))) {
			$data['logo_404'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . 'image/' . $this->config->get('theme_technics_logo_404');
		} else {
			$data['logo_404'] = '';
		}
		$data['text_404'] = sprintf($this->language->get('text_404'), $this->url->link('information/contact', '', true), $this->url->link('product/search', '', true), $this->url->link('common/home', '', true));
		$this->response->setOutput($this->load->view('error/404', $data));
// technics end		
            
		}
	}



	public function review($type = false) {	// technics
            
		$this->load->language('product/consignment');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}


		// technics
		$data['schema'] = $this->config->get('theme_technics_schema');
		$data['reviewsStats'] = array();
		if (isset($this->request->get['rating'])) {
			$rating = $this->request->get['rating'];
		}else{
			$rating = 0;
		}
		// technics end
            
		$data['reviews'] = array();

		
		// technics
		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['consignment_id'], ($page - 1) * 5, 5,  1); //technics add this
		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['consignment_id'],$rating, 1);
		// technics end
          
		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],

				'review_id'     => (int)$result['review_id'], // technics
				'count_good'     => (int)$result['count_good'], // technics
				'count_bad'     => (int)$result['count_bad'], // technics
				'text_plus'     => nl2br($result['text_plus']), // technics
				'text_minus'     => nl2br($result['text_minus']), // technics
				'date_added_schema' => date('Y-m-d', strtotime($result['date_added'])), // technics
            
				'answer'       => $result['answer'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/consignment/review', 'consignment_id=' . $this->request->get['consignment_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		
		$this->load->language('extension/theme/technics');
		$data['text_review_plus'] = $this->language->get('text_review_plus');
		$data['text_review_minus'] = $this->language->get('text_review_minus');
		$this->response->setOutput($this->load->view('product/review', $data));
		    
	}

	
	public function get_filter_url(){
		
		$this->config->set('config_language_id', (int)$this->request->post['language_id']);
		
		if((int)$this->request->post['filter_mark'] > 0){
			$this->session->data['mark_id'] = (int)$this->request->post['filter_mark'];
		}else{
			unset($this->session->data['mark_id']);
		}
		
		if((int)$this->request->post['filter_model'] > 0){
			$this->session->data['model_id'] = (int)$this->request->post['filter_model'];
		}else{
			unset($this->session->data['model_id']);
			unset($this->session->data['auto_model_id']);
		}
			
		$url = $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID);
		
		$categ_url = array();
		
		if(isset($this->request->post['filter_category']) AND
		   count($this->request->post['filter_category']) > 0 AND
		   count($this->request->post['filter_category']) < 4){ //Если выбрано все три категории то нет смысла их отбирать
		
			foreach($this->request->post['filter_category'] as $category_id){
				$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category_id . "' LIMIT 1");
				$categ_url[] = $query->row['keyword'];
			}
			
			$url .= '/' . implode('=', $categ_url);
			
		}

		
		echo $url;
		
	}
	
	public function write() {
		$this->load->language('product/consignment');

		$json = array();

		if (isset($this->request->get['consignment_id']) && $this->request->get['consignment_id']) {
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
					$json['error'] = $this->language->get('error_name');
				}

				if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
					$json['error'] = $this->language->get('error_text');
				}
				
				if (!isset($this->request->post['consignment'])){
					if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
						$json['error'] = $this->language->get('error_rating');
					}
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

					$this->model_catalog_review->addReview($this->request->get['consignment_id'], $this->request->post, 1);

					$json['success'] = $this->language->get('text_success');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_product');
		} 

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


// technics


	public function autocomplete() {
		$this->load->language('extension/theme/technics');
		
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
//			$this->load->model('catalog/option');
			$this->load->model('tool/image');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 7;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_tag'   => $filter_name,
				'filter_model' => $filter_model,
				'filter_price' => true,
				'sort' => 'p.image',
				'order' => 'ASC',
				'start'        => 0,
				'limit'        => $limit
			);

            $key = md5(json_encode($filter_data)).'_'.$this->session->data['currency'];
			
			$cache = $this->cache->get($key.'_'.$this->config->get('config_language_id'));
			
			if(!$cache){
				$results = $this->model_catalog_product->getProducts($filter_data);
	
				$href_search = '';
				$show_all = '';
	
				if (count($results) > 10) {
					$results = array_slice($results, 0, 10);
					$href_search = str_replace('&amp;', '&', $this->url->link('product/search', 'search=' . $filter_name));
					$show_all = $this->language->get('text_technics_show_all');				
				}
	
				foreach ($results as $result) {
					$option_data = array();
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));// technics
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));// technics
					}
					
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = '';
					}
	
					if ((float)$result['special'] && ($this->customer->isLogged() || !$this->config->get('config_customer_price'))) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = '';
					}
	
	
					$json[] = array(
						'consignment_id' => $result['consignment_id'],
						'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
						'model'      => $result['model'],
						'image'      => $image,
						'option'     => $option_data,
						'price'      => $price,
						'special'    => $special,
						'href' 		 => str_replace('&amp;', '&', $this->url->link('product/consignment', 'consignment_id=' . $result['consignment_id'])),
						'href_search' 		 => $href_search,
						'show_all' 		 => $show_all					
					);
				}
				$this->cache->set($key.'_'.$this->config->get('config_language_id'), $json);
			}else{
				$json = $cache;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
    
	public function breadList($category_id, $active_id, $mark_id = false) {
		$this->load->model('catalog/category');
		$data = array();
		$categories = $this->model_catalog_category->getCategories($category_id);
		foreach($categories as $category){
			
			$data[] = array(
				'name'		=> $category['name'],
				'active' => ($category['category_id'] == $active_id) ? true : false,
				'href'       => $this->url->link('product/category', 'path=' . CONSIGNMENT_CATEGORY_ID.'_'.$category['category_id'] . '&mark_id=' . (int)$mark_id)
			);
		}
		return $data;
	}
	public function breadlistcr() {

		$this->load->model('catalog/category');
		$category_id = $this->request->get['cat_id'];
		$data['breadLists'] = array();
		$categories = $this->model_catalog_category->getCategories($category_id);
		foreach($categories as $category){
			$data['breadLists'][] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/category', 'path=' . $category['category_id'])
			);
		}
		$this->response->setOutput($this->load->view('product/bread_popup',$data));
	}
	
}
