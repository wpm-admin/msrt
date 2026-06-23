<?php
class ControllerExtensionModuleSmartMarketingCampaign extends Controller {
	private $error = array();

	public function set_status(){
		
		$sql = "UPDATE " . DB_PREFIX . "sm_subscriber SET status = '1'";
		$this->db->query($sql);
		
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/campaign');

		$this->getList();	
		
	}
	
	public function index() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/campaign');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/campaign');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_campaign->addCampaign($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'] . $url, 'true'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/campaign');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_campaign->editCampaign($this->request->get['campaign_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'] . $url, 'true'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/campaign');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $campaign_id) {
				$this->model_extension_module_smart_marketing_campaign->deleteCampaign($campaign_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'] . $url, 'true'));
		}

		$this->getList();
	}

	public function info() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->load->model('extension/module/smart_marketing/campaign');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/jquery/chart.min.js');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		if (isset($this->request->get['campaign_id'])) {
			$campaign_id = $this->request->get['campaign_id'];
		} else {
			$campaign_id = 0;
		}

		$campaign_info = $this->model_extension_module_smart_marketing_campaign->getCampaign($campaign_id);

		if ($campaign_info) {
			$data['heading_title'] = $this->language->get('heading_title');

			$data['tab_report'] = $this->language->get('tab_report');
			$data['tab_preview'] = $this->language->get('tab_preview');
			$data['tab_html_source'] = $this->language->get('tab_html_source');
			$data['tab_plain_text'] = $this->language->get('tab_plain_text');
			$data['tab_details'] = $this->language->get('tab_details');

			$data['text_recipients'] = $this->language->get('text_recipients');
			$data['text_subject'] = $this->language->get('text_subject');

			$data['text_opened'] = $this->language->get('text_opened');
			$data['text_clicked'] = $this->language->get('text_clicked');
			$data['text_bounced'] = $this->language->get('text_bounced');
			$data['text_unsubscribed'] = $this->language->get('text_unsubscribed');

			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_date_scheduled'] = $this->language->get('text_date_scheduled');
			$data['text_sender_name'] = $this->language->get('text_sender_name');
			$data['text_sender_email'] = $this->language->get('text_sender_email');

			$data['chart_delivery_status'] = $this->language->get('chart_delivery_status');
			$data['chart_engagement_status'] = $this->language->get('chart_engagement_status');
			$data['chart_performance'] = $this->language->get('chart_performance');
			$data['chart_label_sent'] = $this->language->get('chart_label_sent');
			$data['chart_label_delivered'] = $this->language->get('chart_label_delivered');
			$data['chart_label_pending'] = $this->language->get('chart_label_pending');
			$data['chart_label_open'] = $this->language->get('chart_label_open');
			$data['chart_label_click'] = $this->language->get('chart_label_click');
			$data['chart_label_bounce'] = $this->language->get('chart_label_bounce');
			$data['chart_label_unsubscribe_short'] = $this->language->get('chart_label_unsubscribe_short');
			$data['chart_label_unsubscribe'] = $this->language->get('chart_label_unsubscribe');
			$data['chart_label_no_action'] = $this->language->get('chart_label_no_action');
			$data['chart_label_other_action'] = $this->language->get('chart_label_other_action');

			$campaign_stats = $this->model_extension_module_smart_marketing_campaign->getCampaignStats($campaign_id);

			$data['recipient_total'] = $campaign_stats['recipient'];

			$data['name'] = html_entity_decode($campaign_info['name'], ENT_QUOTES, 'UTF-8');
			$data['subject'] = html_entity_decode($campaign_info['subject'], ENT_QUOTES, 'UTF-8');

			$data['sent_total'] = $campaign_stats['sent'];
			$data['delivered_total'] = $campaign_stats['delivered'];
			$data['waiting_total'] = $campaign_stats['waiting'];
			$data['waiting_delivery_total'] = $campaign_stats['waiting_delivery'];
			$data['open_total'] = $campaign_stats['open'];
			$data['click_total'] = $campaign_stats['click'];
			$data['bounce_total'] = $campaign_stats['bounce'];
			$data['unsubscribe_total'] = $campaign_stats['unsubscribe'];
			$data['no_action_total'] = $campaign_stats['no_action'];

			$data['sent_percent'] = $campaign_stats['sent_percent'];
			$data['delivered_percent'] = $campaign_stats['delivered_percent'];
			$data['open_percent'] = $campaign_stats['open_percent'];
			$data['click_percent'] = $campaign_stats['click_percent'];
			$data['bounce_percent'] = $campaign_stats['bounce_percent'];
			$data['unsubscribe_percent'] = $campaign_stats['unsubscribe_percent'];

			$interval = ($this->config->get('module_smart_marketing_chart_performance_interval')) ? $this->config->get('module_smart_marketing_chart_performance_interval') : 24;

			$campaign_performance = $this->model_extension_module_smart_marketing_campaign->getCampaignFirstHoursPerformance($campaign_id, $interval);

			$data['interval'] = $interval;
			$data['performance_chart_data'] = $this->converToChartFormat($campaign_performance);

			$data['html_content'] = html_entity_decode($campaign_info['html_content'], ENT_QUOTES, 'UTF-8');
			$data['plain_content'] = html_entity_decode($campaign_info['plain_content'], ENT_QUOTES, 'UTF-8');

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($campaign_info['date_added']));
			$data['date_scheduled'] = ($campaign_info['date_scheduled'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($campaign_info['date_scheduled'])) : '-';

			$data['sender_name'] = $campaign_info['sender_name'];
			$data['sender_email'] = $campaign_info['sender_email'];
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/campaign_info', $data));
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_subject'])) {
			$filter_subject = $this->request->get['filter_subject'];
		} else {
			$filter_subject = null;
		}

		if (isset($this->request->get['filter_sender_name'])) {
			$filter_sender_name = $this->request->get['filter_sender_name'];
		} else {
			$filter_sender_name = null;
		}

		if (isset($this->request->get['filter_sender_email'])) {
			$filter_sender_email = $this->request->get['filter_sender_email'];
		} else {
			$filter_sender_email = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_scheduled'])) {
			$filter_date_scheduled = $this->request->get['filter_date_scheduled'];
		} else {
			$filter_date_scheduled = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'c.campaign_id';
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

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_name'])) {
			$url .= '&filter_sender_name=' . urlencode(html_entity_decode($this->request->get['filter_sender_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_email'])) {
			$url .= '&filter_sender_email=' . urlencode(html_entity_decode($this->request->get['filter_sender_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_scheduled'])) {
			$url .= '&filter_date_scheduled=' . $this->request->get['filter_date_scheduled'];
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		$data['set_status'] = $this->url->link('extension/module/smart_marketing/campaign/set_status', 'user_token=' . $this->session->data['user_token'], 'true');
		$data['add'] = $this->url->link('extension/module/smart_marketing/campaign/add', 'user_token=' . $this->session->data['user_token'], 'true');
		$data['delete'] = $this->url->link('extension/module/smart_marketing/campaign/delete', 'user_token=' . $this->session->data['user_token'], 'true');

		$data['campaigns'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_subject'           => $filter_subject,
			'filter_sender_name'       => $filter_sender_name,
			'filter_sender_email'      => $filter_sender_email,
			'filter_status'            => $filter_status,
			'filter_date_scheduled'    => $filter_date_scheduled,
			'filter_date_added'        => $filter_date_added,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$campaign_total = $this->model_extension_module_smart_marketing_campaign->getTotalCampaigns($filter_data);

		$results = $this->model_extension_module_smart_marketing_campaign->getCampaigns($filter_data);

		foreach ($results as $result) {
			if ($result['status']) {
				if ($result['sent_total'] == 0) {
					$progress_status = $this->language->get('text_waiting');
					$progress_class = 'primary';
				} elseif ($result['sent_total'] < $result['recipient_total']) {
					$progress_status = $this->language->get('text_progress');
					$progress_class = 'warning';
				} else {
					$progress_status = $this->language->get('text_complete');
					$progress_class = 'success';
				}
			} else {
				$progress_status = false;
			}

			$data['campaigns'][] = array(
				'campaign_id'         => $result['campaign_id'],
				'name'                => $result['name'],
				'subject'             => $result['subject'],
				'recipient_total'     => $result['recipient_total'],
				'sent_total'          => $result['sent_total'],
				'sent_percent'        => number_format($result['sent_total'] / $result['recipient_total'] * 100, 2),
				'status'              => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'progress_status'     => $progress_status,
				'progress_class'      => $progress_class,
				'date_added'          => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_scheduled'      => ($result['date_scheduled'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($result['date_scheduled'])) : '-',
				'send'                => $this->url->link('extension/module/smart_marketing/sender', 'user_token=' . $this->session->data['user_token'] . '&campaign_id=' . $result['campaign_id'], 'true'),
				'edit'                => $this->url->link('extension/module/smart_marketing/campaign/edit', 'user_token=' . $this->session->data['user_token'] . '&campaign_id=' . $result['campaign_id'], 'true'),
				'report'              => $this->url->link('extension/module/smart_marketing/campaign/info', 'user_token=' . $this->session->data['user_token'] . '&campaign_id=' . $result['campaign_id'], 'true')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_subject'] = $this->language->get('column_subject');
		$data['column_recipient'] = $this->language->get('column_recipient');
		$data['column_sent'] = $this->language->get('column_sent');
		$data['column_open'] = $this->language->get('column_open');
		$data['column_click'] = $this->language->get('column_click');
		$data['column_unsubscribe'] = $this->language->get('column_unsubscribe');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_scheduled'] = $this->language->get('column_date_scheduled');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_send'] = $this->language->get('button_send');
		$data['button_report'] = $this->language->get('button_report');

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

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_name'])) {
			$url .= '&filter_sender_name=' . urlencode(html_entity_decode($this->request->get['filter_sender_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_email'])) {
			$url .= '&filter_sender_email=' . urlencode(html_entity_decode($this->request->get['filter_sender_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_scheduled'])) {
			$url .= '&filter_date_scheduled=' . $this->request->get['filter_date_scheduled'];
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

		$data['sort_name'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, 'true');
		$data['sort_status'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.status' . $url, 'true');
		$data['sort_date_scheduled'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_scheduled' . $url, 'true');
		$data['sort_date_added'] = $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=s.date_added' . $url, 'true');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_subject'])) {
			$url .= '&filter_subject=' . urlencode(html_entity_decode($this->request->get['filter_subject'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_name'])) {
			$url .= '&filter_sender_name=' . urlencode(html_entity_decode($this->request->get['filter_sender_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sender_email'])) {
			$url .= '&filter_sender_email=' . urlencode(html_entity_decode($this->request->get['filter_sender_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_scheduled'])) {
			$url .= '&filter_date_scheduled=' . $this->request->get['filter_date_scheduled'];
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

		$pagination = new Pagination();
		$pagination->total = $campaign_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', 'true');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($campaign_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($campaign_total - $this->config->get('config_limit_admin'))) ? $campaign_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $campaign_total, ceil($campaign_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_subject'] = $filter_subject;
		$data['filter_sender_name'] = $filter_sender_name;
		$data['filter_sender_email'] = $filter_sender_email;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_scheduled'] = $filter_date_scheduled;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/campaign_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['campaign_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['legend_recipient'] = $this->language->get('legend_recipient');
		$data['legend_sender'] = $this->language->get('legend_sender');
		$data['legend_subject'] = $this->language->get('legend_subject');
		$data['legend_content'] = $this->language->get('legend_content');
		$data['legend_scheduled'] = $this->language->get('legend_scheduled');

		$data['text_select'] = $this->language->get('text_select');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_subscriber'] = $this->language->get('text_subscriber');
		$data['text_customer'] = $this->language->get('text_customer');
		$data['text_customer_group'] = $this->language->get('text_customer_group');
		$data['text_customer_who_bought_product'] = $this->language->get('text_customer_who_bought_product');
		$data['text_customer_total_spent'] = $this->language->get('text_customer_total_spent');
		$data['text_customer_order_count'] = $this->language->get('text_customer_order_count');
		$data['text_day_ago'] = $this->language->get('text_day_ago');
		$data['text_customer_last_order'] = $this->language->get('text_customer_last_order');
		$data['text_customer_last_login'] = $this->language->get('text_customer_last_login');
		$data['text_subscriber_segment'] = $this->language->get('text_subscriber_segment');
		$data['text_segmentation_match'] = $this->language->get('text_segmentation_match');
		$data['text_any_condition'] = $this->language->get('text_any_condition');
		$data['text_all_condition'] = $this->language->get('text_all_condition');
		$data['text_following_condition'] = $this->language->get('text_following_condition');
		$data['text_template_new'] = $this->language->get('text_template_new');
		$data['text_template_update'] = $this->language->get('text_template_update');

		$data['text_field_firstname'] = $this->language->get('text_field_firstname');
		$data['text_field_lastname'] = $this->language->get('text_field_lastname');
		$data['text_field_email'] = $this->language->get('text_field_email');
		$data['text_field_customer_group'] = $this->language->get('text_field_customer_group');
		$data['text_field_country'] = $this->language->get('text_field_country');
		$data['text_field_source'] = $this->language->get('text_field_source');
		$data['text_field_rating'] = $this->language->get('text_field_rating');
		$data['text_field_date_added'] = $this->language->get('text_field_date_added');
		$data['text_field_date_modified'] = $this->language->get('text_field_date_modified');
		$data['text_field_total_spent'] = $this->language->get('text_field_total_spent');
		$data['text_field_order_count'] = $this->language->get('text_field_order_count');
		$data['text_field_last_order'] = $this->language->get('text_field_last_order');
		$data['text_field_last_login'] = $this->language->get('text_field_last_login');

		$data['text_operator_is'] = $this->language->get('text_operator_is');
		$data['text_operator_is_not'] = $this->language->get('text_operator_is_not');
		$data['text_operator_contain'] = $this->language->get('text_operator_contain');
		$data['text_operator_not_contain'] = $this->language->get('text_operator_not_contain');
		$data['text_operator_start_with'] = $this->language->get('text_operator_start_with');
		$data['text_operator_end_with'] = $this->language->get('text_operator_end_with');
		$data['text_operator_greater_than'] = $this->language->get('text_operator_greater_than');
		$data['text_operator_less_than'] = $this->language->get('text_operator_less_than');
		$data['text_operator_is_before'] = $this->language->get('text_operator_is_before');
		$data['text_operator_is_after'] = $this->language->get('text_operator_is_after');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_segmentation_type'] = $this->language->get('entry_segmentation_type');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_total_spent'] = $this->language->get('entry_total_spent');
		$data['entry_order_count'] = $this->language->get('entry_order_count');
		$data['entry_last_order'] = $this->language->get('entry_last_order');
		$data['entry_last_login'] = $this->language->get('entry_last_login');
		$data['entry_sender_name'] = $this->language->get('entry_sender_name');
		$data['entry_sender_email'] = $this->language->get('entry_sender_email');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_template'] = $this->language->get('entry_template');
		$data['entry_template_update'] = $this->language->get('entry_template_update');
		$data['entry_html_content'] = $this->language->get('entry_html_content');
		$data['entry_plain_content'] = $this->language->get('entry_plain_content');
		$data['entry_scheduled'] = $this->language->get('entry_scheduled');
		$data['entry_date_schedule'] = $this->language->get('entry_date_schedule');
		$data['entry_timezone_based'] = $this->language->get('entry_timezone_based');
		$data['entry_timezone_hour'] = $this->language->get('entry_timezone_hour');

		$data['help_name'] = $this->language->get('help_name');
		$data['help_status'] = $this->language->get('help_status');
		$data['help_sender_name'] = $this->language->get('help_sender_name');
		$data['help_sender_email'] = $this->language->get('help_sender_email');
		$data['help_subject'] = $this->language->get('help_subject');
		$data['help_template'] = $this->language->get('help_template');
		$data['help_template_update'] = $this->language->get('help_template_update');
		$data['help_html_content'] = $this->language->get('help_html_content');
		$data['help_plain_content'] = $this->language->get('help_plain_content');
		$data['help_scheduled'] = $this->language->get('help_scheduled');
		$data['help_date_schedule'] = $this->language->get('help_date_schedule');
		$data['help_timezone_based'] = $this->language->get('help_timezone_based');
		$data['help_timezone_hour'] = $this->language->get('help_timezone_hour');
		$data['help_update_recipient_count'] = $this->language->get('help_update_recipient_count');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_preview'] = $this->language->get('button_preview');
		$data['button_code_full'] = $this->language->get('button_code_full');
		$data['button_edit_design'] = $this->language->get('button_edit_design');
		$data['button_plain_text_test_email'] = $this->language->get('button_plain_text_test_email');
		$data['button_html_test_email'] = $this->language->get('button_html_test_email');
		$data['button_import_template'] = $this->language->get('button_import_template');
		$data['button_save_campaign'] = $this->language->get('button_save_campaign');
		$data['button_schedule_campaign'] = $this->language->get('button_schedule_campaign');
		$data['button_add_condition'] = $this->language->get('button_add_condition');
		$data['button_remove_condition'] = $this->language->get('button_remove_condition');
		$data['button_update_recipient_count'] = $this->language->get('button_update_recipient_count');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['segmentation_type'])) {
			$data['error_segmentation_type'] = $this->error['segmentation_type'];
		} else {
			$data['error_segmentation_type'] = '';
		}

		if (isset($this->error['segmentation_match'])) {
			$data['error_segmentation_match'] = $this->error['segmentation_match'];
		} else {
			$data['error_segmentation_match'] = '';
		}

		if (isset($this->error['segmentation_condition'])) {
			$data['error_segmentation_condition'] = $this->error['segmentation_condition'];
		} else {
			$data['error_segmentation_condition'] = array();
		}

		if (isset($this->error['sender_name'])) {
			$data['error_sender_name'] = $this->error['sender_name'];
		} else {
			$data['error_sender_name'] = '';
		}

		if (isset($this->error['sender_email'])) {
			$data['error_sender_email'] = $this->error['sender_email'];
		} else {
			$data['error_sender_email'] = '';
		}

		if (isset($this->error['subject'])) {
			$data['error_subject'] = $this->error['subject'];
		} else {
			$data['error_subject'] = '';
		}

		if (isset($this->error['html_content'])) {
			$data['error_html_content'] = $this->error['html_content'];
		} else {
			$data['error_html_content'] = '';
		}

		if (isset($this->error['plain_content'])) {
			$data['error_plain_content'] = $this->error['plain_content'];
		} else {
			$data['error_plain_content'] = '';
		}

		if (isset($this->error['date_schedule'])) {
			$data['error_date_schedule'] = $this->error['date_schedule'];
		} else {
			$data['error_date_schedule'] = '';
		}

		if (isset($this->error['timezone_hour'])) {
			$data['error_timezone_hour'] = $this->error['timezone_hour'];
		} else {
			$data['error_timezone_hour'] = '';
		}

		if (isset($this->error['recipient_total'])) {
			$data['error_recipient_total'] = $this->error['recipient_total'];
		} else {
			$data['error_recipient_total'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], 'true')
		);

		if (!isset($this->request->get['campaign_id'])) {
			$data['action'] = $this->url->link('extension/module/smart_marketing/campaign/add', 'user_token=' . $this->session->data['user_token'], 'true');
		} else {
			$data['action'] = $this->url->link('extension/module/smart_marketing/campaign/edit', 'user_token=' . $this->session->data['user_token'] . '&campaign_id=' . $this->request->get['campaign_id'], 'true');
		}

		$data['cancel'] = $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], 'true');

		if (isset($this->request->get['campaign_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$campaign_info = $this->model_extension_module_smart_marketing_campaign->getCampaign($this->request->get['campaign_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($campaign_info['name'])) {
			$data['name'] = $campaign_info['name'];
		} else {
			$data['name'] = sprintf($this->language->get('text_campaign_name_default'), date($this->language->get('datetime_format'), strtotime(date('Y-m-d H:i:s'))));
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($campaign_info['status'])) {
			$data['status'] = $campaign_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['segmentation_type'])) {
			$data['segmentation_type'] = $this->request->post['segmentation_type'];
		} elseif (!empty($campaign_info['segmentation_type'])) {
			$data['segmentation_type'] = $campaign_info['segmentation_type'];
		} else {
			$data['segmentation_type'] = 'subscriber-all';
		}

		if (isset($this->request->post['segmentation_match'])) {
			$data['segmentation_match'] = $this->request->post['segmentation_match'];
		} elseif (!empty($campaign_info['segmentation_match'])) {
			$data['segmentation_match'] = $campaign_info['segmentation_match'];
		} else {
			$data['segmentation_match'] = '';
		}

		if (isset($this->request->post['segmentation_condition'])) {
			$data['segmentation_condition'] = $this->request->post['segmentation_condition'];
		} elseif (!empty($campaign_info['segmentation_condition'])) {
			$data['segmentation_condition'] = unserialize($campaign_info['segmentation_condition']);
		} else {
			$data['segmentation_condition'] = array();
		}

		if (isset($this->request->post['sender_name'])) {
			$data['sender_name'] = $this->request->post['sender_name'];
		} elseif (!empty($campaign_info['sender_name'])) {
			$data['sender_name'] = $campaign_info['sender_name'];
		} else {
			$data['sender_name'] = $this->config->get('module_smart_marketing_sender_name');
		}

		if (isset($this->request->post['sender_email'])) {
			$data['sender_email'] = $this->request->post['sender_email'];
		} elseif (!empty($campaign_info['sender_email'])) {
			$data['sender_email'] = $campaign_info['sender_email'];
		} else {
			$data['sender_email'] = $this->config->get('module_smart_marketing_sender_email');
		}

		if (isset($this->request->post['subject'])) {
			$data['subject'] = $this->request->post['subject'];
		} elseif (!empty($campaign_info['subject'])) {
			$data['subject'] = $campaign_info['subject'];
		} else {
			$data['subject'] = '';
		}

		if (isset($this->request->post['template_id'])) {
			$data['template_id'] = $this->request->post['template_id'];
		} elseif (!empty($campaign_info['template_id'])) {
			$data['template_id'] = $campaign_info['template_id'];
		} else {
			$data['template_id'] = '';
		}

		if (isset($this->request->post['template_update'])) {
			$data['template_update'] = $this->request->post['template_update'];
		} elseif (!empty($campaign_info['template_id'])) {
			$data['template_update'] = 1;
		} else {
			$data['template_update'] = '';
		}

		if (isset($this->request->post['html_content'])) {
			$data['html_content'] = html_entity_decode($this->request->post['html_content'], ENT_QUOTES, 'UTF-8');
		} elseif (!empty($campaign_info['html_content'])) {
			$data['html_content'] = html_entity_decode($campaign_info['html_content'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['html_content'] = '';
		}

		if (isset($this->request->post['plain_content'])) {
			$data['plain_content'] = html_entity_decode($this->request->post['plain_content'], ENT_QUOTES, 'UTF-8');
		} elseif (!empty($campaign_info['plain_content'])) {
			$data['plain_content'] = html_entity_decode($campaign_info['plain_content'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['plain_content'] = '';
		}

		if (isset($this->request->post['scheduled'])) {
			$data['scheduled'] = $this->request->post['scheduled'];
		} elseif (!empty($campaign_info) && isset($campaign_info['date_scheduled']) && $campaign_info['date_scheduled'] != '0000-00-00 00:00:00') {
			$data['scheduled'] = 1;
		} else {
			$data['scheduled'] = '';
		}

		if (isset($this->request->post['date_schedule'])) {
			$data['date_schedule'] = $this->request->post['date_schedule'];
		} elseif (!empty($campaign_info) && isset($campaign_info['date_schedule']) && $campaign_info['date_scheduled'] != '0000-00-00 00:00:00') {
			$data['date_schedule'] = $campaign_info['date_schedule'];
		} else {
			$data['date_schedule'] = date('Y-m-d H:i:s');
		}

		if (isset($this->request->post['timezone_based'])) {
			$data['timezone_based'] = $this->request->post['timezone_based'];
		} elseif (!empty($campaign_info) && isset($campaign_info['timezone_hour']) && $campaign_info['timezone_hour'] != '00:00:00') {
			$data['timezone_based'] = 1;
		} else {
			$data['timezone_based'] = '';
		}

		if (isset($this->request->post['timezone_hour'])) {
			$data['timezone_hour'] = $this->request->post['timezone_hour'];
		} elseif (!empty($campaign_info) && isset($campaign_info['timezone_hour']) && $campaign_info['timezone_hour'] != '00:00:00') {
			$data['timezone_hour'] = $campaign_info['timezone_hour'];
		} else {
			$data['timezone_hour'] = date('H:i:s');
		}

		$this->load->model('extension/module/smart_marketing/template');

		$data['templates'] = $this->model_extension_module_smart_marketing_template->getTemplates();

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		// case segmentation = products => get only selected products from full products list
		$data['products'] = array();

		$product_segmentation_types = array('customer_who_bought_product');

		$search_segment = 'customer_who_bought_product';

		if (in_array($search_segment, $product_segmentation_types)) {
			if (isset($data['segmentation_condition'][$search_segment])) {
				$this->load->model('extension/module/smart_marketing/product');

				$products = $this->model_extension_module_smart_marketing_product->getSegmentationProducts($data['segmentation_condition'][$search_segment]);

				if ($products) {
					foreach ($products as $product) {
						$data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => strip_tags(html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'))
						);
					}
				}
			}
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['sources'] = array();

		$this->load->model('extension/module/smart_marketing/subscriber');

		$sources = $this->model_extension_module_smart_marketing_subscriber->getSources();

		if ($sources) {
			foreach ($sources as $source) {
				$data['sources'][] = array(
					'code' => $source['source'],
					'name' => $this->language->get('text_source_' . str_replace('-','_', $source['source']))
				);
			}
		}

		$data['currency_symbol_left'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		$data['currency_symbol_right'] = $this->currency->getSymbolRight($this->config->get('config_currency'));

		$data['customer_activity_logged'] = $this->config->get('config_customer_activity');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/campaign_form', $data));
	}

	public function getTotalExpectedRecipients() {
		$this->load->language('extension/module/smart_marketing/campaign');

		$this->load->model('extension/module/smart_marketing/campaign');

		$json = array();

		if (!$json) {
			if (utf8_strlen($this->request->post['segmentation_type']) < 1) {
				$json['error'] = $this->language->get('error_segmentation_type');
			}

			if ($this->request->post['segmentation_type'] == 'customer_group') {
				if (!isset($this->request->post['segmentation_condition']['customer_group'])) {
					$json['error'] = $this->language->get('error_segmentation_customer_group');
				}
			}

			if ($this->request->post['segmentation_type'] == 'customer_who_bought_product') {
				if (!isset($this->request->post['segmentation_condition']['customer_who_bought_product'])) {
					$json['error'] = $this->language->get('error_segmentation_customer_who_bought_product');
				} else {
					$selected_products = $this->model_extension_module_smart_marketing_campaign->getSegmentationConditionValues($this->request->post['segmentation_condition']['customer_who_bought_product'], 'product_id');

					if (!$selected_products) {
						$json['error'] = $this->language->get('error_segmentation_customer_who_bought_product');
					}
				}
			}

			$custom_segmentation_types = $this->model_extension_module_smart_marketing_campaign->getCustomConditionKeys('customer');

			foreach ($custom_segmentation_types as $custom_segmentation_type) {
				if ($this->request->post['segmentation_type'] == $custom_segmentation_type) {
					if (isset($this->request->post['segmentation_condition'][$custom_segmentation_type][0])) {
						if (utf8_strlen($this->request->post['segmentation_condition'][$custom_segmentation_type][0]['operator']) < 1) {
							$json['error'] = $this->language->get('error_segmentation_general_operator');
						}

						if (!is_numeric($this->request->post['segmentation_condition'][$custom_segmentation_type][0]['value'])) {
							$json['error'] = $this->language->get('error_segmentation_general_value');
						}
					}
				}
			}

			if ($this->request->post['segmentation_type'] == 'advanced_segment') {
				if (utf8_strlen($this->request->post['segmentation_match']) < 1) {
					$json['error'] = $this->language->get('error_segmentation_match');
				}

				if (!isset($this->request->post['segmentation_condition']['advanced_segment'])) {
					$json['error'] = $this->language->get('error_segmentation_advanced_segment_empty');
				} else {

					$custom_condition_keys = $this->model_extension_module_smart_marketing_campaign->getCustomConditionKeys();

					foreach ($this->request->post['segmentation_condition']['advanced_segment'] as $segment_condition) {
						if (utf8_strlen($segment_condition['key']) < 1) {
							$json['error'] = $this->language->get('error_segmentation_advanced_segment_key');

							break;
						}

						if (utf8_strlen($segment_condition['operator']) < 1) {
							$json['error'] = $this->language->get('error_segmentation_advanced_segment_operator');

							break;
						}

						if (utf8_strlen($segment_condition['value']) < 1) {
							$json['error'] = $this->language->get('error_segmentation_advanced_segment_value');

							break;
						}

						if (in_array($segment_condition['key'], $custom_condition_keys)) {
							if (!is_numeric($segment_condition['value'])) {
								$json['error'] = $this->language->get('error_segmentation_advanced_segment_value');

								break;
							}
						}
					}
				}
			}
		}

		if (!$json) {
			$this->load->model('extension/module/smart_marketing/campaign');

			$recipient_total = $this->model_extension_module_smart_marketing_campaign->getTotalRecipientsConditionsBased($this->request->post);

			$json['recipient_total'] = sprintf($this->language->get('text_recipient_expected_total'), $recipient_total);
			$json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/campaign')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['name']) < 1) {
			$this->error['name'] = $this->language->get('error_name');
		}

		// validate segmentation (if required)
		if (utf8_strlen($this->request->post['segmentation_type']) < 1) {
			$this->error['segmentation_type'] = $this->language->get('error_segmentation_type');
		}

		if ($this->request->post['segmentation_type'] == 'customer_group') {
			if (!isset($this->request->post['segmentation_condition']['customer_group'])) {
				$this->error['segmentation_condition']['customer_group'] = $this->language->get('error_segmentation_customer_group');
			}
		}

		if ($this->request->post['segmentation_type'] == 'customer_who_bought_product') {
			if (!isset($this->request->post['segmentation_condition']['customer_who_bought_product'])) {
				$this->error['segmentation_condition']['customer_who_bought_product'] = $this->language->get('error_segmentation_customer_who_bought_product');
			} else {
				$selected_products = $this->model_extension_module_smart_marketing_campaign->getSegmentationConditionValues($this->request->post['segmentation_condition']['customer_who_bought_product'], 'product_id');

				if (!$selected_products) {
					$this->error['segmentation_condition']['customer_who_bought_product'] = $this->language->get('error_segmentation_customer_who_bought_product');
				}
			}
		}

		$custom_segmentation_types = $this->model_extension_module_smart_marketing_campaign->getCustomConditionKeys('customer');

		foreach ($custom_segmentation_types as $custom_segmentation_type) {
			if ($this->request->post['segmentation_type'] == $custom_segmentation_type) {
				if (isset($this->request->post['segmentation_condition'][$custom_segmentation_type][0])) {
					if (utf8_strlen($this->request->post['segmentation_condition'][$custom_segmentation_type][0]['operator']) < 1) {
						$this->error['segmentation_condition'][$custom_segmentation_type] = $this->language->get('error_segmentation_general_operator');
					}

					if (!is_numeric($this->request->post['segmentation_condition'][$custom_segmentation_type][0]['value'])) {
						$this->error['segmentation_condition'][$custom_segmentation_type] = $this->language->get('error_segmentation_general_value');
					}
				}
			}
		}

		if ($this->request->post['segmentation_type'] == 'advanced_segment') {
			if (utf8_strlen($this->request->post['segmentation_match']) < 1) {
				$this->error['segmentation_match'] = $this->language->get('error_segmentation_match');
			}

			if (!isset($this->request->post['segmentation_condition']['advanced_segment'])) {
				$this->error['segmentation_condition']['advanced_segment'] = $this->language->get('error_segmentation_advanced_segment_empty');
			} else {

				$custom_condition_keys = $this->model_extension_module_smart_marketing_campaign->getCustomConditionKeys();

				foreach ($this->request->post['segmentation_condition']['advanced_segment'] as $segment_condition) {
					if (utf8_strlen($segment_condition['key']) < 1) {
						$this->error['segmentation_condition']['advanced_segment'] = $this->language->get('error_segmentation_advanced_segment_key');

						break;
					}

					if (utf8_strlen($segment_condition['operator']) < 1) {
						$this->error['segmentation_condition']['advanced_segment'] = $this->language->get('error_segmentation_advanced_segment_operator');

						break;
					}

					if (utf8_strlen($segment_condition['value']) < 1) {
						$this->error['segmentation_condition']['advanced_segment'] = $this->language->get('error_segmentation_advanced_segment_value');

						break;
					}

					if (in_array($segment_condition['key'], $custom_condition_keys)) {
						if (!is_numeric($segment_condition['value'])) {
							$this->error['segmentation_condition']['advanced_segment'] = $this->language->get('error_segmentation_advanced_segment_value');

							break;
						}
					}
				}
			}
		}

		if (utf8_strlen($this->request->post['sender_name']) < 1) {
			$this->error['sender_name'] = $this->language->get('error_sender_name');
		}

		if ((utf8_strlen($this->request->post['sender_email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['sender_email'])) {
			$this->error['sender_email'] = $this->language->get('error_sender_email');
		}

		if (utf8_strlen($this->request->post['subject']) < 1) {
			$this->error['subject'] = $this->language->get('error_subject');
		}

		if (utf8_strlen($this->request->post['html_content']) < 20) {
			$this->error['html_content'] = $this->language->get('error_html_content');
		}

		if (utf8_strlen($this->request->post['plain_content']) < 1) {
			$this->error['plain_content'] = $this->language->get('error_plain_content');
		}

		if ($this->request->post['scheduled']) {
			if (utf8_strlen($this->request->post['date_schedule']) < 1) {
				$this->error['date_schedule'] = $this->language->get('error_date_schedule');
			}

			$this->load->model('extension/module/smart_marketing/timer');

			$remaining_minutes = $this->model_extension_module_smart_marketing_timer->getRemainingMinutes($this->request->post['date_schedule']);

			if ($remaining_minutes < 20) {
				$this->error['date_schedule'] = $this->language->get('error_date_schedule_time_remaining');
			}
		}

		if ($this->request->post['timezone_based']) {
			if (utf8_strlen($this->request->post['timezone_hour']) < 1) {
				$this->error['timezone_hour'] = $this->language->get('error_timezone_hour');
			}
		}

		// check if segmentation conditions produce any results (if no regular erorrs until this step is reached)
		if (!$this->error) {
			$recipient_total = $this->model_extension_module_smart_marketing_campaign->getTotalRecipientsConditionsBased($this->request->post);

			if ($recipient_total < 1) {
				$this->error['recipient_total'] = $this->language->get('error_recipient_total');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/campaign')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function converToChartFormat($performance_data) {
		$chart_data = array();

		if ($performance_data) {
			$labels = array();
			$open_data = array();
			$click_data = array();
			$bounce_data = array();
			$unsubscribe_data = array();

			foreach ($performance_data as $performance) {
				$labels[] = $performance['date'] . ' ' . (($performance['hour'] < 10) ? '0' : '') . $performance['hour'] . ':00:00';
				$open_data[] = $performance['open'];
				$click_data[] = $performance['click'];
				$bounce_data[] = $performance['bounce'];
				$unsubscribe_data[] = $performance['unsubscribe'];
			}

			$chart_data = array(
				'labels' => $labels,
				'datasets' => array(
					'open' 		  => $open_data,
					'click' 		  => $click_data,
					'bounce' 	  => $bounce_data,
					'unsubscribe' => $unsubscribe_data
				)
			);
		}

		return $chart_data;
	}
}
