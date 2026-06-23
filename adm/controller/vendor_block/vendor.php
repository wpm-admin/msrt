<?php
class ControllerVendorVendor extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('vendor/vendor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');

		$this->getList();
	}

	public function add() {
		$this->load->language('vendor/vendor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor->addVendor($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_vendor_group_id'])) {
				$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('vendor/vendor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor->editVendor($this->request->get['vendor_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_vendor_group_id'])) {
				$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('vendor/vendor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $vendor_id) {
				$this->model_vendor_vendor->deleteVendor($vendor_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_vendor_group_id'])) {
				$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function unlock() {
		$this->load->language('vendor/vendor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor');

		if (isset($this->request->get['email']) && $this->validateUnlock()) {
			$this->model_vendor_vendor->deleteLoginAttempts($this->request->get['email']);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}
		
			if (isset($this->request->get['filter_vendor_group_id'])) {
				$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_ip'])) {
				$url .= '&filter_ip=' . $this->request->get['filter_ip'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_vendor_group_id'])) {
			$filter_vendor_group_id = $this->request->get['filter_vendor_group_id'];
		} else {
			$filter_vendor_group_id = '';
		}

		if (isset($this->request->get['filter_account_number'])) {
			$filter_account_number = $this->request->get['filter_account_number'];
		} else {
			$filter_account_number = '';
		}

		if (isset($this->request->get['filter_shipping'])) {
			$filter_shipping = $this->request->get['filter_shipping'];
		} else {
			$filter_shipping = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}
		
		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

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
			'href' => $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('vendor/vendor/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('vendor/vendor/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		$data['vendors'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_vendor_group_id' => $filter_vendor_group_id,
			'filter_status'            => $filter_status,
			'filter_account_number'            => $filter_account_number,
			'filter_shipping'            => $filter_shipping,
			'filter_date_added'        => $filter_date_added,
			'filter_ip'                => $filter_ip,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$vendor_total = $this->model_vendor_vendor->getTotalVendors($filter_data);

		$results = $this->model_vendor_vendor->getVendors($filter_data);

		foreach ($results as $result) {
			$login_info = $this->model_vendor_vendor->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('vendor/vendor/unlock', 'user_token=' . $this->session->data['user_token'] . '&email=' . $result['email'] . $url, true);
			} else {
				$unlock = '';
			}

			$store_data = array();

			$store_data[] = array(
				'name' => $this->config->get('config_name'),
				'href' => $this->url->link('vendor/vendor/login', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'] . '&store_id=0', true)
			);

			foreach ($stores as $store) {
				$store_data[] = array(
					'name' => $store['name'],
					'href' => $this->url->link('vendor/vendor/login', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'] . '&store_id=' . $store['store_id'], true)
				);
			}

			$data['vendors'][] = array(
				'vendor_id'    => $result['vendor_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'account_number'          => $result['account_number'],
				'shipping'          => $result['shipping'],
				'vendor_group' => $result['vendor_group'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'ip'             => $result['ip'],
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'unlock'         => $unlock,
				'store'          => $store_data,
				'edit'           => $this->url->link('vendor/vendor/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}
	
		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_account_number'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.account_number' . $url, true);
		$data['sort_shipping'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.shipping' . $url, true);
		$data['sort_name'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_email'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, true);
		$data['sort_vendor_group'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=vendor_group' . $url, true);
		$data['sort_status'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_ip'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.ip' . $url, true);
		$data['sort_date_added'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}
		
		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $vendor_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($vendor_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($vendor_total - $this->config->get('config_limit_admin'))) ? $vendor_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $vendor_total, ceil($vendor_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_account_number'] = $filter_account_number;
		$data['filter_shipping'] = $filter_shipping;
		$data['filter_vendor_group_id'] = $filter_vendor_group_id;
		$data['filter_status'] = $filter_status;
		$data['filter_ip'] = $filter_ip;
		$data['filter_date_added'] = $filter_date_added;

		$this->load->model('vendor/vendor_group');

		$data['vendor_groups'] = $this->model_vendor_vendor_group->getVendorGroups();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['vendor_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['vendor_id'])) {
			$data['vendor_id'] = (int)$this->request->get['vendor_id'];
		} else {
			$data['vendor_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['tracking'])) {
			$data['error_tracking'] = $this->error['tracking'];
		} else {
			$data['error_tracking'] = '';
		}

		if (isset($this->error['cheque'])) {
			$data['error_cheque'] = $this->error['cheque'];
		} else {
			$data['error_cheque'] = '';
		}

		if (isset($this->error['paypal'])) {
			$data['error_paypal'] = $this->error['paypal'];
		} else {
			$data['error_paypal'] = '';
		}

		if (isset($this->error['bank_account_name'])) {
			$data['error_bank_account_name'] = $this->error['bank_account_name'];
		} else {
			$data['error_bank_account_name'] = '';
		}

		if (isset($this->error['bank_account_number'])) {
			$data['error_bank_account_number'] = $this->error['bank_account_number'];
		} else {
			$data['error_bank_account_number'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['vendor_field'])) {
			$data['error_vendor_field'] = $this->error['vendor_field'];
		} else {
			$data['error_vendor_field'] = array();
		}

		if (isset($this->error['vendor_address'])) {
			$data['error_vendor_address'] = $this->error['vendor_address'];
		} else {
			$data['error_vendor_address'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

			if (isset($this->request->get['filter_account_number'])) {
				$url .= '&filter_account_number=' . urlencode(html_entity_decode($this->request->get['filter_account_number'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_shipping'])) {
				$url .= '&filter_shipping=' . urlencode(html_entity_decode($this->request->get['filter_shipping'], ENT_QUOTES, 'UTF-8'));
			}
		
		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

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
			'href' => $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['vendor_id'])) {
			$data['action'] = $this->url->link('vendor/vendor/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('vendor/vendor/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $this->request->get['vendor_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['vendor_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$vendor_info = $this->model_vendor_vendor->getVendor($this->request->get['vendor_id']);
		}

		$this->load->model('vendor/vendor_group');

		$data['vendor_groups'] = $this->model_vendor_vendor_group->getVendorGroups();

		if (isset($this->request->post['vendor_group_id'])) {
			$data['vendor_group_id'] = $this->request->post['vendor_group_id'];
		} elseif (!empty($vendor_info)) {
			$data['vendor_group_id'] = $vendor_info['vendor_group_id'];
		} else {
			$data['vendor_group_id'] = $this->config->get('config_vendor_group_id');
		}

		if (isset($this->request->post['prefix'])) {
			$data['prefix'] = $this->request->post['prefix'];
		} elseif (!empty($vendor_info)) {
			$data['prefix'] = $vendor_info['prefix'];
		} else {
			$data['prefix'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($vendor_info)) {
			$data['firstname'] = $vendor_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($vendor_info)) {
			$data['lastname'] = $vendor_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['account_number'])) {
			$data['account_number'] = $this->request->post['account_number'];
		} elseif (!empty($vendor_info)) {
			$data['account_number'] = $vendor_info['account_number'];
		} else {
			$data['account_number'] = '';
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($vendor_info)) {
			$data['shipping'] = $vendor_info['shipping'];
		} else {
			$data['shipping'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($vendor_info)) {
			$data['email'] = $vendor_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($vendor_info)) {
			$data['telephone'] = $vendor_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['telephone2'])) {
			$data['telephone2'] = $this->request->post['telephone2'];
		} elseif (!empty($vendor_info)) {
			$data['telephone2'] = $vendor_info['telephone2'];
		} else {
			$data['telephone2'] = '';
		}

		if (isset($this->request->post['vendor_field'])) {
			$data['account_vendor_field'] = $this->request->post['vendor_field'];
		} elseif (!empty($vendor_info)) {
			$data['account_vendor_field'] = json_decode($vendor_info['vendor_field'], true);
		} else {
			$data['account_vendor_field'] = array();
		}

		if (isset($this->request->post['vendor_address'])) {
			$data['vendor_addresses'] = $this->request->post['vendor_address'];
		} elseif (isset($this->request->get['vendor_id'])) {
			$data['vendor_addresses'] = $this->model_vendor_vendor->getAddresses($this->request->get['vendor_id']);
		} else {
			$data['vendor_addresses'] = array();
		}

		// Custom Fields
		$this->load->model('vendor/vendor_field');
		$this->load->model('tool/upload');

		$data['vendor_fields'] = array();

		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);

		$vendor_fields = $this->model_vendor_vendor_field->getVendorFields($filter_data);

		foreach ($vendor_fields as $vendor_field) {
			$data['vendor_fields'][] = array(
				'vendor_field_id'    => $vendor_field['vendor_field_id'],
				'vendor_field_value' => $this->model_vendor_vendor_field->getVendorFieldValues($vendor_field['vendor_field_id']),
				'name'               => $vendor_field['name'],
				'value'              => $vendor_field['value'],
				'type'               => $vendor_field['type'],
				'location'           => $vendor_field['location'],
				'sort_order'         => $vendor_field['sort_order']
			);

			if($vendor_field['type'] == 'file') {
				if(isset($data['account_vendor_field'][$vendor_field['vendor_field_id']])) {
					$code = $data['account_vendor_field'][$vendor_field['vendor_field_id']];

					$upload_result = $this->model_tool_upload->getUploadByCode($code);

					$data['account_vendor_field'][$vendor_field['vendor_field_id']] = array();
					if($upload_result) {
						$data['account_vendor_field'][$vendor_field['vendor_field_id']]['name'] = $upload_result['name'];
						$data['account_vendor_field'][$vendor_field['vendor_field_id']]['code'] = $upload_result['code'];
					} else {
						$data['account_vendor_field'][$vendor_field['vendor_field_id']]['name'] = "";
						$data['account_vendor_field'][$vendor_field['vendor_field_id']]['code'] = $code;
					}
				}

				foreach($data['vendor_addresses'] as $vendor_address_id => $vendor_address) {
					if(isset($vendor_address['vendor_field'][$vendor_field['vendor_field_id']])) {
						$code = $vendor_address['vendor_field'][$vendor_field['vendor_field_id']];

						$upload_result = $this->model_tool_upload->getUploadByCode($code);
						
						$data['vendor_addresses'][$vendor_address_id]['vendor_field'][$vendor_field['vendor_field_id']] = array();
						if($upload_result) {
							$data['vendor_addresses'][$vendor_address_id]['vendor_field'][$vendor_field['vendor_field_id']]['name'] = $upload_result['name'];
							$data['vendor_addresses'][$vendor_address_id]['vendor_field'][$vendor_field['vendor_field_id']]['code'] = $upload_result['code'];
						} else {
							$data['vendor_addresses'][$vendor_address_id]['vendor_field'][$vendor_field['vendor_field_id']]['name'] = "";
							$data['vendor_addresses'][$vendor_address_id]['vendor_field'][$vendor_field['vendor_field_id']]['code'] = $code;
						}
					}
				}
			}
		}

		if (isset($this->request->post['newsletter'])) {
			$data['newsletter'] = $this->request->post['newsletter'];
		} elseif (!empty($vendor_info)) {
			$data['newsletter'] = $vendor_info['newsletter'];
		} else {
			$data['newsletter'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($vendor_info)) {
			$data['status'] = $vendor_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['safe'])) {
			$data['safe'] = $this->request->post['safe'];
		} elseif (!empty($vendor_info)) {
			$data['safe'] = $vendor_info['safe'];
		} else {
			$data['safe'] = 0;
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['vendor_address_id'])) {
			$data['vendor_address_id'] = $this->request->post['vendor_address_id'];
		} elseif (!empty($vendor_info)) {
			$data['vendor_address_id'] = $vendor_info['vendor_address_id'];
		} else {
			$data['vendor_address_id'] = '';
		}

		// Affliate
		if (isset($this->request->get['vendor_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$affiliate_info = $this->model_vendor_vendor->getAffiliate($this->request->get['vendor_id']);
		}

		if (isset($this->request->post['affiliate'])) {
			$data['affiliate'] = $this->request->post['affiliate'];
		} elseif (!empty($affiliate_info)) {
			$data['affiliate'] = $affiliate_info['status'];
		} else {
			$data['affiliate'] = '';
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} elseif (!empty($affiliate_info)) {
			$data['company'] = $affiliate_info['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->request->post['website'])) {
			$data['website'] = $this->request->post['website'];
		} elseif (!empty($affiliate_info)) {
			$data['website'] = $affiliate_info['website'];
		} else {
			$data['website'] = '';
		}

		if (isset($this->request->post['tracking'])) {
			$data['tracking'] = $this->request->post['tracking'];
		} elseif (!empty($affiliate_info)) {
			$data['tracking'] = $affiliate_info['tracking'];
		} else {
			$data['tracking'] = '';
		}

		if (isset($this->request->post['commission'])) {
			$data['commission'] = $this->request->post['commission'];
		} elseif (!empty($affiliate_info)) {
			$data['commission'] = $affiliate_info['commission'];
		} else {
			$data['commission'] = $this->config->get('config_affiliate_commission');
		}

		if (isset($this->request->post['tax'])) {
			$data['tax'] = $this->request->post['tax'];
		} elseif (!empty($affiliate_info)) {
			$data['tax'] = $affiliate_info['tax'];
		} else {
			$data['tax'] = '';
		}

		if (isset($this->request->post['payment'])) {
			$data['payment'] = $this->request->post['payment'];
		} elseif (!empty($affiliate_info)) {
			$data['payment'] = $affiliate_info['payment'];
		} else {
			$data['payment'] = 'cheque';
		}

		if (isset($this->request->post['cheque'])) {
			$data['cheque'] = $this->request->post['cheque'];
		} elseif (!empty($affiliate_info)) {
			$data['cheque'] = $affiliate_info['cheque'];
		} else {
			$data['cheque'] = '';
		}

		if (isset($this->request->post['paypal'])) {
			$data['paypal'] = $this->request->post['paypal'];
		} elseif (!empty($affiliate_info)) {
			$data['paypal'] = $affiliate_info['paypal'];
		} else {
			$data['paypal'] = '';
		}

		if (isset($this->request->post['bank_name'])) {
			$data['bank_name'] = $this->request->post['bank_name'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_name'] = $affiliate_info['bank_name'];
		} else {
			$data['bank_name'] = '';
		}

		if (isset($this->request->post['bank_branch_number'])) {
			$data['bank_branch_number'] = $this->request->post['bank_branch_number'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_branch_number'] = $affiliate_info['bank_branch_number'];
		} else {
			$data['bank_branch_number'] = '';
		}

		if (isset($this->request->post['bank_swift_code'])) {
			$data['bank_swift_code'] = $this->request->post['bank_swift_code'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_swift_code'] = $affiliate_info['bank_swift_code'];
		} else {
			$data['bank_swift_code'] = '';
		}

		if (isset($this->request->post['bank_account_name'])) {
			$data['bank_account_name'] = $this->request->post['bank_account_name'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_account_name'] = $affiliate_info['bank_account_name'];
		} else {
			$data['bank_account_name'] = '';
		}

		if (isset($this->request->post['bank_account_number'])) {
			$data['bank_account_number'] = $this->request->post['bank_account_number'];
		} elseif (!empty($affiliate_info)) {
			$data['bank_account_number'] = $affiliate_info['bank_account_number'];
		} else {
			$data['bank_account_number'] = '';
		}

		if (isset($this->request->post['vendor_field'])) {
			$data['affiliate_vendor_field'] = $this->request->post['vendor_field'];
		} elseif (!empty($affiliate_info)) {
			$data['affiliate_vendor_field'] = json_decode($affiliate_info['vendor_field'], true);
		} else {
			$data['affiliate_vendor_field'] = array();
		}

		if (isset($this->request->get['vendor_id'])) {
			$data['products'] = $this->model_vendor_vendor->getProducts((int)$this->request->get['vendor_id']);
		}else{
			$data['products'] = array();
		}

		$this->load->model('tool/image');
		
		foreach($data['products'] as $index => $row){
			$data['products'][$index]['thumb'] = $this->model_tool_image->resize($row['image'], 100, 100);
			$data['products'][$index]['edit'] = $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $row['product_id'] , true);
		}
	
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$vendor_info = $this->model_vendor_vendor->getVendorByEmail($this->request->post['email']);

		if (!isset($this->request->get['vendor_id'])) {
			if ($vendor_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($vendor_info && ($this->request->get['vendor_id'] != $vendor_info['vendor_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Custom field validation
		$this->load->model('vendor/vendor_field');

		$vendor_fields = $this->model_vendor_vendor_field->getVendorFields(array('filter_vendor_group_id' => $this->request->post['vendor_group_id']));

		foreach ($vendor_fields as $vendor_field) {
			if (($vendor_field['location'] == 'account') && $vendor_field['required'] && empty($this->request->post['vendor_field'][$vendor_field['vendor_field_id']])) {
				$this->error['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
			} elseif (($vendor_field['location'] == 'account') && ($vendor_field['type'] == 'text') && !empty($vendor_field['validation']) && !filter_var($this->request->post['vendor_field'][$vendor_field['vendor_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $vendor_field['validation'])))) {
				$this->error['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
			}
		}

		if ($this->request->post['password'] || (!isset($this->request->get['vendor_id']))) {
			if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				//$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				//$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if (isset($this->request->post['vendor_address'])) {
			foreach ($this->request->post['vendor_address'] as $key => $value) {
				if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
					$this->error['vendor_address'][$key]['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($value['lastname']) < 1) || (utf8_strlen($value['lastname']) > 32)) {
					$this->error['vendor_address'][$key]['lastname'] = $this->language->get('error_lastname');
				}

				if ((utf8_strlen($value['vendor_address_1']) < 3) || (utf8_strlen($value['vendor_address_1']) > 128)) {
					$this->error['vendor_address'][$key]['vendor_address_1'] = $this->language->get('error_vendor_address_1');
				}

				if ((utf8_strlen($value['city']) < 2) || (utf8_strlen($value['city']) > 128)) {
					$this->error['vendor_address'][$key]['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($value['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($value['postcode']) < 2 || utf8_strlen($value['postcode']) > 10)) {
					$this->error['vendor_address'][$key]['postcode'] = $this->language->get('error_postcode');
				}

				if ($value['country_id'] == '') {
					$this->error['vendor_address'][$key]['country'] = $this->language->get('error_country');
				}

				if (!isset($value['zone_id']) || $value['zone_id'] == '') {
					$this->error['vendor_address'][$key]['zone'] = $this->language->get('error_zone');
				}

				foreach ($vendor_fields as $vendor_field) {
					if (($vendor_field['location'] == 'vendor_address') && $vendor_field['required'] && empty($value['vendor_field'][$vendor_field['vendor_field_id']])) {
						$this->error['vendor_address'][$key]['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
					} elseif (($vendor_field['location'] == 'vendor_address') && ($vendor_field['type'] == 'text') && !empty($vendor_field['validation']) && !filter_var($value['vendor_field'][$vendor_field['vendor_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $vendor_field['validation'])))) {
						$this->error['vendor_address'][$key]['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
                    }
				}
			}
		}

		if ($this->request->post['affiliate']) {
			if ($this->request->post['payment'] == 'cheque') {
				if ($this->request->post['cheque'] == '') {
					$this->error['cheque'] = $this->language->get('error_cheque');
				}
			} elseif ($this->request->post['payment'] == 'paypal') {
				if ((utf8_strlen($this->request->post['paypal']) > 96) || !filter_var($this->request->post['paypal'], FILTER_VALIDATE_EMAIL)) {
					$this->error['paypal'] = $this->language->get('error_paypal');
				}
			} elseif ($this->request->post['payment'] == 'bank') {
				if ($this->request->post['bank_account_name'] == '') {
					$this->error['bank_account_name'] = $this->language->get('error_bank_account_name');
				}

				if ($this->request->post['bank_account_number'] == '') {
					$this->error['bank_account_number'] = $this->language->get('error_bank_account_number');
				}
			}

			if (!$this->request->post['tracking']) {
				$this->error['tracking'] = $this->language->get('error_tracking');
			}

			$affiliate_info = $this->model_vendor_vendor->getAffiliateByTracking($this->request->post['tracking']);

			if (!isset($this->request->get['vendor_id'])) {
				if ($affiliate_info) {
					$this->error['tracking'] = $this->language->get('error_tracking_exists');
				}
			} else {
				if ($affiliate_info && ($this->request->get['vendor_id'] != $affiliate_info['vendor_id'])) {
					$this->error['tracking'] = $this->language->get('error_tracking_exists');
				}
			}

			foreach ($vendor_fields as $vendor_field) {
				if (($vendor_field['location'] == 'affiliate') && $vendor_field['required'] && empty($this->request->post['vendor_field'][$vendor_field['vendor_field_id']])) {
					$this->error['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
				} elseif (($vendor_field['location'] == 'affiliate') && ($vendor_field['type'] == 'text') && !empty($vendor_field['validation']) && !filter_var($this->request->post['vendor_field'][$vendor_field['vendor_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $vendor_field['validation'])))) {
					$this->error['vendor_field'][$vendor_field['vendor_field_id']] = sprintf($this->language->get('error_vendor_field'), $vendor_field['name']);
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateUnlock() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function login() {
		if (isset($this->request->get['vendor_id'])) {
			$vendor_id = $this->request->get['vendor_id'];
		} else {
			$vendor_id = 0;
		}

		$this->load->model('vendor/vendor');

		$vendor_info = $this->model_vendor_vendor->getVendor($vendor_id);

		if ($vendor_info) {
			// Create token to login with
			$token = token(64);

			$this->model_vendor_vendor->editToken($vendor_id, $token);

			if (isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=account/login&token=' . $token);
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=account/login&token=' . $token);
			}
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function history() {
		$this->load->language('vendor/vendor');

		$this->load->model('vendor/vendor');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->config->get('config_limit_admin');

		$data['histories'] = array();

		$results = $this->model_vendor_vendor->getHistories($this->request->get['vendor_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'comment'    => $result['comment'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_vendor_vendor->getTotalHistories($this->request->get['vendor_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('vendor/vendor/history', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $this->request->get['vendor_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit), $history_total, ceil($history_total / $limit));

		$this->response->setOutput($this->load->view('vendor/vendor_history', $data));
	}

	public function addHistory() {
		$this->load->language('vendor/vendor');

		$json = array();

		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('vendor/vendor');

			$this->model_vendor_vendor->addHistory($this->request->get['vendor_id'], $this->request->post['comment']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function transaction() {
		$this->load->language('vendor/vendor');

		$this->load->model('vendor/vendor');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->config->get('config_limit_admin');

		$data['transactions'] = array();

		$results = $this->model_vendor_vendor->getTransactions($this->request->get['vendor_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->currency->format($this->model_vendor_vendor->getTransactionTotal($this->request->get['vendor_id']), $this->config->get('config_currency'));

		$transaction_total = $this->model_vendor_vendor->getTotalTransactions($this->request->get['vendor_id']);

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('vendor/vendor/transaction', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $this->request->get['vendor_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($transaction_total - $limit)) ? $transaction_total : ((($page - 1) * $limit) + $limit), $transaction_total, ceil($transaction_total / $limit));

		$this->response->setOutput($this->load->view('vendor/vendor_transaction', $data));
	}

	public function addTransaction() {
		$this->load->language('vendor/vendor');

		$json = array();

		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('vendor/vendor');

			$this->model_vendor_vendor->addTransaction($this->request->get['vendor_id'], $this->request->post['description'], $this->request->post['amount']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function reward() {
		$this->load->language('vendor/vendor');

		$this->load->model('vendor/vendor');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->config->get('config_limit_admin');

		$data['rewards'] = array();

		$results = $this->model_vendor_vendor->getRewards($this->request->get['vendor_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->model_vendor_vendor->getRewardTotal($this->request->get['vendor_id']);

		$reward_total = $this->model_vendor_vendor->getTotalRewards($this->request->get['vendor_id']);

		$pagination = new Pagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('vendor/vendor/reward', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $this->request->get['vendor_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($reward_total - $limit)) ? $reward_total : ((($page - 1) * $limit) + $limit), $reward_total, ceil($reward_total / $limit));

		$this->response->setOutput($this->load->view('vendor/vendor_reward', $data));
	}

	public function addReward() {
		$this->load->language('vendor/vendor');

		$json = array();

		if (!$this->user->hasPermission('modify', 'vendor/vendor')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('vendor/vendor');

			$this->model_vendor_vendor->addReward($this->request->get['vendor_id'], $this->request->post['description'], $this->request->post['points']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function ip() {
		$this->load->language('vendor/vendor');

		$this->load->model('vendor/vendor');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->config->get('config_limit_admin');

		$data['ips'] = array();

		$results = $this->model_vendor_vendor->getIps($this->request->get['vendor_id'], ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['ips'][] = array(
				'ip'         => $result['ip'],
				'total'      => $this->model_vendor_vendor->getTotalVendorsByIp($result['ip']),
				'date_added' => date('d/m/y', strtotime($result['date_added'])),
				'filter_ip'  => $this->url->link('vendor/vendor', 'user_token=' . $this->session->data['user_token'] . '&filter_ip=' . $result['ip'], true)
			);
		}

		$ip_total = $this->model_vendor_vendor->getTotalIps($this->request->get['vendor_id']);

		$pagination = new Pagination();
		$pagination->total = $ip_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('vendor/vendor/ip', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $this->request->get['vendor_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ip_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($ip_total - $limit)) ? $ip_total : ((($page - 1) * $limit) + $limit), $ip_total, ceil($ip_total / $limit));

		$this->response->setOutput($this->load->view('vendor/vendor_ip', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['filter_affiliate'])) {
				$filter_affiliate = $this->request->get['filter_affiliate'];
			} else {
				$filter_affiliate = '';
			}

			$this->load->model('vendor/vendor');

			$filter_data = array(
				'filter_name'      => $filter_name,
				'filter_email'     => $filter_email,
				'filter_affiliate' => $filter_affiliate,
				'start'            => 0,
				'limit'            => 5
			);

			$results = $this->model_vendor_vendor->getVendors($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'vendor_id'       => $result['vendor_id'],
					'vendor_group_id' => $result['vendor_group_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'vendor_group'    => $result['vendor_group'],
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'vendor_field'      => json_decode($result['vendor_field'], true),
					'vendor_address'           => $this->model_vendor_vendor->getAddresses($result['vendor_id'])
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

	public function customfield() {
		$json = array();

		$this->load->model('vendor/vendor_field');

		// Vendor Group
		if (isset($this->request->get['vendor_group_id'])) {
			$vendor_group_id = $this->request->get['vendor_group_id'];
		} else {
			$vendor_group_id = $this->config->get('config_vendor_group_id');
		}

		$vendor_fields = $this->model_vendor_vendor_field->getVendorFields(array('filter_vendor_group_id' => $vendor_group_id));

		foreach ($vendor_fields as $vendor_field) {
			$json[] = array(
				'vendor_field_id' => $vendor_field['vendor_field_id'],
				'required'        => empty($vendor_field['required']) || $vendor_field['required'] == 0 ? false : true
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function vendor_address() {
		$json = array();

		if (!empty($this->request->get['vendor_address_id'])) {
			$this->load->model('vendor/vendor');

			$json = $this->model_vendor_vendor->getAddress($this->request->get['vendor_address_id']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
