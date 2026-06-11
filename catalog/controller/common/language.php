<?php
class ControllerCommonLanguage extends Controller {
	public function index() {
		$this->load->language('common/language');

		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);

		$data['code'] = $this->session->data['language'];

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
		
		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'long_name' => $result['long_name'],
					'code' => $result['code'],
					'href' => HTTPS_SERVER.(($result['url'] != '') ? $result['url'].'/' : '') . $url,
				);
			}
		}

		
		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
			
		}

		return $this->load->view('common/language', $data);
	}

	public function language() {
		if (isset($this->request->post['code'])) {
			$this->session->data['language'] = $this->request->post['code'];
		}

		if (isset($this->request->post['redirect'])) {
			$this->response->redirect($this->request->post['redirect']);
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}
}