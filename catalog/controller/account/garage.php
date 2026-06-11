<?php
class ControllerAccountGarage extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/garage', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/account');
		$this->load->language('account/garage');

		$this->load->model('account/garage');

		$this->load->model('catalog/mark');

		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			// Remove Garage
			$this->model_account_garage->deleteGarage($this->request->get['remove']);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('account/garage'));
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
			'href' => $this->url->link('account/garage')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['garages'] = array();

		//Марки
		$data['marks'] = $this->model_catalog_mark->getMarks(0);
		
		//Вложенные модели в марки
		$data['marks_level2'] = array();
		foreach($data['marks'] as $mark){
			$data['marks_level2'][$mark['mark_id']] = $this->model_catalog_mark->getMarks($mark['mark_id']);
		}
	
		
		$results = $this->model_account_garage->getGarage();

		foreach ($results as $result) {

	
			if ($result['image']) {
				$image = $result['image'];
			} elseif ($data['marks_level2'][$result['modeli']][$result['mark']]['image']) {
				$image = $this->model_tool_image->resize($data['marks_level2'][$result['modeli']][$result['mark']]['image'], 300, 300);
			} else {
				$image = false;
			}
	
			$name = $data['marks'][$result['modeli']]['name']. ' '.$data['marks_level2'][$result['modeli']][$result['mark']]['name'] . ' - ' . $result['year'];
			
			$data['garages'][] = array(
				'garage_id' => $result['garage_id'],
				'name' => $name,
				'modeli' => $result['modeli'],
				'mark' => $result['mark'],
				'year' => $result['year'],
				'sort_order' => $result['sort_order'],
				'volume' => $result['volume'],
				'tipo' => $result['tipo'],
				'vin' => $result['vin'],
				'status' => $result['status'],
				'thumb'      => $image,
				'href'       => $this->url->link('product/mark', 'path=' . $result['modeli'] . '_' . $result['mark'] ),
				'remove'     => $this->url->link('account/garage/remove', 'garage_id=' . $result['garage_id'])
			);
			
		}

		
		$data['continue'] = $this->url->link('account/account', '', true);
		$data['add'] = $this->url->link('account/garage/add', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/garage', $data));
	}

	public function get_marks(){
		
		$this->load->model('catalog/mark');
		$this->load->model('tool/image');
		
		$mark_id = 0;
		
		if(isset($this->request->get['mark_id'])){
			$mark_id = (int)$this->request->get['mark_id'];
		}
		
		$json = $this->model_catalog_mark->getMark($mark_id);
		
		$json['thumb'] = $this->model_tool_image->resize($json['image'], 300, 300);
				
		$json['marks'] = $this->model_catalog_mark->getMarks($mark_id);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function ajax_data(){
		
		if(isset($this->request->get['name'])){
			$name = $this->db->escape($this->request->get['name']);	
		}else{
			return false;
		}
		
		if(isset($this->request->get['garage_id'])){
			$garage_id = (int)$this->request->get['garage_id'];	
		}else{
			return false;
		}
		
		if(isset($this->request->get['value'])){
			$value = (int)$this->request->get['value'];	
		}else{
			return false;
		}
			
		
		$this->db->query("UPDATE " . DB_PREFIX . "garage SET
						 `" . $name . "`='" . $value . "'
						 WHERE customer_id = '" . (int)$this->customer->getId() . "' AND garage_id='" . $garage_id . "'");
		
		
	}
	
	public function get_mark(){
		
		$this->load->model('catalog/mark');
		$this->load->model('tool/image');
		
		$mark_id = 0;
		
		if(isset($this->request->get['mark_id'])){
			$mark_id = (int)$this->request->get['mark_id'];
		}
		
		$json = $this->model_catalog_mark->getMark($mark_id);
		
		$json['thumb'] = $this->model_tool_image->resize($json['image'], 300, 300);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function remove(){
		
		$garage_id = 0;
		
		if(isset($this->request->get['garage_id'])){
			$garage_id = (int)$this->request->get['garage_id'];
		}
		
		$this->load->model('account/garage');
		$this->model_account_garage->deleteGarage($garage_id);
		
		$this->response->redirect($this->url->link('account/garage', '', true));
	}
	
	public function add() {
		$this->load->language('account/garage');
		
		$json = array();

		$this->load->model('account/garage');
		$this->model_account_garage->addGarage($this->request->post);

		$json['msg'] = $this->language->get('add_garage_success');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
