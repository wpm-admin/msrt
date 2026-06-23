<?php
class ControllerVendorVendorApproval extends Controller {
	public function index() {
		$this->load->language('vendor/vendor_approval');

		$this->document->setTitle($this->language->get('heading_title'));

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

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('vendor/vendor_approval', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_vendor_group_id'] = $filter_vendor_group_id;
		$data['filter_type'] = $filter_type;
		$data['filter_date_added'] = $filter_date_added;

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('vendor/vendor_group');

		$data['vendor_groups'] = $this->model_vendor_vendor_group->getVendorGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_approval', $data));
	}

	public function vendor_approval() {
		$this->load->language('vendor/vendor_approval');

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

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['vendor_approvals'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_vendor_group_id' => $filter_vendor_group_id,
			'filter_type'              => $filter_type,
			'filter_date_added'        => $filter_date_added,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$this->load->model('vendor/vendor_approval');

		$vendor_approval_total = $this->model_vendor_vendor_approval->getTotalVendorApprovals($filter_data);

		$results = $this->model_vendor_vendor_approval->getVendorApprovals($filter_data);

		foreach ($results as $result) {
			$data['vendor_approvals'][] = array(
				'vendor_id'    => $result['vendor_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'vendor_group' => $result['vendor_group'],
				'type'           => $this->language->get('text_' . $result['type']),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'approve'        => $this->url->link('vendor/vendor_approval/approve', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'] . '&type=' . $result['type'], true),
				'deny'           => $this->url->link('vendor/vendor_approval/deny', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'] . '&type=' . $result['type'], true),
				'edit'           => $this->url->link('vendor/vendor/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_id=' . $result['vendor_id'], true)
			);
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_vendor_group_id'])) {
			$url .= '&filter_vendor_group_id=' . $this->request->get['filter_vendor_group_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . $this->request->get['filter_type'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		$pagination = new Pagination();
		$pagination->total = $vendor_approval_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('vendor/vendor_approval/vendor_approval', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($vendor_approval_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($vendor_approval_total - $this->config->get('config_limit_admin'))) ? $vendor_approval_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $vendor_approval_total, ceil($vendor_approval_total / $this->config->get('config_limit_admin')));

		$this->response->setOutput($this->load->view('vendor/vendor_approval_list', $data));
	}

	public function approve() {
		$this->load->language('vendor/vendor_approval');

		$json = array();

		if (!$this->user->hasPermission('modify', 'vendor/vendor_approval')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('vendor/vendor_approval');

			if ($this->request->get['type'] == 'vendor') {
				$this->model_vendor_vendor_approval->approveVendor($this->request->get['vendor_id']);
			} elseif ($this->request->get['type'] == 'affiliate') {
				$this->model_vendor_vendor_approval->approveAffiliate($this->request->get['vendor_id']);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function deny() {
		$this->load->language('vendor/vendor_approval');

		$json = array();

		if (!$this->user->hasPermission('modify', 'vendor/vendor_approval')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('vendor/vendor_approval');

			if ($this->request->get['type'] == 'vendor') {
				$this->model_vendor_vendor_approval->denyVendor($this->request->get['vendor_id']);
			} elseif ($this->request->get['type'] == 'affiliate') {
				$this->model_vendor_vendor_approval->denyAffiliate($this->request->get['vendor_id']);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
