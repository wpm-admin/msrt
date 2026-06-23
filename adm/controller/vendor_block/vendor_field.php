<?php
class ControllerVendorVendorField extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('vendor/vendor_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_field');

		$this->getList();
	}

	public function add() {
		$this->load->language('vendor/vendor_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor_field->addVendorField($this->request->post);

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

			$this->response->redirect($this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('vendor/vendor_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_field');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_vendor_vendor_field->editVendorField($this->request->get['vendor_field_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('vendor/vendor_field');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('vendor/vendor_field');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $vendor_field_id) {
				$this->model_vendor_vendor_field->deleteVendorField($vendor_field_id);
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

			$this->response->redirect($this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cfd.name';
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
			'href' => $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('vendor/vendor_field/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('vendor/vendor_field/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['vendor_fields'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$vendor_field_total = $this->model_vendor_vendor_field->getTotalVendorFields();

		$results = $this->model_vendor_vendor_field->getVendorFields($filter_data);

		foreach ($results as $result) {
			$type = '';

			switch ($result['type']) {
				case 'select':
					$type = $this->language->get('text_select');
					break;
				case 'radio':
					$type = $this->language->get('text_radio');
					break;
				case 'checkbox':
					$type = $this->language->get('text_checkbox');
					break;
				case 'input':
					$type = $this->language->get('text_input');
					break;
				case 'text':
					$type = $this->language->get('text_text');
					break;
				case 'textarea':
					$type = $this->language->get('text_textarea');
					break;
				case 'file':
					$type = $this->language->get('text_file');
					break;
				case 'date':
					$type = $this->language->get('text_date');
					break;
				case 'datetime':
					$type = $this->language->get('text_datetime');
					break;
				case 'time':
					$type = $this->language->get('text_time');
					break;
			}

			$data['vendor_fields'][] = array(
				'vendor_field_id' => $result['vendor_field_id'],
				'name'            => $result['name'],
				'location'        => $this->language->get('text_' . $result['location']),
				'type'            => $type,
				'status'          => $result['status'],
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('vendor/vendor_field/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_field_id=' . $result['vendor_field_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . '&sort=cfd.name' . $url, true);
		$data['sort_location'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . '&sort=cf.location' . $url, true);
		$data['sort_type'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . '&sort=cf.type' . $url, true);
		$data['sort_status'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . '&sort=cf.status' . $url, true);
		$data['sort_sort_order'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . '&sort=cf.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $vendor_field_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($vendor_field_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($vendor_field_total - $this->config->get('config_limit_admin'))) ? $vendor_field_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $vendor_field_total, ceil($vendor_field_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_field_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['vendor_field_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['vendor_field_value'])) {
			$data['error_vendor_field_value'] = $this->error['vendor_field_value'];
		} else {
			$data['error_vendor_field_value'] = array();
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
			'href' => $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['vendor_field_id'])) {
			$data['action'] = $this->url->link('vendor/vendor_field/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('vendor/vendor_field/edit', 'user_token=' . $this->session->data['user_token'] . '&vendor_field_id=' . $this->request->get['vendor_field_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('vendor/vendor_field', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['vendor_field_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$vendor_field_info = $this->model_vendor_vendor_field->getVendorField($this->request->get['vendor_field_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['vendor_field_description'])) {
			$data['vendor_field_description'] = $this->request->post['vendor_field_description'];
		} elseif (isset($this->request->get['vendor_field_id'])) {
			$data['vendor_field_description'] = $this->model_vendor_vendor_field->getVendorFieldDescriptions($this->request->get['vendor_field_id']);
		} else {
			$data['vendor_field_description'] = array();
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($vendor_field_info)) {
			$data['location'] = $vendor_field_info['location'];
		} else {
			$data['location'] = '';
		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($vendor_field_info)) {
			$data['type'] = $vendor_field_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['value'])) {
			$data['value'] = $this->request->post['value'];
		} elseif (!empty($vendor_field_info)) {
			$data['value'] = $vendor_field_info['value'];
		} else {
			$data['value'] = '';
		}

		if (isset($this->request->post['validation'])) {
			$data['validation'] = $this->request->post['validation'];
		} elseif (!empty($vendor_field_info)) {
			$data['validation'] = $vendor_field_info['validation'];
		} else {
			$data['validation'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($vendor_field_info)) {
			$data['status'] = $vendor_field_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($vendor_field_info)) {
			$data['sort_order'] = $vendor_field_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['vendor_field_value'])) {
			$vendor_field_values = $this->request->post['vendor_field_value'];
		} elseif (isset($this->request->get['vendor_field_id'])) {
			$vendor_field_values = $this->model_vendor_vendor_field->getVendorFieldValueDescriptions($this->request->get['vendor_field_id']);
		} else {
			$vendor_field_values = array();
		}

		$data['vendor_field_values'] = array();

		foreach ($vendor_field_values as $vendor_field_value) {
			$data['vendor_field_values'][] = array(
				'vendor_field_value_id'          => $vendor_field_value['vendor_field_value_id'],
				'vendor_field_value_description' => $vendor_field_value['vendor_field_value_description'],
				'sort_order'                     => $vendor_field_value['sort_order']
			);
		}

		if (isset($this->request->post['vendor_field_vendor_group'])) {
			$vendor_field_vendor_groups = $this->request->post['vendor_field_vendor_group'];
		} elseif (isset($this->request->get['vendor_field_id'])) {
			$vendor_field_vendor_groups = $this->model_vendor_vendor_field->getVendorFieldVendorGroups($this->request->get['vendor_field_id']);
		} else {
			$vendor_field_vendor_groups = array();
		}

		$data['vendor_field_vendor_group'] = array();

		foreach ($vendor_field_vendor_groups as $vendor_field_vendor_group) {
			$data['vendor_field_vendor_group'][] = $vendor_field_vendor_group['vendor_group_id'];
		}

		$data['vendor_field_required'] = array();

		foreach ($vendor_field_vendor_groups as $vendor_field_vendor_group) {
			if ($vendor_field_vendor_group['required']) {
				$data['vendor_field_required'][] = $vendor_field_vendor_group['vendor_group_id'];
			}
		}

		$this->load->model('vendor/vendor_group');

		$data['vendor_groups'] = $this->model_vendor_vendor_group->getVendorGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('vendor/vendor_field_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['vendor_field_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (($this->request->post['type'] == 'select' || $this->request->post['type'] == 'radio' || $this->request->post['type'] == 'checkbox')) {
			if (!isset($this->request->post['vendor_field_value'])) {
				$this->error['warning'] = $this->language->get('error_type');
			}

			if (isset($this->request->post['vendor_field_value'])) {
				foreach ($this->request->post['vendor_field_value'] as $vendor_field_value_id => $vendor_field_value) {
					foreach ($vendor_field_value['vendor_field_value_description'] as $language_id => $vendor_field_value_description) {
						if ((utf8_strlen($vendor_field_value_description['name']) < 1) || (utf8_strlen($vendor_field_value_description['name']) > 128)) {
							$this->error['vendor_field_value'][$vendor_field_value_id][$language_id] = $this->language->get('error_Vendor_value');
						}
					}
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'vendor/vendor_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}