<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		
		
	$data['info16'] = false;
	if((int)$this->request->get['information_id'] == 16){
		$data['info16'] = true;
	}
		
	$data['info18'] = false;
	if((int)$this->request->get['information_id'] == 18){
		$data['info18'] = true;
	}
		
	//Windshield-production
	if($information_id == 23){
		
		$this->load->model('catalog/mark');
		$this->load->model('catalog/production');
		
		$data['productions'] = array();
		$data['marks'] = array();
		$data['models'] = array();
		$marks = array();
		$models = array();

		$filter_data = array(
			'start' => 0,
			'limit' => 999999
		);

		$results = $this->model_catalog_production->getProductions($filter_data);
		foreach ($results as $result) {
			
			if($result['mark_id'] > 0){
				$marks[$result['mark_id']] = $result['mark_id'];
			}
			
			if($result['model_id'] > 0){
				$models[$result['model_id']] = $result['model_id'];
			}
			
			
			$data['productions'][] = array(
				'production_id'    => $result['production_id'],
				'name'            => $result['name'],
				'mark_id'            => $result['mark_id'],
				'model_id'            => $result['model_id'],
				'mark' => (((int)$result['model_id'] > 0) ? $data['id_marks'][$result['model_id']]['name'] : $data['id_marks'][$result['mark_id']]['name']),
				'sort_order'      => $result['sort_order'],
				//'status'      => $result['status'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'            => $this->url->link('catalog/production/edit', 'user_token=' . $this->session->data['user_token'] . '&production_id=' . $result['production_id'] . $url, true)
			);
		}
		
		foreach($marks as $index => $mark_id){
			
			$data['marks'][$index] = $this->model_catalog_mark->getMark((int)$mark_id);
		}
		
		foreach($models as $index => $mark_id){
			
			$mark_info = $this->model_catalog_mark->getMark((int)$mark_id);
			
			if($mark_info){
				$data['models'][$index] = $mark_info;
			}
			
		}
		
	}

	//$data['info15'] = $this->url->link('information/information', 'information_id=15');
	//$data['info14'] = $this->url->link('information/information', 'information_id=14');
		
	$data['language_id'] = $this->config->get['config_language_id'];
		
		
		$data['home_url'] = $_SERVER['REQUEST_URI'];
		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$this->document->setTitle($information_info['meta_title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['heading_title'] = $information_info['title'];

			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
			
			if((int)$information_id == 23){
				$this->response->setOutput($this->load->view('information/information_windshield', $data));
			}else{
				$this->response->setOutput($this->load->view('information/information', $data));	
			}
			
			
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

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

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->addHeader('X-Robots-Tag: noindex');

		$this->response->setOutput($output);
	}
}
