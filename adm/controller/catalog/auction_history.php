<?php
class ControllerCatalogAuctionHistory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/auction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction_history');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/auction_history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction_history');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_auction_history->addAuctionHistory($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/auction_history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction_history');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_auction_history->editAuctionHistory($this->request->get['auction_history_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/auction_history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/auction_history');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $auction_history_id) {
				$this->model_catalog_auction_history->deleteAuctionHistory($auction_history_id);
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

			$this->response->redirect($this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.auction_history_id';
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
			'href' => $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/auction_history/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/auction_history/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['auction_historys'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$auction_history_total = $this->model_catalog_auction_history->getTotalAuctionHistorys();

		$results = $this->model_catalog_auction_history->getAuctionHistorys($filter_data);

		foreach ($results as $result) {
			$data['auction_historys'][] = array(
				'auction_history_id'    => $result['auction_history_id'],
				'name'            => $result['name'],
				'auction_history_group' => $result['auction_history_group'],
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('catalog/auction_history/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_history_id=' . $result['auction_history_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . '&sort=ad.name' . $url, true);
		$data['sort_auction_history_group'] = $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . '&sort=auction_history_group' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . '&sort=a.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $auction_history_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($auction_history_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($auction_history_total - $this->config->get('config_limit_admin'))) ? $auction_history_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $auction_history_total, ceil($auction_history_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_history', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['auction_history_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['auction_history_group'])) {
			$data['error_auction_history_group'] = $this->error['auction_history_group'];
		} else {
			$data['error_auction_history_group'] = '';
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
			'href' => $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['auction_history_id'])) {
			$data['action'] = $this->url->link('catalog/auction_history/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/auction_history/edit', 'user_token=' . $this->session->data['user_token'] . '&auction_history_id=' . $this->request->get['auction_history_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/auction_history', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['auction_history_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$auction_history_info = $this->model_catalog_auction_history->getAuctionHistory($this->request->get['auction_history_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['auction_history_description'])) {
			$data['auction_history_description'] = $this->request->post['auction_history_description'];
		} elseif (isset($this->request->get['auction_history_id'])) {
			$data['auction_history_description'] = $this->model_catalog_auction_history->getAuctionHistoryDescriptions($this->request->get['auction_history_id']);
		} else {
			$data['auction_history_description'] = array();
		}

		if (isset($this->request->post['auction_history_group_id'])) {
			$data['auction_history_group_id'] = $this->request->post['auction_history_group_id'];
		} elseif (!empty($auction_history_info)) {
			$data['auction_history_group_id'] = $auction_history_info['auction_history_group_id'];
		} else {
			$data['auction_history_group_id'] = '';
		}

		$this->load->model('catalog/auction_history_group');

		$data['auction_history_groups'] = $this->model_catalog_auction_history_group->getAuctionHistoryGroups();

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($auction_history_info)) {
			$data['sort_order'] = $auction_history_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/auction_history_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/auction_history')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['auction_history_group_id']) {
			$this->error['auction_history_group'] = $this->language->get('error_auction_history_group');
		}

		foreach ($this->request->post['auction_history_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/auction_history')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $auction_history_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByAuctionHistoryId($auction_history_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/auction_history');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_auction_history->getAuctionHistorys($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'auction_history_id'    => $result['auction_history_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'auction_history_group' => $result['auction_history_group']
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
