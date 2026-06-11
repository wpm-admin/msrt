<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('product/product');
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['forms'] = $this->load->view('product/product_forms', $data);
		
		$data['end_date'] = date('Y');
		
		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
 	
		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		//Получим массив языковых УРЛ
		$lang_urls = array();
		foreach ($results as $result) {
			$lang_urls[] = $result['url'];
		}
		
		$url = '';
		if(isset($this->request->get['_route_'])){
			$url = explode('/', $this->request->get['_route_']);
			
			//Уберем из ЧПУ языковой УРЛ
			if(in_array($url[0],$lang_urls)){
				unset($url[0]);
			}
			//Проверм на два уровня в случае замусорення чпу
			if(isset($url[1]) AND in_array($url[1],$lang_urls)){
				unset($url[1]);
			}
			$url = implode('/', $url);
		}	
		
		//Соберем строку для футера
		foreach ($results as $result) {
			if ($result['status']) {
				if($result['language_id'] == $this->config->get('config_language_id')){
					$data['languages'][] = '<a class="lang_select" href="'.HTTPS_SERVER.(($result['url'] != '') ? $result['url'].'/' : '') . $url.'">'.$result['name'].'</a>';
				}else{
					$data['languages'][] = '<a href="'.HTTPS_SERVER.(($result['url'] != '') ? $result['url'].'/' : '') . $url.'">'.$result['name'].'</a>';
				}
			}
		}
		$data['text_languages'] = implode('|', $data['languages']);
		
		
		
		$data['config_email'] = $this->config->get('config_email');	
		$data['telephone'] = $this->config->get('config_telephone');
		
		
		
		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		return $this->load->view('common/footer', $data);
	}
}
