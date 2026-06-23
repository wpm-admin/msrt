<?php
class ControllerCatalogProduction extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/production');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/production');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/production');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/production');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_production->addProduction($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/production');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/production');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_production->editProduction($this->request->get['production_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/production');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/production');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $production_id) {
				$this->model_catalog_production->deleteProduction($production_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		$this->load->model('catalog/mark');
		
		$marks = $this->model_catalog_mark->getMarks(array('start'=>0,'limit'=>99999));	
	
		$data['id_marks'] = array();
		$data['marks'] = array();
		
		foreach($marks as $index => $row){
			if($row['parent_id'] == 0){
				$data['marks'][] = $data['id_marks'][$index] = $row;
				unset($marks[$index]);

				foreach($marks as $index1 => $row1){
					if($row1['parent_id'] == $index){
						$data['marks'][] = $data['id_marks'][$index1] = $row1;
						unset($marks[$index1]);

					}
				}
			}
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ad.name';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/production/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/production/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['productions'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$production_total = $this->model_catalog_production->getTotalProductions();

		$results = $this->model_catalog_production->getProductions($filter_data);

		foreach ($results as $result) {
			$data['productions'][] = array(
				'production_id'    => $result['production_id'],
				'name'            => $result['name'],
				'mark' => (((int)$result['model_id'] > 0) ? $data['id_marks'][$result['model_id']]['name'] : $data['id_marks'][$result['mark_id']]['name']),
				'sort_order'      => $result['sort_order'],
				//'status'      => $result['status'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'            => $this->url->link('catalog/production/edit', 'user_token=' . $this->session->data['user_token'] . '&production_id=' . $result['production_id'] . $url, true)
			);
		}

		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . '&sort=a.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $production_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($production_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($production_total - $this->config->get('config_limit_admin'))) ? $production_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $production_total, ceil($production_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/production_list', $data));
	}

	protected function getForm() {
		
		
		$this->load->model('catalog/mark');
		
		$marks = $this->model_catalog_mark->getMarks(array('start'=>0,'limit'=>99999));	
	
		$data['id_marks'] = array();
		$data['marks'] = array();
		
		foreach($marks as $index => $row){
			if($row['parent_id'] == 0){
				$data['marks'][] = $data['id_marks'][$index.'_0'] = $row;
				unset($marks[$index]);

				foreach($marks as $index1 => $row1){
					if($row1['parent_id'] == $index){
						$data['marks'][] = $data['id_marks'][$index.'_'.$index1] = $row1;
						unset($marks[$index1]);

					}
				}
			}
		}
		
		$data['text_form'] = !isset($this->request->get['production_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['production_id'])) {
			$data['action'] = $this->url->link('catalog/production/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/production/edit', 'user_token=' . $this->session->data['user_token'] . '&production_id=' . $this->request->get['production_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/production', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['production_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$production_info = $this->model_catalog_production->getProduction($this->request->get['production_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['production_description'])) {
			$data['production_description'] = $this->request->post['production_description'];
		} elseif (isset($this->request->get['production_id'])) {
			$data['production_description'] = $this->model_catalog_production->getProductionDescriptions($this->request->get['production_id']);
		} else {
			$data['production_description'] = array();
		}
		
		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($production_info)) {
			$data['sort_order'] = $production_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($production_info)) {
			$data['status'] = $production_info['status'];
		} else {
			$data['status'] = 1;
		}
/*
		if (isset($this->request->post['mark_id'])) {
			$data['mark_id'] = $this->request->post['mark_id'];
		} elseif (!empty($production_info)) {
			$data['mark_id'] = $production_info['mark_id'];
		} else {
			$data['mark_id'] = 0;
		}
*/
		if (isset($this->request->post['model_id'])) {
			$data['model_id'] = $this->request->post['model_id'];
		} elseif (!empty($production_info)) {
			$data['model_id'] = $production_info['mark_id'].'_'.$production_info['model_id'];
		} else {
			$data['model_id'] = 0;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/production_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/production')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['production_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/production')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $production_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByProductionId($production_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/production');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_production->getProductions($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'production_id'    => $result['production_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'production_group' => $result['production_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
