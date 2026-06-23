<?php
class ControllerExtensionModuleSmartMarketingsubscriber extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_subscriber->addSubscriber($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}

			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_source'])) {
				$url .= '&filter_source=' . $this->request->get['filter_source'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_subscriber->editSubscriber($this->request->get['subscriber_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}

			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_source'])) {
				$url .= '&filter_source=' . $this->request->get['filter_source'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function import() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateFormImport()) {
			$total_import = $this->model_extension_module_smart_marketing_subscriber->customImport($this->request->post);

			$this->session->data['success'] = ($total_import > 0) ? sprintf($this->language->get('text_success_import'), $total_import) : $this->language->get('text_no_import');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}

			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_source'])) {
				$url .= '&filter_source=' . $this->request->get['filter_source'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getImportForm();
	}

	public function unsubscribe() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateFormUnsubscribe()) {
			$total_unsubscribe = $this->model_extension_module_smart_marketing_subscriber->customUnsubscribe($this->request->post);

			$this->session->data['success'] = ($total_unsubscribe > 0) ? sprintf($this->language->get('text_success_unsubscribe'), $total_unsubscribe) : $this->language->get('text_no_unsubscribe');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}

			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_source'])) {
				$url .= '&filter_source=' . $this->request->get['filter_source'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getUnsubscribeForm();
	}

	public function delete() {
		$this->load->language('extension/module/smart_marketing/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/subscriber');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $subscriber_id) {
				$this->model_extension_module_smart_marketing_subscriber->deleteSubscriber($subscriber_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_customer_id'])) {
				$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
			}

			if (isset($this->request->get['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
			}

			if (isset($this->request->get['filter_country_id'])) {
				$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
			}

			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_source'])) {
				$url .= '&filter_source=' . $this->request->get['filter_source'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		} else {
			$filter_country_id = null;
		}

		if (isset($this->request->get['filter_rating'])) {
			$filter_rating = $this->request->get['filter_rating'];
		} else {
			$filter_rating = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_source'])) {
			$filter_source = $this->request->get['filter_source'];
		} else {
			$filter_source = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'subscriber_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
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

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/module/smart_marketing/subscriber/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('extension/module/smart_marketing/subscriber/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['unsubscribe'] = $this->url->link('extension/module/smart_marketing/subscriber/unsubscribe', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/module/smart_marketing/subscriber/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['subscribers'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_customer_id' 		=> $filter_customer_id,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_country_id'        => $filter_country_id,
			'filter_status'            => $filter_status,
			'filter_rating'            => $filter_rating,
			'filter_source'            => $filter_source,
			'filter_date_added'        => $filter_date_added,
			'filter_date_modified'     => $filter_date_modified,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$subscriber_total = $this->model_extension_module_smart_marketing_subscriber->getTotalSubscribers($filter_data);

		$results = $this->model_extension_module_smart_marketing_subscriber->getSubscribers($filter_data);

		foreach ($results as $result) {
			$data['subscribers'][] = array(
				'subscriber_id'  => $result['subscriber_id'],
				'name'           => $result['name'],
				'email'          => $result['email'],
				'customer_id'    => ($result['customer_id']) ? $result['customer_id'] : '-',
				'customer_group' => ($result['customer_group']) ? $result['customer_group'] : '-',
				'country'        => ($result['country']) ? $result['country'] : '',
				'iso_code_2'     => ($result['iso_code_2']) ? $result['iso_code_2'] : '',
				'utc'     		  => ($result['utc']) ? $result['utc'] : '',
				'rating'         => ($result['rating']) ? $result['rating'] : false,
				'status'         => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'note'           => (!$result['status'] && $result['note']) ? $result['note'] : '',
				'source'         => $this->language->get('text_source_' . str_replace('-','_', $result['source'])),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'  => ($result['date_modified'] != '0000-00-00 00:00:00') ? date($this->language->get('date_format_short'), strtotime($result['date_modified'])) : '-',
				'edit'           => $this->url->link('extension/module/smart_marketing/subscriber/edit', 'user_token=' . $this->session->data['user_token'] . '&subscriber_id=' . $result['subscriber_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_customer_id'] = $this->language->get('column_customer_id');
		$data['column_customer_group'] = $this->language->get('column_customer_group');
		$data['column_country'] = $this->language->get('column_country');
		$data['column_rating'] = $this->language->get('column_rating');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_source'] = $this->language->get('column_source');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_customer_id'] = $this->language->get('entry_customer_id');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_source'] = $this->language->get('entry_source');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['button_sync'] = $this->language->get('button_sync');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_import'] = $this->language->get('button_import');
		$data['button_unsubscribe'] = $this->language->get('button_unsubscribe');
		$data['button_filter'] = $this->language->get('button_filter');

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

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_email'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.email' . $url, true);
		$data['sort_customer_id'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.customer_id' . $url, true);
		$data['sort_customer_group'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=customer_group' . $url, true);
		$data['sort_country'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.country_id' . $url, true);
		$data['sort_rating'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.rating' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.status' . $url, true);
		$data['sort_source'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.source' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $subscriber_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($subscriber_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($subscriber_total - $this->config->get('config_limit_admin'))) ? $subscriber_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $subscriber_total, ceil($subscriber_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_customer_id'] = $filter_customer_id;
		$data['filter_customer_group_id'] = $filter_customer_group_id;
		$data['filter_country_id'] = $filter_country_id;
		$data['filter_status'] = $filter_status;
		$data['filter_rating'] = $filter_rating;
		$data['filter_source'] = $filter_source;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['sources'] = array();

		$sources = $this->model_extension_module_smart_marketing_subscriber->getSources();

		if ($sources) {
			foreach ($sources as $source) {
				$data['sources'][] = array(
					'code' => $source['source'],
					'name' => $this->language->get('text_source_' . str_replace('-','_', $source['source']))
				);
			}
		}

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/subscriber_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['tab_general'] = $this->language->get('tab_general');

		$data['text_form'] = !isset($this->request->get['subscriber_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_customer_id'] = $this->language->get('entry_customer_id');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_source'] = $this->language->get('entry_source');
		$data['entry_note'] = $this->language->get('entry_note');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['subscriber_id'])) {
			$data['subscriber_id'] = $this->request->get['subscriber_id'];
		} else {
			$data['subscriber_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_country_id'])) {
			$url .= '&filter_country_id=' . $this->request->get['filter_country_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['subscriber_id'])) {
			$data['action'] = $this->url->link('extension/module/smart_marketing/subscriber/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/smart_marketing/subscriber/edit', 'user_token=' . $this->session->data['user_token'] . '&subscriber_id=' . $this->request->get['subscriber_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['subscriber_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$subscriber_info = $this->model_extension_module_smart_marketing_subscriber->getSubscriber($this->request->get['subscriber_id']);
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($subscriber_info)) {
			$data['firstname'] = $subscriber_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($subscriber_info)) {
			$data['lastname'] = $subscriber_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($subscriber_info)) {
			$data['email'] = $subscriber_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($subscriber_info)) {
			$data['customer_id'] = $subscriber_info['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($subscriber_info)) {
			$data['customer_group_id'] = $subscriber_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($subscriber_info)) {
			$data['country_id'] = $subscriber_info['country_id'];
		} else {
			$data['country_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($subscriber_info)) {
			$data['status'] = $subscriber_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['source'])) {
			$data['source'] = $this->request->post['source'];
		} elseif (!empty($subscriber_info)) {
			$data['source'] = $subscriber_info['source'];
		} else {
			$data['source'] = 'manual';
		}

		if (isset($this->request->post['note'])) {
			$data['note'] = $this->request->post['note'];
		} elseif (!empty($subscriber_info)) {
			$data['note'] = $subscriber_info['note'];
		} else {
			$data['note'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/subscriber_form', $data));
	}

	protected function getImportForm() {
		$data['heading_title_import'] = $this->language->get('heading_title_import');

		$data['text_form'] = $this->language->get('text_import');

		$data['entry_import_list'] = $this->language->get('entry_import_list');

		$data['help_import_list'] = $this->language->get('help_import_list');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_import'] = $this->language->get('button_import');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['import_list'])) {
			$data['error_import_list'] = $this->error['import_list'];
		} else {
			$data['error_import_list'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('extension/module/smart_marketing/subscriber/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['cancel'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->post['import_list'])) {
			$data['import_list'] = $this->request->post['import_list'];
		} else {
			$data['import_list'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/subscriber_import', $data));
	}

	protected function getUnsubscribeForm() {
		$data['heading_title_unsubscribe'] = $this->language->get('heading_title_unsubscribe');

		$data['text_form'] = $this->language->get('text_unsubscribe');

		$data['entry_unsubscribe_list'] = $this->language->get('entry_unsubscribe_list');

		$data['help_unsubscribe_list'] = $this->language->get('help_unsubscribe_list');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_unsubscribe'] = $this->language->get('button_unsubscribe');

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['unsubscribe_list'])) {
			$data['error_unsubscribe_list'] = $this->error['unsubscribe_list'];
		} else {
			$data['error_unsubscribe_list'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . $this->request->get['filter_source'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('extension/module/smart_marketing/subscriber/unsubscribe', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['cancel'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->post['unsubscribe_list'])) {
			$data['unsubscribe_list'] = $this->request->post['unsubscribe_list'];
		} else {
			$data['unsubscribe_list'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/subscriber_unsubscribe', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$subscriber_info = $this->model_extension_module_smart_marketing_subscriber->getSubscriberByEmail($this->request->post['email']);

		if (!isset($this->request->get['subscriber_id'])) {
			if ($subscriber_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($subscriber_info && ($this->request->get['subscriber_id'] != $subscriber_info['subscriber_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateFormImport() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['import_list']) < 1) {
			$this->error['warning'] = $this->language->get('error_import_list');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateFormUnsubscribe() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['unsubscribe_list']) < 1) {
			$this->error['warning'] = $this->language->get('error_unsubscribe_list');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function sync() {
		$this->load->language('extension/module/smart_marketing/subscriber');
		$this->load->language('extension/module/smart_marketing/api');

		$json = array();

		$this->load->model('extension/module/smart_marketing/api');

		$sm_response = $this->model_extension_module_smart_marketing_api->syncSubscribers();

		if (isset($sm_response['error'])) {
			$json['error'] = vsprintf($this->language->get($sm_response['error']['code']), $sm_response['error']['args']);
		}

		if (isset($sm_response['response'])) {
			$sm_response_ok = $sm_response['response'];

			if (isset($sm_response_ok['success'])) {
				$this->session->data['success'] = $this->language->get('text_success_sync');

				$json['success'] = $this->language->get('text_success_sync');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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

			$this->load->model('extension/module/smart_marketing/subscriber');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'start'        => 0,
				'limit'        => 5
			);

			$results = $this->model_extension_module_smart_marketing_subscriber->getSubscribers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'subscriber_id'     => $result['subscriber_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email']
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
