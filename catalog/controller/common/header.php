<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		
		
		//ОТключить показ диаграм
		unset($this->session->data['show_as_diagram']);
		
		$data['is_soglashenie'] = false;
		if(isset($this->session->data['is_soglashenie']) AND $this->session->data['is_soglashenie'] == true){
			$data['is_soglashenie'] = true;
		}else{
		
			$this->load->model('catalog/information');
		
			if(isset($this->request->get['information_id']) AND (int)$this->request->get['information_id'] == 3){
				$data['is_soglashenie'] = true;
			}else{
		
				$data['soglashenie'] = $this->model_catalog_information->getInformation(3);
				$data['soglashenie']['link'] = $this->url->link('information/information', 'information_id=3', true);
				
				//$data['text_soglasie'] = $this->language->get('text_soglasie');
				//$data['text_otkaz'] = $this->language->get('text_otkaz');
			}
			
		}			
		
		
		// Analytics
		$this->load->model('account/consignment');
		$this->load->model('setting/extension');
		$this->load->model('catalog/blog_category');
		$data['analytics'] = array();

		
		$data['alternates'] = array();
		
		$languages_res = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		$languages = $languages_res->rows;
	
		$languages[] = array(
							 'code' => 'en-us',
							 'url' => 'en',
							 );
	
		foreach ($languages as $language) {
			
			if($language['code'] == 'en-gb' OR $language['code'] == 'en-us'){
				$language['url'] = '';
			}else{
				$language['url'] .= '/';
			}
			
			if(isset($this->request->get['_route_'])){
				$data['alternates'][] = '<link  rel="alternate" hreflang="'.$language['code'].'" href="'.HTTPS_SERVER.$language['url'].$this->request->get['_route_'].'" />';	
			}else{
				$data['alternates'][] = '<link  rel="alternate" hreflang="'.$language['code'].'" href="'.HTTPS_SERVER.$language['url'].'" />';
			}
			
			
		}
		
		
		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}
		
		
		
		$this->load->model('catalog/mark');
		$data['breadLists'] = array();
		$categories = $this->model_catalog_mark->getMarks(0);
		foreach($categories as $category){
			$data['breadLists'][] = array(
				'name'		=> $category['name'],
				'href'       => $this->url->link('product/mark', 'mark_id=' . $category['mark_id'])
			);
		}
		

		if (isset($this->request->get['route'])) {
		  if (isset($this->request->get['product_id'])) {
			$class = '-' . $this->request->get['product_id'];
		  } elseif (isset($this->request->get['path'])) {
			$class = '-' . $this->request->get['path'];
		  } elseif (isset($this->request->get['manufacturer_id'])) {
			$class = '-' . $this->request->get['manufacturer_id'];
		  } elseif (isset($this->request->get['information_id']   )) {
			  
			$class = '-' . $this->request->get['information_id'];
		  } else {
			$class = '';
		  }

      $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
    } else {
      $data['class'] = 'common-home';
    }	
		
		$data['club_href'] =  $this->url->link('product/category', 'path=' . CLUB_CATEGORY_ID);
		$data['auction_href'] =  $this->url->link('product/category', 'path=234');
		$data['auction_href'] =  HTTPS_SERVER.'auctions';
		
		
		$data['home_url'] = $_SERVER['REQUEST_URI'];
		$data['http_home_url'] = rtrim(HTTPS_SERVER, '/').$_SERVER['REQUEST_URI'];

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['name'] = $this->config->get('config_name');
		$data['config_email'] = $this->config->get('config_email');	
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->model('catalog/mark');
		
		$data['mark_id'] = $data['model_id'] = false;
		
		$data['marks'] = $this->model_catalog_mark->getMarks(0);
		foreach($data['marks'] as $index => $row){
			$data['marks'][$index]['image'] = $server . 'image/' . $row['image'];
			$data['marks'][$index]['href'] = $this->url->link('product/mark', 'mark_id='.$row['mark_id'], true);
		}
		
		/*
		if(isset($this->request->get['mark_id'])){
			
			$mark_id = (int)$this->request->get['mark_id'];
			
			$mark_info = $this->model_catalog_mark->getMark($mark_id);
			
			if((int)$mark_info['parent_id'] == 0){
				$this->session->data['mark_id'] = $mark_id;
				$this->session->data['model_id'] = false;
			}else{
				$this->session->data['mark_id'] = (int)$mark_info['parent_id'];
				$this->session->data['model_id'] = $mark_id;
			}
			
		}
		*/
		if(isset($this->session->data['mark_id'])){
			$data['mark_id'] = $this->session->data['mark_id'];
		}
		
		if(isset($this->session->data['model_id'])){
			$data['model_id'] = $this->session->data['model_id'];
		}

		if(isset($this->session->data['auto_model_id'])){
			$model_info = $this->model_catalog_mark->getMark($this->session->data['auto_model_id']);
			$data['mark_id'] = $this->session->data['mark_id'] = $model_info['parent_id'];
			$data['model_id'] = $this->session->data['model_id'] = $model_info['mark_id'];
		}
		
		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');
			$this->load->model('account/auction');
			$this->load->model('account/notify');

			$data['text_auction'] = sprintf($this->language->get('text_auction'), $this->model_account_auction->getTotalAuctions());
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			
			
			$data['total_notify'] = $this->model_account_notify->getNotifyTotal();
			
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		
		$this->load->model('catalog/information');
		$data['info12'] = $this->model_catalog_information->getInformation(12);
		$data['info12']['href'] = $this->url->link('information/information', 'information_id=12', true);
		
		$data['info19'] = $this->model_catalog_information->getInformation(19);
		$data['info19']['href'] = $this->url->link('information/information', 'information_id=19', true);
		
		$data['info4'] = $this->model_catalog_information->getInformation(4);
		$data['info4']['href'] = $this->url->link('information/information', 'information_id=4', true);
		
		$data['info13'] = $this->model_catalog_information->getInformation(13);
		$data['info13']['href'] = $this->url->link('information/information', 'information_id=13', true);
		
		$data['info14'] = $this->model_catalog_blog_category->getCategory(3);
		$data['info14']['href'] = $this->url->link('product/blog_category', 'blogpath=3', true);

		
		$data['home'] = $this->url->link('common/home');
		$data['product'] = $this->url->link('catalog/product', '', true);
		$data['address_href'] = $this->url->link('account/address', '', true);
		$data['garage'] = $this->url->link('account/garage', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['auction'] = $this->url->link('account/auction', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/simpleedit', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');
		
		$this->load->model('account/auction');
		$auction_total = $this->model_account_auction->getCustomerAuctionProductTotal();
		
		if($auction_total > 0){
			$data['text_auction_href'] = $this->language->get('text_auction_href') . ' ('.$auction_total.')';
		}
		
			$data['contact_form'] = $this->model_catalog_information->getInformation(15);
			
			$data['categories'] = $this->model_catalog_category->getCategories(CONSIGNMENT_CATEGORY_ID);
			
			$data['contact_form']['description'] =  html_entity_decode($data['contact_form']['description'], ENT_QUOTES, 'UTF-8');
			$data['forms'] = $this->load->view('product/product_forms', $data);
		
		if(!$this->request->get){
			$data['categoryMark'] = $this->load->controller('extension/module/categoryMark');
		}
		
		$data['web_type'] = 'website';
	
		if(isset($this->request->get['article_id'])){
			$data['web_type'] = 'article';
		}

		if(isset($this->request->get['mark_id'])){
			$data['web_type'] = 'product.group';
		}
		
		if(isset($this->request->get['path'])){
			$data['web_type'] = 'product.group';
		}
		
		if(isset($this->request->get['product_id'])){
			$data['web_type'] = 'product.item';
		}
		
		
		
		$data['page_type'] = $this->document->getPageType();
		$data['meta_title'] = $this->document->getTitle();
		$data['meta_price'] = number_format($this->document->getPrice(), 2);
		$data['meta_image'] = $this->document->getImage();
		$data['href'] = $this->document->getHref();
		$data['meta_currency'] = $this->session->data['currency'];
		
		if(!$data['href'] AND isset($_SERVER['SCRIPT_URI'])){
			$data['href'] = $_SERVER['SCRIPT_URI'];
		}else{
			$data['href'] = '';
		}
		
		return $this->load->view('common/header', $data);
	}
}
