<?php
class ControllerCatalogAuction extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_auction->addAuction($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_auction->editAuction($this->request->get['auction_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $auction_id) {
				$this->model_catalog_auction->deleteAuction($auction_id);
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

			$this->response->redirect($this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.auction_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
			'href' => $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/auction/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/auction/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['auctions'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$auction_total = $this->model_catalog_auction->getTotalAuctions();

		$results = $this->model_catalog_auction->getAuctions($filter_data);

		foreach ($results as $result) {
			
			$slot = $result;
			$slot['edit'] = $this->url->link('catalog/auction/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_id=' . $result['auction_id'] . $url, true);
			$slot['product_edit'] = $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true);
			
			$data['auctions'][] = $slot;
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

		$data['sort_name'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
		$data['sort_auction_group'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=auction_group' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . '&sort=a.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $auction_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($auction_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($auction_total - $this->config->get('config_limit_admin'))) ? $auction_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $auction_total, ceil($auction_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['auction_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['user_token'] = $this->request->get['user_token'];
		
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

		if (isset($this->error['auction_group'])) {
			$data['error_auction_group'] = $this->error['auction_group'];
		} else {
			$data['error_auction_group'] = '';
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
			'href' => $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['auction_id'])) {
			$data['action'] = $this->url->link('catalog/auction/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/auction/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_id=' . $this->request->get['auction_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/auction', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['auction_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$auction_info = $this->model_catalog_auction->getAuction($this->request->get['auction_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

	
		if (isset($this->request->post['lot_id'])) {
			$data['lot_id'] = $this->request->post['lot_id'];
		} elseif (!empty($auction_info)) {
			$data['lot_id'] = $auction_info['lot_id'];
		} else {
			$data['lot_id'] = '';
		}

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($auction_info)) {
			$data['product_id'] = $auction_info['product_id'];
		} else {
			$data['product_id'] = 0;
		}

		if($data['product_id']){
			$this->load->model('catalog/product');
			$data['product_info'] = $this->model_catalog_product->getProduct($data['product_id']);
		}

		if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($auction_info)) {
			$data['customer_id'] = $auction_info['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}
		
		if($data['customer_id']){
			$this->load->model('customer/customer');
			$data['customer_info'] = $this->model_customer_customer->getCustomer($data['customer_id']);
		}	
		
		if (isset($this->request->post['price_start'])) {
			$data['price_start'] = $this->request->post['price_start'];
		} elseif (!empty($auction_info)) {
			$data['price_start'] = $auction_info['price_start'];
		} else {
			$data['price_start'] = 0;
		}

		if (isset($this->request->post['price_end'])) {
			$data['price_end'] = $this->request->post['price_end'];
		} elseif (!empty($auction_info)) {
			$data['price_end'] = $auction_info['price_end'];
		} else {
			$data['price_end'] = 0;
		}

		if (isset($this->request->post['price_step'])) {
			$data['price_step'] = $this->request->post['price_step'];
		} elseif (!empty($auction_info)) {
			$data['price_step'] = $auction_info['price_step'];
		} else {
			$data['price_step'] = 0;
		}

		if (isset($this->request->post['reserv_price'])) {
			$data['reserv_price'] = $this->request->post['reserv_price'];
		} elseif (!empty($auction_info)) {
			$data['reserv_price'] = $auction_info['reserv_price'];
		} else {
			$data['reserv_price'] = 0;
		}

		if (isset($this->request->post['key'])) {
			$data['key'] = $this->request->post['key'];
		} elseif (!empty($auction_info)) {
			$data['key'] = $auction_info['key'];
		} else {
			$data['key'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($auction_info)) {
			$data['status'] = $auction_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($auction_info)) {
			$data['date_added'] = $auction_info['date_added'];
		} else {
			$data['date_added'] = date('Y-m-d H:i:s');
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} elseif (!empty($auction_info)) {
			$data['date_start'] = $auction_info['date_start'];
		} else {
			$data['date_start'] = date('Y-m-d H:i:s');
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} elseif (!empty($auction_info)) {
			$data['date_end'] = $auction_info['date_end'];
		} else {
			$data['date_end'] = date('Y-m-d H:i:s', strtotime('+3 day'));
		}


		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/auction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/auction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');
		/*
		foreach ($this->request->post['selected'] as $auction_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByAuctionId($auction_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}
		*/
		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/auction');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_auction->getAuctions($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'auction_id'    => $result['auction_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'auction_group' => $result['auction_group']
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
