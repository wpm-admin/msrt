<?php
class ControllerVendorVendorGroup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('vendor/vendor_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_group');

		$this->getList();
	}

	public function add() {
		$this->load->language('vendor/vendor_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor_group->addVendorGroup($this->request->post);

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

			$this->response->redirect($this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('vendor/vendor_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor_group->editVendorGroup($this->request->get['vendor_group_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('vendor/vendor_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_group');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $vendor_group_id) {
				$this->model_vendor_vendor_group->deleteVendorGroup($vendor_group_id);
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

			$this->response->redirect($this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cgd.name';
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
			'href' => $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('vendor/vendor_group/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('vendor/vendor_group/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['vendor_groups'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$vendor_group_total = $this->model_vendor_vendor_group->getTotalVendorGroups();

		$results = $this->model_vendor_vendor_group->getVendorGroups($filter_data);

		foreach ($results as $result) {
			$data['vendor_groups'][] = array(
				'vendor_group_id' => $result['vendor_group_id'],
				'name'              => $result['name'] . (($result['vendor_group_id'] == $this->config->get('config_vendor_group_id')) ? $this->language->get('text_default') : null),
				'sort_order'        => $result['sort_order'],
				'edit'              => $this->url->link('vendor/vendor_group/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_group_id=' . $result['vendor_group_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . '&sort=cgd.name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . '&sort=cg.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $vendor_group_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($vendor_group_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($vendor_group_total - $this->config->get('config_limit_admin'))) ? $vendor_group_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $vendor_group_total, ceil($vendor_group_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_group_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['vendor_group_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['vendor_group_id'])) {
			$data['action'] = $this->url->link('vendor/vendor_group/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('vendor/vendor_group/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_group_id=' . $this->request->get['vendor_group_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('vendor/vendor_group', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['vendor_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$vendor_group_info = $this->model_vendor_vendor_group->getVendorGroup($this->request->get['vendor_group_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['vendor_group_description'])) {
			$data['vendor_group_description'] = $this->request->post['vendor_group_description'];
		} elseif (isset($this->request->get['vendor_group_id'])) {
			$data['vendor_group_description'] = $this->model_vendor_vendor_group->getVendorGroupDescriptions($this->request->get['vendor_group_id']);
		} else {
			$data['vendor_group_description'] = array();
		}

		if (isset($this->request->post['approval'])) {
			$data['approval'] = $this->request->post['approval'];
		} elseif (!empty($vendor_group_info)) {
			$data['approval'] = $vendor_group_info['approval'];
		} else {
			$data['approval'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($vendor_group_info)) {
			$data['sort_order'] = $vendor_group_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_group_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['vendor_group_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');
		$this->load->model('vendor/vendor');

		foreach ($this->request->post['selected'] as $vendor_group_id) {
			if ($this->config->get('config_vendor_group_id') == $vendor_group_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}

			$store_total = $this->model_setting_store->getTotalStoresByVendorGroupId($vendor_group_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}

			$vendor_total = $this->model_vendor_vendor->getTotalVendorsByVendorGroupId($vendor_group_id);

			if ($vendor_total) {
				$this->error['warning'] = sprintf($this->language->get('error_vendor'), $vendor_total);
			}
		}

		return !$this->error;
	}
}