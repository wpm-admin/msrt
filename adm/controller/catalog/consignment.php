<?php
class ControllerCatalogConsignment extends Controller {
	private $error = array();

	public function index() {
		

		
		$this->load->language('catalog/consignment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/consignment');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/consignment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/consignment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_consignment->addConsignment($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/consignment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/consignment');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_consignment->editConsignment($this->request->get['consignment_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/consignment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/consignment');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $consignment_id) {
				$this->model_catalog_consignment->deleteConsignment($consignment_id);
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

			$this->response->redirect($this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		

		
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
			'href' => $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/consignment/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/consignment/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['consignments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('customer/customer');
		$this->load->model('catalog/category');
		$this->load->model('catalog/mark');
		
		$consignment_total = $this->model_catalog_consignment->getTotalConsignments();

		$results = $this->model_catalog_consignment->getConsignments($filter_data);

		foreach ($results as $result) {
			$data['consignments'][$result['consignment_id']] = $result;
			$data['consignments'][$result['consignment_id']]['edit'] = $this->url->link('catalog/consignment/edit', 'user_token=' . $this->session->data['user_token'] . '&consignment_id=' . $result['consignment_id'] . $url, true);
			$data['consignments'][$result['consignment_id']]['customer'] = $this->model_customer_customer->getCustomer($result['customer_id']);
			$data['consignments'][$result['consignment_id']]['category'] = $this->model_catalog_category->getCategory($result['category_id']);
			$data['consignments'][$result['consignment_id']]['mark'] = $this->model_catalog_mark->getMark($result['mark_id']);
			$data['consignments'][$result['consignment_id']]['images'] = explode(';', $result['images']);
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

		$data['sort_name'] = $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_consignment_group'] = $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . '&sort=consignment_group' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . '&sort=a.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $consignment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($consignment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($consignment_total - $this->config->get('config_limit_admin'))) ? $consignment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $consignment_total, ceil($consignment_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/consignment_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['consignment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['consignment_group'])) {
			$data['error_consignment_group'] = $this->error['consignment_group'];
		} else {
			$data['error_consignment_group'] = '';
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
			'href' => $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['consignment_id'])) {
			$data['action'] = $this->url->link('catalog/consignment/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/consignment/edit', 'user_token=' . $this->session->data['user_token'] . '&consignment_id=' . $this->request->get['consignment_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/consignment', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['consignment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$consignment_info = $this->model_catalog_consignment->getConsignment($this->request->get['consignment_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($consignment_info)) {
			$data['customer_id'] = $consignment_info['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

	if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($consignment_info)) {
			$data['product_id'] = $consignment_info['product_id'];
		} else {
			$data['product_id'] = 0;
		}

	if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($consignment_info)) {
			$data['category_id'] = $consignment_info['category_id'];
		} else {
			$data['category_id'] = 0;
		}

	if (isset($this->request->post['mark_id'])) {
			$data['mark_id'] = $this->request->post['mark_id'];
		} elseif (!empty($consignment_info)) {
			$data['mark_id'] = $consignment_info['mark_id'];
		} else {
			$data['mark_id'] = 0;
		}

	if (isset($this->request->post['year'])) {
			$data['year'] = $this->request->post['year'];
		} elseif (!empty($consignment_info)) {
			$data['year'] = $consignment_info['year'];
		} else {
			$data['year'] = '';
		}

	if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($consignment_info)) {
			$data['price'] = $consignment_info['price'];
		} else {
			$data['price'] = 0;
		}

	if (isset($this->request->post['my_price'])) {
			$data['my_price'] = $this->request->post['my_price'];
		} elseif (!empty($consignment_info)) {
			$data['my_price'] = $consignment_info['my_price'];
		} else {
			$data['my_price'] = 0;
		}

	if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($consignment_info)) {
			$data['customer_id'] = $consignment_info['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

	if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($consignment_info)) {
			$data['description'] = $consignment_info['description'];
		} else {
			$data['description'] = '';
		}
		
	if (isset($this->request->post['images'])) {
			$data['images'] = $this->request->post['images'];
		} elseif (!empty($consignment_info)) {
			$data['images'] = explode(';', $consignment_info['images']);
		} else {
			$data['images'] = array();
		}

	if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($consignment_info)) {
			$data['name'] = $consignment_info['name'];
		} else {
			$data['name'] = '';
		}

	if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($consignment_info)) {
			$data['telephone'] = $consignment_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

	if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($consignment_info)) {
			$data['email'] = $consignment_info['email'];
		} else {
			$data['email'] = '';
		}

	if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($consignment_info)) {
			$data['status'] = $consignment_info['status'];
		} else {
			$data['status'] = 1;
		}

	$data['statuses'] = array(
							  1 => 'new added',
							  2 => 'edited',
							  3 => 'published',
							  4 => 'to sale',
							  5 => 'disable',
							  );
		
	if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($consignment_info)) {
			$data['date_added'] = $consignment_info['date_added'];
		} else {
			$data['date_added'] = date('Y-m-d');
		}



		$data['add_product'] = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . '&consignment_id='.$consignment_info['consignment_id'].'&add_like_consignment', true);;
		$data['user_token'] = $this->request->get['user_token'];

		$this->load->model('customer/customer');
		$data['customer'] = $this->model_customer_customer->getCustomer($data['customer_id']);

		$this->load->model('catalog/mark');
		$data['mark'] = $this->model_catalog_mark->getMark($data['mark_id']);

		$this->load->model('catalog/category');
		$data['category'] = $this->model_catalog_category->getCategory($data['category_id']);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/consignment_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/consignment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}


		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/consignment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		
		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/consignment');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_consignment->getConsignments($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'consignment_id'    => $result['consignment_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'consignment_group' => $result['consignment_group']
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
